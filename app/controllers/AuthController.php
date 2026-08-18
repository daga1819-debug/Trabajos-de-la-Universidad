<?php

require_once __DIR__ . '/../models/Usuario.php';

/*
    Controla el inicio y cierre de sesión
 */
class AuthController
{
    /*
        Valida el formulario de acceso y crea la sesión del usuario autenticado
     */
    public static function iniciarSesion(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }

        $correo = strtolower(trim((string) filter_input(
            INPUT_POST,
            'correo',
            FILTER_SANITIZE_EMAIL
        )));
        $contrasena = (string) ($_POST['contrasena'] ?? '');

        // El token CSRF evita que otro sitio envíe el formulario en nombre del usuario
        $tokenFormulario = (string) ($_POST['csrf_token'] ?? '');
        $tokenSesion = (string) ($_SESSION['csrf_token'] ?? '');

        if (
            $tokenSesion === ''
            || $tokenFormulario === ''
            || !hash_equals($tokenSesion, $tokenFormulario)
        ) {
            self::guardarError('La solicitud no es válida. Intente nuevamente.');
        }

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

        if (strlen($contrasena) < 8 || strlen($contrasena) > 72) {
            self::guardarError(
                'La contraseña debe tener entre 8 y 72 caracteres.',
                $correo
            );
        }

        try {
            $usuario = Usuario::autenticar($correo, $contrasena);
        } catch (RuntimeException $excepcion) {
            // El detalle técnico ya se registra en storage/logs/errores.log
            self::guardarError(
                'No fue posible procesar el inicio de sesión. Intente nuevamente.',
                $correo
            );
        }

        if ($usuario === null) {
            self::guardarError(
                'El correo o la contraseña son incorrectos.',
                $correo
            );
        }

        // Evita reutilizar el identificador de una sesión previa al autenticarse
        session_regenerate_id(true);

        $_SESSION['usuario'] = [
            'id_usuario' => (int) $usuario['id_usuario'],
            'nombre' => $usuario['nombre'],
            'correo' => $usuario['correo'],
            'telefono' => $usuario['telefono'] ?? '',
            'fotografia' => $usuario['fotografia'] ?? null,
            'rol' => $usuario['rol'],
        ];

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        unset($_SESSION['error'], $_SESSION['correo_anterior']);

        header('Location: principal.php');
        exit;
    }

    /*
        Conserva el mensaje y el correo para mostrarlos nuevamente en el login
     */
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

    /*
        Elimina variables, cookie e identificador de la sesión actual
     */
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
