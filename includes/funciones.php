<?php
session_start(); // Iniciar sesión para manejar los datos de los libros

// Constantes para la biblioteca
define('Nombre_Biblioteca', 'La Biblioteca de Kenay');
define('Max_ISBN', 13);
define('Anio_Min', 1900);
define('Anio_Max', 2024);
define('Paginas_Min', 1);
define('Paginas_Max', 5000);

// Función para inicializar los libros con datos de ejemplo
function iniciarLibros()
{
    if (!isset($_SESSION['libros'])) {
        $_SESSION['libros'] = [];
        array_push($_SESSION['libros'], [
            'isbn' => '9783625125697',
            'titulo' => 'Las aventuras de Kenay',
            'autor' => 'Eugenio Jiménez',
            'genero' => 'Fantasía',
            'año' => 2019,
            'paginas' => 115,
            'disponible' => true,
            'cantidad' => 8
        ]);
        array_push($_SESSION['libros'], [
            'isbn' => '9789653481234',
            'titulo' => 'El platillo favorito de Kenay',
            'autor' => 'Yolanda Ávalos',
            'genero' => 'No Ficción',
            'año' => 2023,
            'paginas' => 60,
            'disponible' => true,
            'cantidad' => 2
        ]);
        array_push($_SESSION['libros'], [
            'isbn' => '9784362515697',
            'titulo' => 'Kenay y sus amigos',
            'autor' => 'Gabriel Jiménez',
            'genero' => 'Historia',
            'año' => 2024,
            'paginas' => 370,
            'disponible' => false,
            'cantidad' => 0
        ]);
    }
}

// Función para limpiar datos de entrada
function limpiarDato($dato)
{
    return htmlspecialchars(trim($dato));
}

// Función para verificar si un ISBN ya existe
function isbnExiste($isbn)
{
    foreach ($_SESSION['libros'] as $libro) {
        if ($libro['isbn'] == $isbn) {
            return true;
        }
    }
    return false;
}

// Función para agregar un nuevo libro al inventario
function agregarLibro($libro)
{
    array_push($_SESSION['libros'], $libro);
}

// Función para mostrar el texto de disponibilidad
function textoDisponible($estado)
{
    return $estado ? 'Disponible' : 'No disponible';
}

// Función para obtener la clase CSS de disponibilidad
function claseDisponible($estado)
{
    return $estado ? 'badge disponible' : 'badge no-disponible';
}
?>