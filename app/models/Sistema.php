<?php

require_once __DIR__ . '/../../config/database.php';

/*
    Modelo central de los módulos turísticos
    Agrupa consultas de catálogo, reservaciones, perfil, favoritos y reportes
 */
class Sistema
{
    private const ENTIDADES = [
        'destinos' => 'id_destino',
        'hoteles' => 'id_hotel',
        'actividades' => 'id_actividad',
        'usuarios' => 'id_usuario',
    ];

    public static function listar(string $entidad, string $buscar = ''): array
    {
        self::validarEntidad($entidad);
        $conexion = Database::conectar();
        $sql = "SELECT * FROM {$entidad}";
        $datos = [];

        if ($buscar !== '') {
            $campoExtra = match ($entidad) {
                'destinos' => ' OR provincia LIKE :buscar',
                'usuarios' => ' OR correo LIKE :buscar',
                default => '',
            };
            $sql .= " WHERE nombre LIKE :buscar{$campoExtra}";
            $datos['buscar'] = '%' . trim($buscar) . '%';
        }

        $sql .= ' ORDER BY nombre';
        $consulta = $conexion->prepare($sql);
        $consulta->execute($datos);
        return $consulta->fetchAll();
    }

    public static function listarActivos(string $entidad): array
    {
        self::validarEntidad($entidad);

        $consulta = Database::conectar()->prepare(
            "SELECT * FROM {$entidad} WHERE estado = 'activo' ORDER BY nombre"
        );

        $consulta->execute();

        return $consulta->fetchAll();
    }

    public static function hotelesPorDestino(int $idDestino): array
    {
        $consulta = Database::conectar()->prepare(
            'SELECT * FROM hoteles
            WHERE id_destino = :id_destino AND estado = "activo"
            ORDER BY nombre'
        );

        $consulta->execute([
            'id_destino' => $idDestino,
        ]);

        return $consulta->fetchAll();
    }

    public static function actividadesPorDestino(int $idDestino): array
    {
        $consulta = Database::conectar()->prepare(
            'SELECT * FROM actividades
            WHERE id_destino = :id_destino AND estado = "activo"
            ORDER BY nombre'
        );

        $consulta->execute([
            'id_destino' => $idDestino,
        ]);

        return $consulta->fetchAll();
    }

    public static function buscar(string $entidad, int $id): ?array
    {
        self::validarEntidad($entidad);
        $llave = self::ENTIDADES[$entidad];
        $consulta = Database::conectar()->prepare(
            "SELECT * FROM {$entidad} WHERE {$llave} = :id LIMIT 1"
        );
        $consulta->execute(['id' => $id]);
        $resultado = $consulta->fetch();
        return $resultado ?: null;
    }

    // Mantenimiento del catálogo

    public static function guardarDestino(array $datos, int $id = 0): bool
    {
        $campos = [
            'nombre' => trim($datos['nombre']),
            'provincia' => $datos['provincia'],
            'descripcion' => trim($datos['descripcion']),
            'imagen_principal' => trim($datos['imagen_principal']),
            'latitud' => $datos['latitud'] !== '' ? $datos['latitud'] : null,
            'longitud' => $datos['longitud'] !== '' ? $datos['longitud'] : null,
            'ubicacion' => trim($datos['ubicacion']),
            'estado' => $datos['estado'],
        ];
        return self::guardarFila('destinos', 'id_destino', $campos, $id);
    }

    public static function guardarHotel(array $datos, int $id = 0): bool
    {
        $campos = [
            'id_destino' => (int) $datos['id_destino'],
            'nombre' => trim($datos['nombre']),
            'categoria' => (int) $datos['categoria'],
            'direccion' => trim($datos['direccion']),
            'telefono' => trim($datos['telefono']),
            'correo' => trim($datos['correo']),
            'precio_noche' => (float) $datos['precio_noche'],
            'cantidad_habitaciones' => (int) $datos['cantidad_habitaciones'],
            'descripcion' => trim($datos['descripcion']),
            'imagen' => trim($datos['imagen']),
            'estado' => $datos['estado'],
        ];
        return self::guardarFila('hoteles', 'id_hotel', $campos, $id);
    }

