<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/controllers/SistemaController.php';

session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$controlador = new SistemaController();
$datosVista = $controlador->ejecutar();

extract($datosVista, EXTR_SKIP);

require __DIR__ . '/../app/views/sistema/index.php';
