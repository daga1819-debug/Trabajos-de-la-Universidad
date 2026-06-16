<?php
require_once 'includes/funciones.php'; // Iniciar sesión y cargar funciones necesarias
iniciarLibros();
// Obtener el ID del libro desde la URL y cambiar su disponibilidad
$id = isset($_GET['id']) ? (int)$_GET['id'] : -1;
if (isset($_SESSION['libros'][$id])) {
    $_SESSION['libros'][$id]['disponible'] = $_SESSION['libros'][$id]['disponible'] ? false : true;
    header('Location: index.php?mensaje=Disponibilidad actualizada correctamente');
    exit();
}
header('Location: index.php?mensaje=No se encontró el libro solicitado');
exit();
?>