    public static function guardarActividad(array $datos, int $id = 0): bool
    {
        $campos = [
            'id_destino' => (int) $datos['id_destino'],
            'nombre' => trim($datos['nombre']),
            'tipo' => $datos['tipo'],
            'descripcion' => trim($datos['descripcion']),
            'precio' => (float) $datos['precio'],
            'duracion_minutos' => (int) $datos['duracion_minutos'],
            'cupo_maximo' => (int) $datos['cupo_maximo'],
            'imagen' => trim($datos['imagen']),
            'estado' => $datos['estado'] ?? 'activo',
        ];
        return self::guardarFila('actividades', 'id_actividad', $campos, $id);
    }

    /*
        Reutiliza la misma lógica de insert/update para los tres catálogos
     */
    private static function guardarFila(string $tabla, string $llave, array $campos, int $id): bool
    {
        $conexion = Database::conectar();
        if ($id > 0) {
            $asignaciones = implode(', ', array_map(fn($campo) => "$campo = :$campo", array_keys($campos)));
            $campos['id'] = $id;
            $consulta = $conexion->prepare("UPDATE {$tabla} SET {$asignaciones} WHERE {$llave} = :id");
        } else {
            $nombres = implode(', ', array_keys($campos));
            $marcadores = ':' . implode(', :', array_keys($campos));
            $consulta = $conexion->prepare("INSERT INTO {$tabla} ({$nombres}) VALUES ({$marcadores})");
        }
        return $consulta->execute($campos);
    }

    public static function eliminar(string $entidad, int $id): bool
    {
        self::validarEntidad($entidad);
        $conexion = Database::conectar();

        // Antes de eliminar se comprueban las relaciones para conservar la integridad referencial
        $relaciones = [
            'destinos' => [
                ['hoteles', 'id_destino'],
                ['actividades', 'id_destino'],
                ['reservaciones', 'id_destino'],
            ],
            'hoteles' => [
                ['reservacion_hoteles', 'id_hotel'],
            ],
            'actividades' => [
                ['reservacion_actividades', 'id_actividad'],
            ],
        ];

        foreach ($relaciones[$entidad] ?? [] as [$tablaRelacionada, $campo]) {
            $consulta = $conexion->prepare(
                "SELECT COUNT(*) FROM {$tablaRelacionada} WHERE {$campo} = :id"
            );
            $consulta->execute(['id' => $id]);
            if ((int) $consulta->fetchColumn() > 0) {
                throw new DomainException(
                    'No se puede eliminar el registro porque tiene información relacionada. '
                        . 'Puede editarlo y cambiar su estado a inactivo.'
                );
            }
        }

        $llave = self::ENTIDADES[$entidad];
        $consulta = $conexion->prepare("DELETE FROM {$entidad} WHERE {$llave} = :id");
        return $consulta->execute(['id' => $id]);
    }

    // Reservaciones

