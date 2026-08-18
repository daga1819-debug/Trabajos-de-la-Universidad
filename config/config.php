<?php

// Configuración general compartida por todas las páginas de la aplicación
define('APP_NAME', 'Viajar es Pura Vida');

date_default_timezone_set('America/Costa_Rica');

// Endurece la cookie de sesión durante el desarrollo local
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
// En localhost se mantiene desactivado porque XAMPP normalmente trabaja sin HTTPS
ini_set('session.cookie_secure', '0');

/*
    Escapa texto antes de imprimirlo en HTML para evitar inyección de contenido
 */
function e(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

/*
    Convierte el valor almacenado en MySQL en una ruta pública utilizable por <img>
 */
function rutaImagen(?string $imagen, string $tipo = 'destinos'): string
{
    $imagen = trim((string) $imagen);

    $imagenesPredeterminadas = [
        'destinos' => 'assets/img/arenal.jpg',
        'hoteles' => 'assets/img/hotel-eco.png',
        'actividades' => 'assets/img/actividad-aventura.png',
        'perfiles' => 'assets/img/arenal.jpg',
    ];

    if ($imagen === '') {
        return $imagenesPredeterminadas[$tipo]
            ?? $imagenesPredeterminadas['destinos'];
    }

    return str_contains($imagen, '/')
        ? $imagen
        : 'assets/img/' . $imagen;
}
