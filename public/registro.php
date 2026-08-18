<?php

require_once __DIR__ . '/../config/config.php';

session_start();

// Un usuario autenticado no necesita volver a registrarse
if (isset($_SESSION['usuario'])) {
    header('Location: principal.php');
    exit;
}

// Recupera mensajes y datos no sensibles conservados después de un error
$error = (string) ($_SESSION['error_registro'] ?? '');
$datosAnteriores = $_SESSION['datos_registro'] ?? [
    'nombre' => '',
    'correo' => '',
    'telefono' => '',
];

unset(
    $_SESSION['error_registro'],
    $_SESSION['datos_registro']
);

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>

<body class="pagina-login">
    <main class="contenedor-login">
        <section class="informacion-login">
            <p class="etiqueta">Descubrí Costa Rica</p>
            <h1>Viajar es Pura Vida</h1>
            <p>
                Creá tu cuenta y empezá a planificar tu próxima experiencia por Costa Rica.
            </p>
        </section>

        <section class="formulario-login">
            <h2>Crear cuenta</h2>
            <p>Completá tus datos para registrarte.</p>

            <?php if ($error !== ''): ?>
                <div class="mensaje mensaje-error" role="alert">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form action="registro_usuario.php" method="POST">
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e($_SESSION['csrf_token']) ?>">

                <div class="campo">
                    <label for="nombre">Nombre completo</label>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        minlength="3"
                        maxlength="100"
                        value="<?= e($datosAnteriores['nombre']) ?>"
                        autocomplete="name"
                        required>
                </div>

                <div class="campo">
                    <label for="correo">Correo electrónico</label>
                    <input
                        type="email"
                        id="correo"
                        name="correo"
                        maxlength="150"
                        value="<?= e($datosAnteriores['correo']) ?>"
                        autocomplete="email"
                        required>
                </div>

                <div class="campo">
                    <label for="telefono">Teléfono</label>
                    <input
                        type="text"
                        id="telefono"
                        name="telefono"
                        minlength="8"
                        maxlength="20"
                        value="<?= e($datosAnteriores['telefono']) ?>"
                        autocomplete="tel"
                        required>
                </div>

                <div class="campo">
                    <label for="contrasena">Contraseña</label>
                    <input
                        type="password"
                        id="contrasena"
                        name="contrasena"
                        minlength="8"
                        maxlength="72"
                        autocomplete="new-password"
                        required>
                </div>

                <div class="campo">
                    <label for="confirmar_contrasena">Confirmar contraseña</label>
                    <input
                        type="password"
                        id="confirmar_contrasena"
                        name="confirmar_contrasena"
                        minlength="8"
                        maxlength="72"
                        autocomplete="new-password"
                        required>
                </div>

                <button class="boton boton-principal boton-completo" type="submit">
                    Registrarme
                </button>
            </form>

            <div class="enlaces-login">
                <a href="index.php">Ya tengo una cuenta</a>
            </div>
        </section>
    </main>
</body>

</html>