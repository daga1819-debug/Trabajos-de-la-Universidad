<?php

require_once __DIR__ . '/../models/Sistema.php';
require_once __DIR__ . '/../services/ImagenService.php';

/*
    Controlador principal de los módulos privados del sistema
    Verifica permisos, procesa formularios y prepara los datos de cada vista
 */
class SistemaController
{
    private array $usuario;
    private bool $esAdministrador;

    public function __construct()
    {
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php');
            exit;
        }

        $this->usuario = $_SESSION['usuario'];
        $this->esAdministrador = $this->usuario['rol'] === 'administrador';
    }

    public function ejecutar(): array
    {
        $modulo = $this->obtenerModulo();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarFormulario($modulo);
        }

        $buscar = trim((string) ($_GET['buscar'] ?? ''));
        $destinos = Sistema::listar('destinos', $modulo === 'destinos' ? $buscar : '');
        $hoteles = Sistema::listar('hoteles', $modulo === 'hoteles' ? $buscar : '');
        $actividades = Sistema::listar(
            'actividades',
            $modulo === 'actividades' ? $buscar : ''
        );

        $edicion = null;

        if (
            isset($_GET['editar'])
            && in_array($modulo, ['destinos', 'hoteles', 'actividades'], true)
        ) {
            $edicion = Sistema::buscar($modulo, (int) $_GET['editar']);
        }

        // El perfil se consulta nuevamente para mostrar teléfono y fotografía actualizados
        if ($modulo === 'perfil') {
            $perfil = Sistema::perfilUsuario((int) $this->usuario['id_usuario']);

            if ($perfil !== null) {
                $this->usuario = array_merge($this->usuario, $perfil);
            }
        }

        return [
            'usuario' => $this->usuario,
            'esAdministrador' => $this->esAdministrador,
            'modulo' => $modulo,
            'buscar' => $buscar,
            'destinos' => $destinos,
            'hoteles' => $hoteles,
            'actividades' => $actividades,
            'edicion' => $edicion,
            'reservaciones' => $modulo === 'reservaciones'
                ? Sistema::reservaciones(
                    $this->esAdministrador
                        ? null
                        : (int) $this->usuario['id_usuario']
                )
                : [],
            'favoritos' => $modulo === 'favoritos'
                ? Sistema::favoritos((int) $this->usuario['id_usuario'])
                : [],
            'usuarios' => $modulo === 'usuarios'
                ? Sistema::listar('usuarios', $buscar)
                : [],
            'reportes' => $modulo === 'reportes'
                ? Sistema::reportes()
                : [],
            'mensaje' => $this->leerMensaje('mensaje'),
            'error' => $this->leerMensaje('error_sistema'),
        ];
    }

    private function obtenerModulo(): string
    {
        $permitidos = [
            'destinos',
            'hoteles',
            'actividades',
            'reservaciones',
            'perfil',
            'favoritos',
            'usuarios',
            'reportes',
        ];

        $modulo = (string) ($_GET['modulo'] ?? 'destinos');

        if (!in_array($modulo, $permitidos, true)) {
            return 'destinos';
        }

        if (
            in_array($modulo, ['usuarios', 'reportes'], true)
            && !$this->esAdministrador
        ) {
            return 'destinos';
        }

        return $modulo;
    }

    private function procesarFormulario(string $modulo): never
    {
        try {
            $this->validarCsrf();
            $accion = (string) ($_POST['accion'] ?? '');

            if ($accion === 'perfil') {
                $this->actualizarPerfil();
            } elseif ($accion === 'favorito') {
                Sistema::alternarFavorito(
                    (int) $this->usuario['id_usuario'],
                    (int) ($_POST['id_destino'] ?? 0)
                );
            } elseif ($accion === 'reservar') {
                $this->crearReservacion();
            } else {
                $this->procesarAccionAdministrativa($accion, $modulo);
            }

            $this->redirigir($modulo, 'La operación se realizó correctamente.');
        } catch (Throwable $excepcion) {
            error_log(
                '[' . date('Y-m-d H:i:s') . '] '
                    . $excepcion->getMessage()
                    . PHP_EOL,
                3,
                __DIR__ . '/../../storage/logs/errores.log'
            );

            if ($excepcion instanceof PDOException) {
                $mensaje = 'No fue posible completar la operación porque existen datos relacionados.';
            } elseif (
                $excepcion instanceof RuntimeException
                || $excepcion instanceof DomainException
            ) {
                $mensaje = $excepcion->getMessage();
            } else {
                $mensaje = 'No fue posible completar la operación.';
            }

            $this->redirigir($modulo, $mensaje, true);
        }
    }

    /*
        Ejecuta únicamente operaciones reservadas al administrador y registra en la bitácora el tipo real de acción realizada
     */
    private function procesarAccionAdministrativa(
        string $accion,
        string $modulo
    ): void {
        if (!$this->esAdministrador) {
            throw new RuntimeException(
                'No tiene autorización para realizar esta operación.'
            );
        }

        $accionBitacora = 'Actualizar';
        $descripcion = 'Mantenimiento realizado desde el panel administrativo.';

        if ($accion === 'guardar_destino') {
            $this->guardarDestino();
            $accionBitacora = 'Guardar';
        } elseif ($accion === 'guardar_hotel') {
            $this->guardarHotel();
            $accionBitacora = 'Guardar';
        } elseif ($accion === 'guardar_actividad') {
            $this->guardarActividad();
            $accionBitacora = 'Guardar';
        } elseif ($accion === 'eliminar') {
            $entidad = (string) ($_POST['entidad'] ?? '');

            if (!in_array($entidad, ['destinos', 'hoteles', 'actividades'], true)) {
                throw new RuntimeException('El registro indicado no se puede eliminar.');
            }

            Sistema::eliminar($entidad, (int) ($_POST['id'] ?? 0));
            $accionBitacora = 'Eliminar';
            $descripcion = 'Registro eliminado desde el panel administrativo.';
        } elseif ($accion === 'usuario') {
            $idUsuario = (int) ($_POST['id_usuario'] ?? 0);
            $estado = (string) ($_POST['estado'] ?? '');
            $rol = (string) ($_POST['rol'] ?? '');

            if (
                $idUsuario === (int) $this->usuario['id_usuario']
                && ($estado !== 'activo' || $rol !== 'administrador')
            ) {
                throw new RuntimeException(
                    'No puede desactivar su propia cuenta ni retirar su rol de administrador.'
                );
            }

            if (!Sistema::cambiarEstadoUsuario($idUsuario, $estado, $rol)) {
                throw new RuntimeException('Los datos del usuario no son válidos.');
            }

            $descripcion = 'Rol o estado de usuario actualizado.';
        } elseif ($accion === 'estado_reserva') {
            if (!Sistema::cambiarEstadoReservacion(
                (int) ($_POST['id_reservacion'] ?? 0),
                (string) ($_POST['estado'] ?? '')
            )) {
                throw new RuntimeException('El estado de la reservación no es válido.');
            }

            $descripcion = 'Estado de reservación actualizado.';
        } else {
            throw new RuntimeException('La operación solicitada no es válida.');
        }

        Sistema::bitacora(
            (int) $this->usuario['id_usuario'],
            $accionBitacora,
            ucfirst($modulo),
            $descripcion
        );
    }

    private function guardarDestino(): void
    {
        $imagen = ImagenService::guardar(
            $_FILES['imagen'] ?? [],
            'destinos',
            'destino'
        );

        $_POST['imagen_principal'] = $imagen
            ?? (string) ($_POST['imagen_actual'] ?? '');

        Sistema::guardarDestino($_POST, (int) ($_POST['id'] ?? 0));
    }

    private function guardarHotel(): void
    {
        $imagen = ImagenService::guardar(
            $_FILES['imagen_archivo'] ?? [],
            'hoteles',
            'hotel'
        );

        $_POST['imagen'] = $imagen
            ?? (string) ($_POST['imagen_actual'] ?? '');

        Sistema::guardarHotel($_POST, (int) ($_POST['id'] ?? 0));
    }


    /*
        Guarda una actividad y procesa su imagen con el mismo servicio utilizado por destinos y hoteles. Si no se selecciona una imagen nueva, conserva la actual
     */
    private function guardarActividad(): void
    {
        $imagen = ImagenService::guardar(
            $_FILES['imagen_archivo'] ?? [],
            'actividades',
            'actividad'
        );

        $_POST['imagen'] = $imagen
            ?? (string) ($_POST['imagen_actual'] ?? '');

        Sistema::guardarActividad($_POST, (int) ($_POST['id'] ?? 0));
    }

    /*
        Valida las fechas mínimas antes de delegar el cálculo y la transacción al modelo
     */
    private function crearReservacion(): void
    {
        $fechaInicio = (string) ($_POST['fecha_inicio'] ?? '');
        $fechaFin = (string) ($_POST['fecha_fin'] ?? '');
        $idHotel = (int) ($_POST['id_hotel'] ?? 0);

        if (
            $idHotel <= 0
            || $fechaInicio === ''
            || $fechaFin === ''
            || $fechaInicio < date('Y-m-d')
            || $fechaFin <= $fechaInicio
        ) {
            throw new RuntimeException(
                'Los datos de la reservación no son válidos.'
            );
        }

        Sistema::crearReservacion(
            (int) $this->usuario['id_usuario'],
            $_POST
        );
    }

    /*
        Actualiza los datos personales y mantiene sincronizada la sesión
     */
    private function actualizarPerfil(): void
    {
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $correo = strtolower(trim((string) ($_POST['correo'] ?? '')));
        $contrasena = (string) ($_POST['contrasena'] ?? '');

        if ($nombre === '') {
            throw new RuntimeException('El nombre es obligatorio.');
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('El correo no es válido.');
        }

        if ($contrasena !== '' && (strlen($contrasena) < 8 || strlen($contrasena) > 72)) {
            throw new RuntimeException(
                'La contraseña debe tener entre 8 y 72 caracteres.'
            );
        }

        if (Sistema::correoEnUso(
            $correo,
            (int) $this->usuario['id_usuario']
        )) {
            throw new RuntimeException(
                'Ya existe otra cuenta registrada con ese correo.'
            );
        }

        $fotografia = ImagenService::guardar(
            $_FILES['fotografia'] ?? [],
            'perfiles',
            'perfil_' . (int) $this->usuario['id_usuario']
        );

        if ($fotografia !== null) {
            $_POST['fotografia'] = $fotografia;
        }

        Sistema::actualizarPerfil(
            (int) $this->usuario['id_usuario'],
            $_POST
        );

        $_SESSION['usuario']['nombre'] = $nombre;
        $_SESSION['usuario']['correo'] = $correo;
        $_SESSION['usuario']['telefono'] = trim((string) ($_POST['telefono'] ?? ''));

        if ($fotografia !== null) {
            $_SESSION['usuario']['fotografia'] = $fotografia;
        }

        $this->usuario = $_SESSION['usuario'];
    }

    private function validarCsrf(): void
    {
        $tokenSesion = (string) ($_SESSION['csrf_token'] ?? '');
        $tokenFormulario = (string) ($_POST['csrf_token'] ?? '');

        if (
            $tokenSesion === ''
            || $tokenFormulario === ''
            || !hash_equals($tokenSesion, $tokenFormulario)
        ) {
            throw new RuntimeException('La solicitud no es válida.');
        }
    }

    private function redirigir(
        string $modulo,
        string $mensaje,
        bool $esError = false
    ): never {
        $_SESSION[$esError ? 'error_sistema' : 'mensaje'] = $mensaje;

        header('Location: sistema.php?modulo=' . urlencode($modulo));
        exit;
    }

    private function leerMensaje(string $nombre): string
    {
        $mensaje = (string) ($_SESSION[$nombre] ?? '');
        unset($_SESSION[$nombre]);

        return $mensaje;
    }
}
