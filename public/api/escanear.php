<?php
// Header para que las respuestas de este archivo se envíen en formato JSON
header('Content-Type: application/json; charset=utf-8');

// Se incluye la configuración de la base de datos y la función getConnection()
require_once __DIR__ . '/../../config/database.php';

// Datos usados para crear nombres de archivos falsos
$nombresBase = ['fantasma', 'oculto', 'registro', 'entidad', 'sombra', 'archivo'];
$extensiones = ['tmp', 'dat', 'log', 'bin', 'ghost', 'cache'];

// Cada escaneo genera entre dos y seis archivos
$cantidad = random_int(2, 6);
$usuario = 'Administrador';
$archivosGenerados = [];

try {
    $pdo = getConnection();

    // La transacción asegura que todo el escaneo se guarde completo o no se guarde nada
    $pdo->beginTransaction();

    // Primero se registra el escaneo general
    $stmtEscaneo = $pdo->prepare(
        'INSERT INTO escaneos (fecha, cantidad_archivos, usuario)
        VALUES (NOW(), ?, ?)'
    );
    $stmtEscaneo->execute([$cantidad, $usuario]);
    $escaneoId = (int) $pdo->lastInsertId();

    // Esta consulta se reutiliza para insertar cada archivo detectado
    $stmtArchivo = $pdo->prepare(
        'INSERT INTO archivos (nombre, tamano, fecha_detectado, peligroso, escaneo_id)
        VALUES (?, ?, ?, 0, ?)'
    );

    for ($i = 0; $i < $cantidad; $i++) {
        // Se forma un nombre único combinando texto, bytes aleatorios y una extensión
        $nombre = $nombresBase[array_rand($nombresBase)]
            . '_'
            . bin2hex(random_bytes(3))
            . '.'
            . $extensiones[array_rand($extensiones)];

        // El tamaño se simula en kilobytes y la fecha es la actual
        $tamano = random_int(25, 15000);
        $fecha = date('Y-m-d H:i:s');

        $stmtArchivo->execute([$nombre, $tamano, $fecha, $escaneoId]);

        // Se guarda la información en un arreglo para devolverla al js
        $archivosGenerados[] = [
            'id' => (int) $pdo->lastInsertId(),
            'nombre' => $nombre,
            'tamano' => $tamano,
            'fecha_detectado' => $fecha,
            'peligroso' => 0,
            'escaneo_id' => $escaneoId
        ];
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'escaneo_id' => $escaneoId,
        'cantidad_archivos' => $cantidad,
        'archivos' => $archivosGenerados
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    // Si algo falla, se revierten los cambios pendientes
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'No se pudo completar el escaneo. Verifique la conexión con MySQL.'
    ], JSON_UNESCAPED_UNICODE);
}
?>