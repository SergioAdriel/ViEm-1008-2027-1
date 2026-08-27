<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../db.php';

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'mensaje' => 'ID inválido.']);
    exit;
}

$stmt = $pdo->prepare("SELECT imagen FROM registros WHERE id = ?");
$stmt->execute([$id]);
$registro = $stmt->fetch();

if (!$registro) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'mensaje' => 'Registro no encontrado.']);
    exit;
}

$stmt = $pdo->prepare("DELETE FROM registros WHERE id = ?");
$stmt->execute([$id]);

if ($registro['imagen'] !== 'default.svg' && is_file(__DIR__ . '/../uploads/' . $registro['imagen'])) {
    unlink(__DIR__ . '/../uploads/' . $registro['imagen']);
}

echo json_encode(['ok' => true, 'mensaje' => 'Registro eliminado.']);
?>
