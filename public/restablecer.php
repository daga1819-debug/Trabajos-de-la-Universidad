<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/models/Usuario.php';

session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$token = (string) ($_GET['token'] ?? $_POST['token'] ?? '');
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contrasena = (string) ($_POST['contrasena'] ?? '');
    $confirmacion = (string) ($_POST['confirmar'] ?? '');
    $tokenFormulario = (string) ($_POST['csrf_token'] ?? '');

    if (!hash_equals($_SESSION['csrf_token'], $tokenFormulario)) {
        $error = 'La solicitud no es válida.';
    } elseif (strlen($contrasena) < 8 || strlen($contrasena) > 72) {
        $error = 'La contraseña debe tener entre 8 y 72 caracteres.';
    } elseif ($contrasena !== $confirmacion) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (!Usuario::restablecerContrasena($token, $contrasena)) {
        $error = 'El enlace venció o no es válido.';
    } else {
        $_SESSION['registro_exitoso'] =
            'La contraseña fue actualizada. Ya puede iniciar sesión.';

        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva contraseña | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>

<body class="pagina-login">
    <main class="contenedor-login">
        <section class="informacion-login">
            <p class="etiqueta">Seguridad</p>
            <h1>Nueva contraseña</h1>
            <p>Ingresá una contraseña nueva para recuperar el acceso.</p>
        </section>

        <section class="formulario-login">
            <h2>Restablecer acceso</h2>

            <?php if ($error !== ''): ?>
                <div class="mensaje mensaje-error">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <input
                    type="hidden"
                    name="token"
                    value="<?= e($token) ?>">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e($_SESSION['csrf_token']) ?>">

                <div class="campo">
                    <label for="contrasena">Nueva contraseña</label>
                    <input
                        id="contrasena"
                        type="password"
                        name="contrasena"
                        minlength="8"
                        maxlength="72"
                        required>
                </div>

                <div class="campo">
                    <label for="confirmar">Confirmar contraseña</label>
                    <input
                        id="confirmar"
                        type="password"
                        name="confirmar"
                        minlength="8"
                        maxlength="72"
                        required>
                </div>

                <button class="boton boton-principal boton-completo" type="submit">
                    Guardar contraseña
                </button>
            </form>
        </section>
    </main>
</body>

</html>