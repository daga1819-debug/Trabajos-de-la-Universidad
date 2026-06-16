<?php
require_once 'includes/funciones.php'; // Iniciar sesión y cargar funciones necesarias
iniciarLibros();

// Variables para estadísticas
$total = count($_SESSION['libros']);
$disponibles = 0;
$noDisponibles = 0;
$totalPaginas = 0;
$inventarioTotal = 0;
$generos = [];
$libroAntiguo = $_SESSION['libros'][0];
$libroReciente = $_SESSION['libros'][0];

// Recorrer los libros para calcular estadísticas
for ($i = 0; $i < $total; $i++) {
    $libro = $_SESSION['libros'][$i];
    if ($libro['disponible']) {
        $disponibles++;
    } else {
        $noDisponibles++;
    }
    $totalPaginas += $libro['paginas'];
    $inventarioTotal += $libro['cantidad'];
    if ($libro['año'] < $libroAntiguo['año']) {
        $libroAntiguo = $libro;
    }
    if ($libro['año'] > $libroReciente['año']) {
        $libroReciente = $libro;
    }
    if (!isset($generos[$libro['genero']])) {
        $generos[$libro['genero']] = 0;
    }
    $generos[$libro['genero']]++;
}

// Calcular porcentaje de disponibilidad y promedio de páginas
$porcentaje = $total > 0 ? ($disponibles / $total) * 100 : 0;
$promedioPaginas = $total > 0 ? $totalPaginas / $total : 0;
$generoPopular = '';
$mayorCantidad = 0;
// Determinar el género más popular
foreach ($generos as $nombreGenero => $cantidadGenero) {
    if ($cantidadGenero > $mayorCantidad) {
        $mayorCantidad = $cantidadGenero;
        $generoPopular = $nombreGenero;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>
    <header>
        <h1><?php echo Nombre_Biblioteca; ?></h1>
        <p>Estadísticas del sistema</p>
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
    <!-- Resultados de estadísticas -->
    <main>
        <section class="card">
            <h2>Resumen general</h2>
            <div class="grid-estadisticas">
                <div class="estadistica">
                    <div class="numero"><?php echo $total; ?></div>
                    <p>Total de libros</p>
                </div>
                <div class="estadistica">
                    <div class="numero"><?php echo $disponibles; ?></div>
                    <p>Libros disponibles</p>
                </div>
                <div class="estadistica">
                    <div class="numero"><?php echo $noDisponibles; ?></div>
                    <p>Libros no disponibles</p>
                </div>
                <div class="estadistica">
                    <div class="numero"><?php echo number_format($porcentaje, 2); ?>%</div>
                    <p>Porcentaje de disponibilidad</p>
                </div>
                <div class="estadistica">
                    <div class="numero"><?php echo $libroAntiguo['año']; ?></div>
                    <p>Más antiguo: <?php echo $libroAntiguo['titulo']; ?></p>
                </div>
                <div class="estadistica">
                    <div class="numero"><?php echo $libroReciente['año']; ?></div>
                    <p>Más reciente: <?php echo $libroReciente['titulo']; ?></p>
                </div>
                <div class="estadistica">
                    <div class="numero"><?php echo $generoPopular; ?></div>
                    <p>Género más popular</p>
                </div>
                <div class="estadistica">
                    <div class="numero"><?php echo $totalPaginas; ?></div>
                    <p>Total de páginas</p>
                </div>
                <div class="estadistica">
                    <div class="numero"><?php echo number_format($promedioPaginas, 2); ?></div>
                    <p>Promedio de páginas</p>
                </div>
                <div class="estadistica">
                    <div class="numero"><?php echo $inventarioTotal; ?></div>
                    <p>Inventario total</p>
                </div>
            </div>
        </section>
    </main>
    <footer>Universidad Tecnológica Costarricense, Programación IV</footer>
</body>

</html>