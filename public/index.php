<?php
require_once __DIR__ . '/../config/config.php';
session_start();

if (isset($_SESSION['usuario'])) {
    header('Location: principal.php');
    exit;
}

// Token utilizado para proteger el formulario contra solicitudes externas.
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = (string) ($_SESSION['error'] ?? '');
$correoAnterior = (string) ($_SESSION['correo_anterior'] ?? '');
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema web de gestión turística de Costa Rica.">
    <title>Iniciar sesión | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>

<body class="pagina-login">
    <main class="login-contenedor">
        <section class="login-presentacion" aria-label="Presentación del sistema">
            <div class="login-presentacion-contenido">
                <span class="etiqueta-clara">Turismo nacional</span>
                <h1>Viajar es <span>Pura Vida</span></h1>
                <p>
                    Descubrí destinos, hoteles y actividades para crear experiencias inolvidables en Costa Rica.
                </p>
                    <!-- Sección de beneficios de viajar a Costa Rica. -->
                <div class="beneficios-login">
                    <article>
                        <strong>7 provincias</strong>
                        <span>Una gran variedad de destinos.</span>
                    </article>
                    <article>
                        <strong>Aventura</strong>
                        <span>Canopy, rafting, senderismo y más.</span>
                    </article>
                    <article>
                        <strong>Reservas</strong>
                        <span>Hoteles y actividades en un solo lugar.</span>
                    </article>
                </div>
            </div>
        </section>
            <!-- Sección de formulario de inicio de sesión. -->
        <section class="login-formulario-contenedor">
            <div class="login-marca">
                <span>🌿</span>
                <div>
                    <strong>Viajar es Pura Vida</strong>
                    <small>Sistema de gestión turística</small>
                </div>
            </div>

            <div class="login-formulario">
                <span class="etiqueta">Bienvenido</span>
                <h2>Iniciar sesión</h2>
                <p>Ingrese sus datos para explorar el sistema.</p>

                <?php if ($error !== ''): ?>
                    <div class="mensaje mensaje-error" role="alert">
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form action="autenticar.php" method="POST">
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

                    <div class="grupo-campo">
                        <label for="correo">Correo electrónico</label>
                        <input
                            type="email"
                            id="correo"
                            name="correo"
                            value="<?= htmlspecialchars($correoAnterior, ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="nombre@correo.com"
                            maxlength="100"
                            autocomplete="email"
                            required>
                    </div>

                    <div class="grupo-campo">
                        <label for="contrasena">Contraseña</label>
                        <input
                            type="password"
                            id="contrasena"
                            name="contrasena"
                            placeholder="Mínimo 6 caracteres"
                            minlength="6"
                            maxlength="50"
                            autocomplete="current-password"
                            required>
                    </div>

                    <div class="fila-formulario">
                        <label class="casilla">
                            <input type="checkbox" name="recordar" value="1">
                            <span>Recordarme</span>
                        </label>
                        <a href="recuperar.php">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button class="boton boton-principal boton-completo" type="submit">
                        Iniciar sesión
                    </button>
                </form>

                <p class="texto-registro">
                    ¿No tienes una cuenta?
                    <a href="registro.php">Registrate aquí</a>
                </p>
                <!-- Sección de credenciales de prueba para ingresar. -->
                <aside class="credenciales-prueba">
                    <strong>Credenciales de prueba</strong>
                    <p>Administrador: admin@viajarespuravida.cr / Admin123</p>
                    <p>Cliente: cliente@viajarespuravida.cr / Cliente123</p>
                </aside>
            </div>
        </section>
    </main>
</body>

</html>