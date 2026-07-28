<?php
require_once __DIR__ . '/../config/config.php';
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo en desarrollo | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body class="pagina-secundaria">
    <main class="tarjeta-formulario-secundario centrado">
        <div class="icono-grande">🚧</div>
        <span class="etiqueta">Segundo avance</span>
        <h1>Módulo en desarrollo</h1>
        <p>
            Esta pantalla tendrá una funcionalidad en futuras etapas del proyecto.
        </p>
        <a class="boton boton-principal" href="<?= isset($_SESSION['usuario']) ? 'principal.php' : 'index.php' ?>">Regresar</a>
    </main>
</body>
</html>
