<?php

require_once __DIR__ . '/../models/Usuario.php';

/*
    Controlador encargado de la autenticación, recibe los datos del formulario, los valida,
    consulta el modelo Usuario y administra la sesión
 */
class AuthController
{
    /*
        Procesa el formulario de inicio de sesión
     */
    public static function iniciarSesion(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }

        // Recibimos y limpiamos el correo

        $correo = filter_input(
            INPUT_POST,
            'correo',
            FILTER_SANITIZE_EMAIL
        );

        $correo = strtolower(trim((string) $correo));

        $contrasena = (string) ($_POST['contrasena'] ?? '');


        // Validación del token CSRF

        $tokenFormulario = (string) ($_POST['csrf_token'] ?? '');
        $tokenSesion = (string) ($_SESSION['csrf_token'] ?? '');

        if (
            $tokenSesion === ''
            || $tokenFormulario === ''
            || !hash_equals($tokenSesion, $tokenFormulario)
        ) {
            self::guardarError(
                'La solicitud no es válida. Intente nuevamente.'
            );
        }

        // Validaciones del lado del servidor

        if ($correo === '' || $contrasena === '') {
            self::guardarError(
                'Debe completar el correo y la contraseña.',
                $correo
            );
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            self::guardarError(
                'El correo electrónico no tiene un formato válido.',
                $correo
            );
        }

        if (
            strlen($contrasena) < 6
            || strlen($contrasena) > 72
        ) {
            self::guardarError(
                'La contraseña debe tener entre 6 y 72 caracteres.',
                $correo
            );
        }

        try {

            // El modelo consulta MySQL y verifica el hash

            $usuario = Usuario::autenticar(
                $correo,
                $contrasena
            );
        } catch (RuntimeException $excepcion) {

            // El mensaje técnico fue registrado en errores.log y se muestra un mensaje genérico al usuario

            self::guardarError(
                'No fue posible procesar el inicio de sesión. '
                    . 'Intente nuevamente.',
                $correo
            );
        }

        if ($usuario === null) {
            self::guardarError(
                'El correo o la contraseña son incorrectos.',
                $correo
            );
        }

        // Se cambia el identificador de sesión después de autenticar correctamente al usuario

        session_regenerate_id(true);

        // Solo se almacenan los datos necesarios

        $_SESSION['usuario'] = [
            'id_usuario' => (int) $usuario['id_usuario'],
            'nombre' => $usuario['nombre'],
            'correo' => $usuario['correo'],
            'rol' => $usuario['rol'],
        ];

        // Se crea un nuevo token para futuras solicitudes

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        unset(
            $_SESSION['error'],
            $_SESSION['correo_anterior']
        );

        header('Location: principal.php');
        exit;
    }


    // Guarda un mensaje de error y regresa al formulario

    private static function guardarError(
        string $mensaje,
        string $correo = ''
    ): never {
        $_SESSION['error'] = $mensaje;

        if ($correo !== '') {
            $_SESSION['correo_anterior'] = $correo;
        }

        header('Location: index.php');
        exit;
    }

    // Cierra completamente la sesión

    public static function cerrarSesion(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $parametros = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $parametros['path'],
                $parametros['domain'],
                $parametros['secure'],
                $parametros['httponly']
            );
        }

        session_destroy();

        header('Location: index.php');
        exit;
    }
}
