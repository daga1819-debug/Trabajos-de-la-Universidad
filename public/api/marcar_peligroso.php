<?php
// Header para recibir y devolver datos en formato JSON
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/database.php';

// Lee el cuerpo JSON enviado por fetch desde js
$datos = json_decode(file_get_contents('php://input'), true);
$id = isset($datos['id']) ? (int) $datos['id'] : 0;

// No se permite continuar si el identificador no es válido
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID inválido.']);
    exit;
}

try {
    $pdo = getConnection();

    // Consulta el estado actual para poder alternarlo
    $stmt = $pdo->prepare('SELECT peligroso FROM archivos WHERE id = ?');
    $stmt->execute([$id]);
    $archivo = $stmt->fetch();

    if (!$archivo) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Archivo no encontrado.']);
        exit;
    }

    // Si era peligroso pasa a normal y vicieversa
    $nuevoEstado = (int) $archivo['peligroso'] === 1 ? 0 : 1;

    $stmt = $pdo->prepare('UPDATE archivos SET peligroso = ? WHERE id = ?');
    $stmt->execute([$nuevoEstado, $id]);

    echo json_encode([
        'success' => true,
        'id' => $id,
        'peligroso' => $nuevoEstado
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'No fue posible actualizar el archivo.'
    ]);
}
?>