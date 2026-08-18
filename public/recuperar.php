<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/models/Usuario.php';

session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$enlace = '';
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tokenSesion = (string) $_SESSION['csrf_token'];
    $tokenFormulario = (string) ($_POST['csrf_token'] ?? '');

    if (!hash_equals($tokenSesion, $tokenFormulario)) {
        $mensaje = 'La solicitud no es válida.';
    } else {
        $correo = strtolower(trim((string) ($_POST['correo'] ?? '')));
        $token = filter_var($correo, FILTER_VALIDATE_EMAIL)
            ? Usuario::crearTokenRecuperacion($correo)
            : null;

        $mensaje = 'Si el correo está registrado, se generó un enlace temporal.';

        if ($token !== null) {
            $enlace = 'restablecer.php?token=' . urlencode($token);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>

<body class="pagina-login">
    <main class="contenedor-login">
        <section class="informacion-login">
            <p class="etiqueta">Acceso seguro</p>
            <h1>Recuperar contraseña</h1>
            <p>Generá un enlace temporal para cambiar tu contraseña.</p>
        </section>

        <section class="formulario-login">
            <h2>Solicitar enlace</h2>
            <p>Ingresá el correo asociado con tu cuenta.</p>

            <?php if ($mensaje !== ''): ?>
                <div class="mensaje-exito">
                    <?= e($mensaje) ?>
                </div>
            <?php endif; ?>

            <?php if ($enlace !== ''): ?>
                <a class="boton boton-principal" href="<?= e($enlace) ?>">
                    Continuar con el cambio
                </a>
            <?php endif; ?>

            <form method="POST">
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e($_SESSION['csrf_token']) ?>">

                <div class="campo">
                    <label for="correo">Correo electrónico</label>
                    <input id="correo" type="email" name="correo" required>
                </div>

                <button class="boton boton-principal boton-completo" type="submit">
                    Generar enlace
                </button>
            </form>

            <div class="enlaces-login">
                <a href="index.php">Volver al inicio de sesión</a>
            </div>
        </section>
    </main>
</body>

</html>