<?php
require_once __DIR__ . '/../config/config.php';
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>

<body class="pagina-secundaria">
    <main class="tarjeta-formulario-secundario">
        <a class="volver" href="index.php">← Volver al inicio de sesión</a>
        <span class="etiqueta">Nuevo usuario</span>
        <h1>Crear una cuenta</h1>
        <p>Este formulario es aún un boceto visual del registro de clientes.</p>

        <form action="modulo_en_desarrollo.php" method="POST" class="formulario-secundario">
            <div class="grupo-campo"><label for="nombre">Nombre completo</label>
                <input type="text" id="nombre" name="nombre" minlength="3" maxlength="100" required>
            </div>
            <div class="grupo-campo"><label for="correo">Correo electrónico</label>
                <input type="email" id="correo" name="correo" maxlength="100" required>
            </div>
            <div class="grupo-campo"><label for="telefono">Teléfono</label>
                <input type="tel" id="telefono" name="telefono" pattern="[0-9]{8}" placeholder="88888888" required>
            </div>
            <div class="grupo-campo"><label for="contrasena">Contraseña</label>
                <input type="password" id="contrasena" name="contrasena" minlength="8" maxlength="50" required>
            </div>
            <div class="grupo-campo"><label for="confirmacion">Confirmar contraseña</label>
                <input type="password" id="confirmacion" name="confirmacion" minlength="8" maxlength="50" required>
            </div>
            <button class="boton boton-principal boton-completo" type="submit">Crear cuenta</button>
        </form>
    </main>
</body>

</html>