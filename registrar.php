<?php
require_once 'includes/funciones.php'; // Iniciar sesión y cargar funciones necesarias
iniciarLibros();

// Variables para almacenar errores y datos del formulario
$errores = [];
$exito = '';
$isbn = $titulo = $autor = $genero = $anio = $paginas = $cantidad = '';
$disponible = false;

// Procesar el formulario al enviarlo
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $isbn = limpiarDato($_POST['isbn'] ?? '');
    $titulo = limpiarDato($_POST['titulo'] ?? '');
    $autor = limpiarDato($_POST['autor'] ?? '');
    $genero = limpiarDato($_POST['genero'] ?? '');
    $anio = limpiarDato($_POST['anio'] ?? '');
    $paginas = limpiarDato($_POST['paginas'] ?? '');
    $cantidad = limpiarDato($_POST['cantidad'] ?? '');
    $disponible = isset($_POST['disponible']);

    if (empty($isbn) || !is_numeric($isbn) || strlen($isbn) > Max_ISBN) {
        $errores[] = 'El ISBN es obligatorio, solo números y máximo 13 caracteres.';
    }
    if (isbnExiste($isbn)) {
        $errores[] = 'El ISBN ya existe, debe ser único.';
    }
    if (empty($titulo) || strlen($titulo) < 5) {
        $errores[] = 'El título es obligatorio y debe tener mínimo 5 caracteres.';
    }
    if (empty($autor) || strlen($autor) < 3) {
        $errores[] = 'El autor es obligatorio y debe tener mínimo 3 caracteres.';
    }
    if (empty($genero)) {
        $errores[] = 'Debe seleccionar un género.';
    }
    if (empty($anio) || !is_numeric($anio) || (int)$anio < Anio_Min || (int)$anio > Anio_Max) {
        $errores[] = 'El año debe estar entre 1900 y 2024.';
    }
    if (empty($paginas) || !is_numeric($paginas) || (int)$paginas < Paginas_Min || (int)$paginas > Paginas_Max) {
        $errores[] = 'Las páginas deben estar entre 1 y 5000.';
    }
    if (empty($cantidad) || !is_numeric($cantidad) || (int)$cantidad < 1) {
        $errores[] = 'La cantidad en inventario debe ser mínimo 1.';
    }

    // Si no hay errores, agregar el libro al sistema
    if (count($errores) == 0) {
        $nuevoLibro = [
            'isbn' => $isbn,
            'titulo' => $titulo,
            'autor' => $autor,
            'genero' => $genero,
            'año' => (int)$anio,
            'paginas' => (int)$paginas,
            'disponible' => $disponible,
            'cantidad' => (int)$cantidad
        ];
        agregarLibro($nuevoLibro);
        $exito = 'Libro registrado correctamente.';
        $isbn = $titulo = $autor = $genero = $anio = $paginas = $cantidad = '';
        $disponible = false;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar libro</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>
    <header>
        <h1><?php echo Nombre_Biblioteca; ?></h1>
        <p>Registrar libro</p>
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
    <!-- Formulario de registro -->
    <main>
        <section class="card">
            <h2>Formulario de registro</h2>
            <!-- Mostrar mensajes de éxito o errores -->
            <?php
            if (!empty($exito)) {
                echo '<div class="mensaje exito">' . $exito . '</div>';
            }
            if (count($errores) > 0) {
                echo '<div class="mensaje error"><strong>Errores encontrados:</strong><ul>';
                foreach ($errores as $error) {
                    echo '<li>' . $error . '</li>';
                }
                echo '</ul></div>';
            }
            ?>
            <!-- Formulario con campos para registrar un nuevo libro -->
            <form action="registrar.php" method="post">
                <div class="form-fila"><label for="isbn">ISBN:</label>
                <input type="text" id="isbn" name="isbn" maxlength="13" value="<?php echo $isbn; ?>" required></div>
                <div class="form-fila"><label for="titulo">Título del libro:</label>
                <input type="text" id="titulo" name="titulo" value="<?php echo $titulo; ?>" required></div>
                <div class="form-fila"><label for="autor">Autor:</label>
                <input type="text" id="autor" name="autor" value="<?php echo $autor; ?>" required></div>
                <div class="form-fila">
                    <label for="genero">Género:</label>
                    <select name="genero" id="genero" required>
                        <option value="">Seleccione un género</option>
                        <option value="Ficción">Ficción</option>
                        <option value="No Ficción">No Ficción</option>
                        <option value="Ciencia">Ciencia</option>
                        <option value="Historia">Historia</option>
                        <option value="Romance">Romance</option>
                        <option value="Fantasía">Fantasía</option>
                    </select>
                </div>
                <div class="form-fila"><label for="anio">Año de publicación:</label>
                <input type="number" id="anio" name="anio" min="1900" max="2024" value="<?php echo $anio; ?>" required></div>
                <div class="form-fila"><label for="paginas">Número de páginas:</label>
                <input type="number" id="paginas" name="paginas" min="1" max="5000" value="<?php echo $paginas; ?>" required></div>
                <div class="opciones"><label>
                <input type="checkbox" name="disponible" <?php echo $disponible ? 'checked' : ''; ?>> Disponible</label></div>
                <div class="form-fila"><label for="cantidad">Cantidad en inventario:</label><input type="number" id="cantidad" name="cantidad" min="1" value="<?php echo $cantidad; ?>" required></div>
                <button type="submit">Registrar</button>
            </form>
        </section>
    </main>
    <footer>Universidad Tecnológica Costarricense, Programación IV</footer>
</body>

</html>