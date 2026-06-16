<?php
require_once 'includes/funciones.php'; // Iniciar sesión y cargar funciones necesarias
iniciarLibros();

// Obtener mensaje de éxito o error si existe
$mensaje = isset($_GET['mensaje']) ? limpiarDato($_GET['mensaje']) : '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo Nombre_Biblioteca; ?></title>
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>
    <header>
        <h1><?php echo Nombre_Biblioteca; ?></h1>
        <p>Sistema de Gestión de Biblioteca Digital</p>
    </header>
    <!-- Menú de navegación -->
    <nav>
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="registrar.php">Registrar libro</a></li>
            <li><a href="buscar.php">Buscar/Filtrar libros</a></li>
            <li><a href="estadisticas.php">Estadísticas</a></li>
            <li><a href="salir.php">Salir</a></li>
        </ul>
    </nav>
    <!-- Tabla de libros almacenados -->
    <main>
        <?php if (!empty($mensaje)) {
            echo '<div class="mensaje exito">' . $mensaje . '</div>';
        } ?>
        <section class="card">
            <h2>Libros almacenados</h2>
            <p>Total de libros: <?php echo count($_SESSION['libros']); ?></p>
            <table>
                <thead>
                    <tr>
                        <th>ISBN</th>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Género</th>
                        <th>Año</th>
                        <th>Páginas</th>
                        <th>Estado</th>
                        <th>Cantidad</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['libros'] as $posicion => $libro) { ?> <!-- for each para mostrar cada libro almacenado en una fila de la tabla -->
                        <tr>
                            <td><?php echo $libro['isbn']; ?></td>
                            <td><?php echo $libro['titulo']; ?></td>
                            <td><?php echo $libro['autor']; ?></td>
                            <td><?php echo $libro['genero']; ?></td>
                            <td><?php echo $libro['año']; ?></td>
                            <td><?php echo $libro['paginas']; ?></td>
                            <td><span class="<?php echo claseDisponible($libro['disponible']); ?>"><?php echo textoDisponible($libro['disponible']); ?></span></td>
                            <td><?php echo $libro['cantidad']; ?></td>
                            <td><a class="boton pequeno" href="cambiar_disponibilidad.php?id=<?php echo $posicion; ?>">Cambiar disponibilidad</a></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>
    </main>
    <footer>Universidad Tecnológica Costarricense, Programación IV</footer>
</body>

</html>