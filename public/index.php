<?php
// Carga la conexión a la base de datos y obtiene los registros de archivos
require_once __DIR__ . '/../config/database.php';

$archivos = [];
$errorConexion = '';
//  Maneja la conexión a la base de datos y captura errores sin detener la carga de la página
try {
    $pdo = getConnection();

    // Une archivos y escaneos para conocer a qué escaneo pertenece cada registro
    $archivos = $pdo->query(
        'SELECT a.id, a.nombre, a.tamano, a.fecha_detectado, a.peligroso, a.escaneo_id, e.fecha AS fecha_escaneo, e.usuario
        FROM archivos a
        INNER JOIN escaneos e ON e.id = a.escaneo_id
        ORDER BY a.id DESC'
    )->fetchAll();
} catch (Throwable $e) {
    // Si hay un error de conexión, se guarda el mensaje para mostrarlo en la interfaz
    $errorConexion = 'No fue posible consultar MySQL. Verifique que la base de datos esté activa e importada.';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>El Archivo Fantasma</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>
    <!-- Encabezado principal  -->
    <header class="encabezado">
        <div>
            <h1>El Archivo Fantasma</h1>
            <p>Panel interno de detección y registro de archivos sospechosos</p>
        </div>
        <div class="estado-sistema">
            <span class="punto-activo"></span>
            Sistema activo
        </div>
    </header>

    <main class="contenedor">
        <!-- Controles principales del panel -->
        <section class="acciones">
            <button id="btnEscanear" class="boton boton-principal">Escanear directorio</button>
            <button id="btnReportes" class="boton boton-secundario">Ver reportes</button>

            <label for="filtroPeligrosos">Filtrar:</label>
            <select id="filtroPeligrosos">
                <option value="todos">Todos</option>
                <option value="peligrosos">Solo peligrosos</option>
            </select>

            <div class="contador">
                Auto-detección en <strong id="segundosRestantes">60</strong> segundos
            </div>
        </section>

        <!-- Tabla con los archivos almacenados en mysql -->
        <section class="panel">
            <div class="panel-titulo">
                <h2>Archivos detectados</h2>
                <span id="mensajeEstado" class="<?= $errorConexion ? 'error' : '' ?>">
                    <?= htmlspecialchars($errorConexion, ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>

            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Tamaño</th>
                            <th>Fecha detectado</th>
                            <th>Escaneo</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="tablaArchivos">
                        <?php foreach ($archivos as $archivo): ?>
                            <tr
                                data-id="<?= (int) $archivo['id'] ?>"
                                data-peligroso="<?= (int) $archivo['peligroso'] ?>"
                                class="<?= $archivo['peligroso'] ? 'archivo-peligroso' : '' ?>">
                                <td><?= (int) $archivo['id'] ?></td>
                                <td><?= htmlspecialchars($archivo['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= number_format((int) $archivo['tamano']) ?> KB</td>
                                <td><?= htmlspecialchars($archivo['fecha_detectado'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>#<?= (int) $archivo['escaneo_id'] ?></td>
                                <td>
                                    <span class="etiqueta <?= $archivo['peligroso'] ? 'etiqueta-peligro' : 'etiqueta-normal' ?>">
                                        <?= $archivo['peligroso'] ? 'Peligroso' : 'Normal' ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="boton-marcar" data-id="<?= (int) $archivo['id'] ?>">
                                        <?= $archivo['peligroso'] ? 'Quitar peligro' : 'Marcar peligroso' ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Sección de reportes -->
        <section id="seccionReportes" class="panel oculto">
            <div class="panel-titulo">
                <h2>Historial de reportes externos</h2>
            </div>
            <div id="listaReportes" class="reportes-grid"></div>
        </section>
    </main>

    <!-- Scripts -->
    <script src="js/app.js"></script>
</body>

</html>