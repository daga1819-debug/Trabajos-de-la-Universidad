<?php
/*
    Configuración general del proyecto.
    Más adelante se incluirán las credenciales de MySQL y la creación de la conexión PDO.
 */

define('APP_NAME', 'Viajar es Pura Vida');
define('APP_VERSION', '1.0.0 - Primera entrega');

/*
    Zona horaria de Costa Rica.
 */
date_default_timezone_set('America/Costa_Rica');

/*
    Configuración segura básica para la cookie de sesión.
    httponly evita que JavaScript lea la cookie.
    samesite ayuda a reducir solicitudes maliciosas desde otros sitios.
 */
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
