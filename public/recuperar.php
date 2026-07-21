<?php
require_once __DIR__ . '/../config/config.php';
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>

<body class="pagina-secundaria">
    <main class="tarjeta-formulario-secundario">
        <a class="volver" href="index.php">← Volver al inicio de sesión</a>
        <span class="etiqueta">Seguridad</span>
        <h1>Recuperar contraseña</h1>
        <p>En la versión final se implementará la funcionalidad de recuperación de contraseña.</p>

        <form action="modulo_en_desarrollo.php" method="POST" class="formulario-secundario">
            <div class="grupo-campo">
                <label for="correo">Correo electrónico</label>
                <input type="email" id="correo" name="correo" placeholder="nombre@correo.com" maxlength="100" required>
            </div>
            <button class="boton boton-principal boton-completo" type="submit">Solicitar recuperación</button>
        </form>
    </main>
</body>

</html>