    /*
        Crea la reservación y sus detalles dentro de una transacción. Si falla cualquiera de los INSERT, se revierte toda la operación
     */
    public static function crearReservacion(int $usuario, array $datos): int
    {
        $conexion = Database::conectar();
        $hotel = self::buscar('hoteles', (int) $datos['id_hotel']);
        $actividad = !empty($datos['id_actividad']) ? self::buscar('actividades', (int) $datos['id_actividad']) : null;

        if ($hotel === null || $hotel['estado'] !== 'activo') {
            throw new RuntimeException('El hotel seleccionado no está disponible.');
        }

        $idDestino = (int) ($datos['id_destino'] ?? 0);

        if ((int) $hotel['id_destino'] !== $idDestino) {
            throw new RuntimeException('El hotel no pertenece al destino seleccionado.');
        }

        if (
            $actividad !== null
            && (
                $actividad['estado'] !== 'activo'
                || (int) $actividad['id_destino'] !== $idDestino
            )
        ) {
            throw new RuntimeException(
                'La actividad no pertenece al destino seleccionado.'
            );
        }
        $inicio = new DateTime($datos['fecha_inicio']);
        $fin = new DateTime($datos['fecha_fin']);
        $noches = max(1, (int) $inicio->diff($fin)->days);
        $habitaciones = max(1, (int) $datos['cantidad_habitaciones']);
        $personas = max(1, (int) $datos['cantidad_personas']);
        $totalHotel = (float) $hotel['precio_noche'] * $noches * $habitaciones;
        $totalActividad = $actividad ? (float) $actividad['precio'] * $personas : 0;

        try {
            $conexion->beginTransaction();

            $consulta = $conexion->prepare(
                'INSERT INTO reservaciones (
                    id_usuario,
                    id_destino,
                    fecha_inicio,
                    fecha_fin,
                    cantidad_personas,
                    estado,
                    total_hoteles,
                    total_actividades,
                    total_reservacion,
                    observaciones
                ) VALUES (
                    :usuario,
                    :destino,
                    :inicio,
                    :fin,
                    :personas,
                    "confirmada",
                    :hotel,
                    :actividad,
                    :total,
                    :observaciones
                )'
            );

            $consulta->execute([
                'usuario' => $usuario,
                'destino' => $hotel['id_destino'],
                'inicio' => $datos['fecha_inicio'],
                'fin' => $datos['fecha_fin'],
                'personas' => $personas,
                'hotel' => $totalHotel,
                'actividad' => $totalActividad,
                'total' => $totalHotel + $totalActividad,
                'observaciones' => trim($datos['observaciones'] ?? ''),
            ]);

            $id = (int) $conexion->lastInsertId();

            $consulta = $conexion->prepare(
                'INSERT INTO reservacion_hoteles (
                    id_reservacion,
                    id_hotel,
                    cantidad_habitaciones,
                    cantidad_noches,
                    precio_noche,
                    subtotal
                ) VALUES (
                    :reserva,
                    :hotel,
                    :habitaciones,
                    :noches,
                    :precio,
                    :subtotal
                )'
            );

            $consulta->execute([
                'reserva' => $id,
                'hotel' => $hotel['id_hotel'],
                'habitaciones' => $habitaciones,
                'noches' => $noches,
                'precio' => $hotel['precio_noche'],
                'subtotal' => $totalHotel,
            ]);

            if ($actividad) {
                $consulta = $conexion->prepare(
                    'INSERT INTO reservacion_actividades (
                        id_reservacion,
                        id_actividad,
                        fecha_actividad,
                        cantidad_personas,
                        precio_persona,
                        subtotal
                    ) VALUES (
                        :reserva,
                        :actividad,
                        :fecha,
                        :personas,
                        :precio,
                        :subtotal
                    )'
                );

