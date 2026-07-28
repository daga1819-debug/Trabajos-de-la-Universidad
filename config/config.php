<?php
/*
    Configuración general de la aplicación
 */

define('APP_NAME', 'Viajar es Pura Vida');
define('APP_VERSION', '2.0.0 - Conexión MySQL');

/*
    Zona horaria de Costa Rica
 */
date_default_timezone_set('America/Costa_Rica');

/*
    Configuración de seguridad de las sesiones
 */
// Evita que JavaScript pueda acceder a la cookie de sesión
ini_set('session.cookie_httponly', '1');
// Reduce el riesgo de solicitudes provenientes de páginas externas
ini_set('session.cookie_samesite', 'Lax');
// En localhost se mantiene en 0 porque todavía no utilizamos HTTPS
ini_set('session.cookie_secure', '0');