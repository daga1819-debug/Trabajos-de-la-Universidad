<?php

require_once __DIR__ . '/../models/Usuario.php';

/*
    Controlador encargado de la autenticación.
    Recibe los datos del formulario, valida la información, consulta el modelo temporal Usuario y crea la sesión cuando las credenciales son correctas.
 */
class AuthController
{
    public static function iniciarSesion(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }

        // Recibimos y limpiamos el correo electrónico.
        $correo = filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_EMAIL);
        $correo = trim((string) $correo);

        // La contraseña no se transforma para no alterar caracteres válidos.
        $contrasena = trim((string) ($_POST['contrasena'] ?? ''));

        // Validación del token CSRF para evitar envíos desde formularios externos.
        $tokenFormulario = (string) ($_POST['csrf_token'] ?? '');
        $tokenSesion = (string) ($_SESSION['csrf_token'] ?? '');

        if ($tokenSesion === '' || !hash_equals($tokenSesion, $tokenFormulario)) {
            $_SESSION['error'] = 'La solicitud no es válida. Intente nuevamente.';
            header('Location: index.php');
            exit;
        }

        if ($correo === '' || $contrasena === '') {
            $_SESSION['error'] = 'Debe completar el correo y la contraseña.';
            header('Location: index.php');
            exit;
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'El correo electrónico no tiene un formato válido.';
            header('Location: index.php');
            exit;
        }

        if (strlen($contrasena) < 6 || strlen($contrasena) > 50) {
            $_SESSION['error'] = 'La contraseña debe tener entre 6 y 50 caracteres.';
            header('Location: index.php');
            exit;
        }

        $usuario = Usuario::autenticar($correo, $contrasena);

        if ($usuario === null) {
            $_SESSION['error'] = 'El correo o la contraseña son incorrectos.';
            $_SESSION['correo_anterior'] = $correo;
            header('Location: index.php');
            exit;
        }

        // Regenerar el identificador de sesión reduce el riesgo de fijación de sesión.
        session_regenerate_id(true);

        $_SESSION['usuario'] = [
            'nombre' => $usuario['nombre'],
            'correo' => $usuario['correo'],
            'rol' => $usuario['rol'],
        ];

        unset($_SESSION['error'], $_SESSION['correo_anterior']);

        header('Location: principal.php');
        exit;
    }
    /*
        Cierra la sesión del usuario y redirige al inicio de sesión.
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
