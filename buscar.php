<?php
require_once 'includes/funciones.php'; // Iniciar sesión y cargar funciones necesarias
iniciarLibros();
// Obtener y limpiar los parámetros de búsqueda
$titulo = limpiarDato($_GET['titulo'] ?? '');
$genero = limpiarDato($_GET['genero'] ?? '');
$estado = limpiarDato($_GET['estado'] ?? 'todos');
$resultados = [];

// Filtrar los libros según los criterios de búsqueda
foreach ($_SESSION['libros'] as $posicion => $libro) {
    $cumple = true;

    if (!empty($titulo)) {
        if (stripos($libro['titulo'], $titulo) === false) {
            $cumple = false;
        }
    }

    switch ($genero) {
        case 'Ficción':
        case 'No Ficción':
        case 'Ciencia':
        case 'Historia':
        case 'Romance':
        case 'Fantasía':
            if ($libro['genero'] != $genero) {
                $cumple = false;
            }
            break;
        default:
            break;
    }

    if ($estado == 'disponibles' && !$libro['disponible']) {
        $cumple = false;
    } elseif ($estado == 'no_disponibles' && $libro['disponible']) {
        $cumple = false;
    }

    if ($cumple) {
        $libro['posicion'] = $posicion;
        array_push($resultados, $libro);
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar libros</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>
    <header>
        <h1><?php echo Nombre_Biblioteca; ?></h1>
        <p>Buscar y filtrar libros</p>
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
    <!-- Formulario de búsqueda -->
    <main>
        <section class="card">
            <h2>Formulario de búsqueda</h2>
            <form action="buscar.php" method="get">
                <div class="form-fila"><label for="titulo">Título del libro:</label>
                <input type="text" id="titulo" name="titulo" value="<?php echo $titulo; ?>"></div>
                <div class="form-fila"><label for="genero">Género:</label>
                    <select name="genero" id="genero">
                        <option value="">Todos los géneros</option>
                        <option value="Ficción">Ficción</option>
                        <option value="No Ficción">No Ficción</option>
                        <option value="Ciencia">Ciencia</option>
                        <option value="Historia">Historia</option>
                        <option value="Romance">Romance</option>
                        <option value="Fantasía">Fantasía</option>
                    </select></div>
                <div class="opciones">
                    <label><input type="radio" name="estado" value="todos" <?php echo ($estado == 'todos') ? 'checked' : ''; ?>> Todos</label>
                    <label><input type="radio" name="estado" value="disponibles" <?php echo ($estado == 'disponibles') ? 'checked' : ''; ?>> Disponibles</label>
                    <label><input type="radio" name="estado" value="no_disponibles" <?php echo ($estado == 'no_disponibles') ? 'checked' : ''; ?>> No disponibles</label>
                </div>
                <button type="submit">Buscar</button>
            </form>
        </section>
        <!-- Tabla de resultados -->
        <section class="card">
            <h2>Resultados</h2>
            <p>Cantidad total de libros encontrados: <?php echo count($resultados); ?></p>
            <?php if (count($resultados) == 0) { ?>
                <div class="mensaje info">No hay resultados.</div>
            <?php } else { ?>
                <table>
                    <thead>
                        <tr>
                            <th>ISBN</th>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>Género</th>
                            <th>Año</th>
                            <th>Páginas</th>
                            <th>Disponibilidad</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resultados as $libro) { ?> <!-- for each para mostrar cada libro encontrado en una fila de la tabla -->
                            <tr>
                                <td><?php echo $libro['isbn']; ?></td>
                                <td><?php echo $libro['titulo']; ?></td>
                                <td><?php echo $libro['autor']; ?></td>
                                <td><?php echo $libro['genero']; ?></td>
                                <td><?php echo $libro['año']; ?></td>
                                <td><?php echo $libro['paginas']; ?></td>
                                <td><span class="<?php echo claseDisponible($libro['disponible']); ?>"><?php echo textoDisponible($libro['disponible']); ?></span></td>
                                <td><?php echo $libro['cantidad']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </section>
    </main>
    <footer>Universidad Tecnológica Costarricense, Programación IV</footer>
</body>

</html>