<?php
session_start(); // Iniciar sesión para manejar los datos de los libros
session_destroy(); // Destruir la sesión para eliminar todos los datos almacenados
header('Location: index.php?mensaje=Sesión reiniciada correctamente'); // Redirigir al inicio con un mensaje de éxito
exit();
?>
