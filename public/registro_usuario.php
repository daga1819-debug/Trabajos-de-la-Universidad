<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/models/Usuario.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: registro.php');
    exit;
}

$nombre = trim((string) ($_POST['nombre'] ?? ''));
$correo = strtolower(trim((string) filter_input(
    INPUT_POST,
    'correo',
    FILTER_SANITIZE_EMAIL
)));
$telefono = trim((string) ($_POST['telefono'] ?? ''));
$contrasena = (string) ($_POST['contrasena'] ?? '');
$confirmarContrasena = (string) ($_POST['confirmar_contrasena'] ?? '');

// Solo se conservan datos no sensibles para repoblar el formulario si falla
$_SESSION['datos_registro'] = [
    'nombre' => $nombre,
    'correo' => $correo,
    'telefono' => $telefono,
];

$tokenFormulario = (string) ($_POST['csrf_token'] ?? '');
$tokenSesion = (string) ($_SESSION['csrf_token'] ?? '');

if (
    $tokenFormulario === ''
    || $tokenSesion === ''
    || !hash_equals($tokenSesion, $tokenFormulario)
) {
    $_SESSION['error_registro'] = 'La solicitud no es válida.';
    header('Location: registro.php');
    exit;
}

// Validaciones del lado del servidor: no se depende únicamente del HTML
if (
    $nombre === ''
    || $correo === ''
    || $telefono === ''
    || $contrasena === ''
    || $confirmarContrasena === ''
) {
    $_SESSION['error_registro'] = 'Debe completar todos los campos.';
    header('Location: registro.php');
    exit;
}

if (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 100) {
    $_SESSION['error_registro'] = 'El nombre debe tener entre 3 y 100 caracteres.';
    header('Location: registro.php');
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_registro'] = 'El correo electrónico no es válido.';
    header('Location: registro.php');
    exit;
}

if (!preg_match('/^[0-9+\-\s]{8,20}$/', $telefono)) {
    $_SESSION['error_registro'] = 'El teléfono no tiene un formato válido.';
    header('Location: registro.php');
    exit;
}

if (strlen($contrasena) < 8 || strlen($contrasena) > 72) {
    $_SESSION['error_registro'] = 'La contraseña debe tener entre 8 y 72 caracteres.';
    header('Location: registro.php');
    exit;
}

if ($contrasena !== $confirmarContrasena) {
    $_SESSION['error_registro'] = 'Las contraseñas no coinciden.';
    header('Location: registro.php');
    exit;
}

try {
    if (Usuario::existeCorreo($correo)) {
        $_SESSION['error_registro'] =
            'Ya existe una cuenta registrada con ese correo.';
        header('Location: registro.php');
        exit;
    }

    if (!Usuario::crear($nombre, $correo, $telefono, $contrasena)) {
        throw new RuntimeException('No fue posible crear el usuario.');
    }

    unset($_SESSION['datos_registro']);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['registro_exitoso'] =
        'Su cuenta fue creada correctamente. Ahora puede iniciar sesión.';

    header('Location: index.php');
    exit;
} catch (Throwable $excepcion) {
    error_log(
        '[' . date('Y-m-d H:i:s') . '] Error al registrar usuario: '
            . $excepcion->getMessage()
            . PHP_EOL,
        3,
        __DIR__ . '/../storage/logs/errores.log'
    );

    $_SESSION['error_registro'] =
        'No fue posible completar el registro. Intente nuevamente.';

    header('Location: registro.php');
    exit;
}
