<?php
require_once __DIR__ . '/../config/config.php';
session_start();

require_once __DIR__ . '/../app/controllers/AuthController.php';

AuthController::cerrarSesion();
