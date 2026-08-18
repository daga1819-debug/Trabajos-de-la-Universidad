<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(ucfirst($modulo)) ?> | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>

<body class="pagina-sistema">

    <header class="barra-sistema">
        <a class="marca-sistema" href="principal.php">
            🌿 Viajar es Pura Vida
        </a>

        <nav>
            <?php
            $opciones = [
                'destinos' => 'Destinos',
                'hoteles' => 'Hoteles',
                'actividades' => 'Actividades',
                'reservaciones' => 'Reservaciones',
                'favoritos' => 'Favoritos',
                'perfil' => 'Mi perfil',
            ];
            ?>

            <?php foreach ($opciones as $ruta => $texto): ?>
                <a
                    class="<?= $modulo === $ruta ? 'activo' : '' ?>"
                    href="?modulo=<?= e($ruta) ?>">
                    <?= e($texto) ?>
                </a>
            <?php endforeach; ?>

            <?php if ($esAdministrador): ?>
                <a
                    class="<?= $modulo === 'usuarios' ? 'activo' : '' ?>"
                    href="?modulo=usuarios">Usuarios</a>
                <a
                    class="<?= $modulo === 'reportes' ? 'activo' : '' ?>"
                    href="?modulo=reportes">Reportes</a>
            <?php endif; ?>

            <a href="cerrar_sesion.php">Salir</a>
        </nav>
    </header>

    <main class="contenido-sistema">
        <div class="titulo-modulo">
            <div>
                <span class="etiqueta">
                    <?= $esAdministrador ? 'Administración' : 'Área de cliente' ?>
                </span>

                <h1><?= e(ucfirst($modulo)) ?></h1>
            </div>
        </div>

        <?php if ($mensaje !== ''): ?>
            <div class="mensaje-exito">
                <?= e($mensaje) ?>
            </div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="mensaje mensaje-error">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <?php
        if (in_array($modulo, ['destinos', 'hoteles', 'actividades'], true)) {
            require __DIR__ . '/catalogo.php';
        } elseif ($modulo === 'reservaciones') {
            require __DIR__ . '/reservaciones.php';
        } elseif ($modulo === 'perfil') {
            require __DIR__ . '/perfil.php';
        } elseif ($modulo === 'favoritos') {
            require __DIR__ . '/favoritos.php';
        } elseif ($modulo === 'usuarios') {
            require __DIR__ . '/usuarios.php';
        } elseif ($modulo === 'reportes') {
            require __DIR__ . '/reportes.php';
        }
        ?>
    </main>

    <script src="assets/js/sistema.js"></script>
</body>

</html>