                $consulta->execute([
                    'reserva' => $id,
                    'actividad' => $actividad['id_actividad'],
                    'fecha' => $datos['fecha_inicio'],
                    'personas' => $personas,
                    'precio' => $actividad['precio'],
                    'subtotal' => $totalActividad,
                ]);
            }

            self::bitacora(
                $usuario,
                'Crear',
                'Reservaciones',
                "Reservación #{$id}",
                $conexion
            );

            $conexion->commit();

            return $id;
        } catch (Throwable $error) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }

            throw $error;
        }
    }

    public static function reservaciones(?int $usuario = null): array
    {
        $sql = 'SELECT
                    r.*,
                    u.nombre AS usuario,
                    d.nombre AS destino
                FROM reservaciones r
                INNER JOIN usuarios u
                    ON u.id_usuario = r.id_usuario
                INNER JOIN destinos d
                    ON d.id_destino = r.id_destino';

        $datos = [];

        if ($usuario !== null) {
            $sql .= ' WHERE r.id_usuario = :usuario';
            $datos['usuario'] = $usuario;
        }

        $sql .= ' ORDER BY r.fecha_reservacion DESC';

        $consulta = Database::conectar()->prepare($sql);
        $consulta->execute($datos);

        return $consulta->fetchAll();
    }

    public static function cambiarEstadoReservacion(int $id, string $estado): bool
    {
        $estados = ['pendiente', 'confirmada', 'cancelada', 'completada'];

        if (!in_array($estado, $estados, true)) {
            return false;
        }

        $consulta = Database::conectar()->prepare(
            'UPDATE reservaciones
            SET estado = :estado
            WHERE id_reservacion = :id'
        );

        return $consulta->execute([
            'estado' => $estado,
            'id' => $id,
        ]);
    }

    // Perfil y usuarios

    /*
        Obtiene únicamente los datos necesarios para mostrar el perfil La contraseña y los tokens nunca se exponen a la vista
     */
    public static function perfilUsuario(int $id): ?array
    {
        $consulta = Database::conectar()->prepare(
            'SELECT nombre, correo, telefono, fotografia, rol, estado
            FROM usuarios
            WHERE id_usuario = :id
            LIMIT 1'
        );
        $consulta->execute(['id' => $id]);
        $perfil = $consulta->fetch();

        return $perfil !== false ? $perfil : null;
    }

    public static function actualizarPerfil(int $id, array $datos): bool
    {
        $campos = [
            'nombre' => trim($datos['nombre']),
            'correo' => strtolower(trim($datos['correo'])),
            'telefono' => trim($datos['telefono']),
            'id' => $id,
        ];

        $sql = 'UPDATE usuarios
                SET nombre = :nombre,
                    correo = :correo,
                    telefono = :telefono';

        if (!empty($datos['fotografia'])) {
            $sql .= ', fotografia = :fotografia';
            $campos['fotografia'] = $datos['fotografia'];
        }

        if (!empty($datos['contrasena'])) {
            $sql .= ', contrasena = :contrasena';
            $campos['contrasena'] = password_hash(
                $datos['contrasena'],
                PASSWORD_DEFAULT
            );
        }

        $sql .= ' WHERE id_usuario = :id';

        return Database::conectar()->prepare($sql)->execute($campos);
    }

    /*
        Comprueba si un correo pertenece a otro usuario distinto del perfil actual
     */
    public static function correoEnUso(string $correo, int $exceptoId): bool
    {
        $consulta = Database::conectar()->prepare(
            'SELECT COUNT(*)
            FROM usuarios
            WHERE correo = :correo
            AND id_usuario <> :id'
        );
        $consulta->execute([
            'correo' => strtolower(trim($correo)),
            'id' => $exceptoId,
        ]);

        return (int) $consulta->fetchColumn() > 0;
    }

    public static function cambiarEstadoUsuario(int $id, string $estado, string $rol): bool
    {
        if (
            !in_array($estado, ['activo', 'inactivo'], true)
            || !in_array($rol, ['administrador', 'cliente'], true)
        ) {
            return false;
        }

        $consulta = Database::conectar()->prepare(
            'UPDATE usuarios
            SET estado = :estado, rol = :rol
            WHERE id_usuario = :id'
        );

        return $consulta->execute([
            'estado' => $estado,
            'rol' => $rol,
            'id' => $id,
        ]);
    }

    // Favoritos y comentarios

    public static function favoritos(int $usuario): array
    {
        $consulta = Database::conectar()->prepare(
            'SELECT d.*
            FROM favoritos f
            INNER JOIN destinos d ON d.id_destino = f.id_destino
            WHERE f.id_usuario = :usuario
            ORDER BY d.nombre'
        );

        $consulta->execute([
            'usuario' => $usuario,
        ]);

        return $consulta->fetchAll();
    }

    public static function comentarios(int $destino): array
    {
        $consulta = Database::conectar()->prepare(
            'SELECT c.*, u.nombre AS usuario
            FROM comentarios c
            INNER JOIN usuarios u ON u.id_usuario = c.id_usuario
            WHERE c.id_destino = :destino
            AND c.estado = "aprobado"
            ORDER BY c.fecha_registro DESC'
        );

        $consulta->execute([
            'destino' => $destino,
        ]);

        return $consulta->fetchAll();
    }

    public static function comentar(int $usuario, int $destino, string $comentario, int $calificacion): bool
    {
        $consulta = Database::conectar()->prepare(
            'INSERT INTO comentarios (
                id_usuario,
                id_destino,
                comentario,
                calificacion,
                estado
            ) VALUES (
                :usuario,
                :destino,
                :comentario,
                :calificacion,
                "aprobado"
            )'
        );

        return $consulta->execute([
            'usuario' => $usuario,
            'destino' => $destino,
            'comentario' => trim($comentario),
            'calificacion' => max(1, min(5, $calificacion)),
        ]);
    }

    /*
        Agrega el destino a favoritos o lo elimina cuando ya estaba guardado
     */
    public static function alternarFavorito(int $usuario, int $destino): void
    {
        $conexion = Database::conectar();
        $datos = [
            'usuario' => $usuario,
            'destino' => $destino,
        ];

        $consulta = $conexion->prepare(
            'SELECT id_favorito
            FROM favoritos
            WHERE id_usuario = :usuario
            AND id_destino = :destino'
        );

        $consulta->execute($datos);

        if ($consulta->fetch()) {
            $sql = 'DELETE FROM favoritos
                    WHERE id_usuario = :usuario
                    AND id_destino = :destino';
        } else {
            $sql = 'INSERT INTO favoritos (id_usuario, id_destino)
                    VALUES (:usuario, :destino)';
        }

        $conexion->prepare($sql)->execute($datos);
    }

    // Reportes y bitácora

    /*
        Ejecuta las seis consultas estadísticas mostradas en el panel de reportes
     */
    public static function reportes(): array
    {
        $conexion = Database::conectar();

        $consultas = [
            'destinos' => 'SELECT d.nombre AS etiqueta,
                                COUNT(r.id_reservacion) AS total
                        FROM destinos d
                        LEFT JOIN reservaciones r
                            ON r.id_destino = d.id_destino
                        GROUP BY d.id_destino',
            'hoteles' => 'SELECT h.nombre AS etiqueta,
                                COUNT(rh.id_reservacion_hotel) AS total
                        FROM hoteles h
                        LEFT JOIN reservacion_hoteles rh
                            ON rh.id_hotel = h.id_hotel
                        GROUP BY h.id_hotel
                        ORDER BY total DESC',
            'actividades' => 'SELECT a.nombre AS etiqueta,
                                    COALESCE(SUM(ra.cantidad_personas), 0) AS total
                            FROM actividades a
                            LEFT JOIN reservacion_actividades ra
                                ON ra.id_actividad = a.id_actividad
                            GROUP BY a.id_actividad
                            ORDER BY total DESC',
            'usuarios' => 'SELECT DATE(fecha_registro) AS etiqueta,
                                COUNT(*) AS total
                        FROM usuarios
                        GROUP BY DATE(fecha_registro)',
            'fechas' => 'SELECT DATE(fecha_reservacion) AS etiqueta,
                                COUNT(*) AS total
                        FROM reservaciones
                        GROUP BY DATE(fecha_reservacion)',
            'ingresos' => 'SELECT DATE(fecha_reservacion) AS etiqueta,
                                COALESCE(SUM(total_reservacion), 0) AS total
                        FROM reservaciones
                        WHERE estado <> "cancelada"
                        GROUP BY DATE(fecha_reservacion)',
        ];

        $salida = [];

        foreach ($consultas as $nombre => $sql) {
            $salida[$nombre] = $conexion->query($sql)->fetchAll();
        }

        return $salida;
    }

    /*
        Registra una acción relevante junto con el usuario y la dirección IP
     */
    public static function bitacora(
        ?int $usuario,
        string $accion,
        string $modulo,
        string $descripcion,
        ?PDO $conexion = null
    ): void {
        $conexion ??= Database::conectar();

        $consulta = $conexion->prepare(
            'INSERT INTO bitacora (
                id_usuario,
                accion,
                modulo,
                descripcion,
                direccion_ip
            ) VALUES (
                :usuario,
                :accion,
                :modulo,
                :descripcion,
                :direccion_ip
            )'
        );

        $consulta->execute([
            'usuario' => $usuario,
            'accion' => $accion,
            'modulo' => $modulo,
            'descripcion' => $descripcion,
            'direccion_ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    }

    private static function validarEntidad(string $entidad): void
    {
        if (!array_key_exists($entidad, self::ENTIDADES)) {
            throw new InvalidArgumentException('Módulo no válido.');
        }
    }
}
