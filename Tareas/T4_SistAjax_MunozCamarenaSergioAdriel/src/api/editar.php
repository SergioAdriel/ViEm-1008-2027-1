<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../db.php';

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
$texto = trim($_POST['texto'] ?? '');
$numero = filter_var($_POST['numero'] ?? null, FILTER_VALIDATE_INT);

if (!$id || $texto === '' || $numero === false || $numero === null) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'mensaje' => 'Datos inválidos.']);
    exit;
}

$stmt = $pdo->prepare("SELECT imagen FROM registros WHERE id = ?");
$stmt->execute([$id]);
$actual = $stmt->fetch();

if (!$actual) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'mensaje' => 'Registro no encontrado.']);
    exit;
}

$imagen = $actual['imagen'];

if (!empty($_FILES['imagen']['name'])) {
    $permitidas = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($_FILES['imagen']['type'], $permitidas, true)) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'mensaje' => 'Formato de imagen no permitido.']);
        exit;
    }

    $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
    $nuevaImagen = bin2hex(random_bytes(12)) . '.' . $ext;

    if (move_uploaded_file($_FILES['imagen']['tmp_name'], __DIR__ . '/../uploads/' . $nuevaImagen)) {
        if ($imagen !== 'default.svg' && is_file(__DIR__ . '/../uploads/' . $imagen)) {
            unlink(__DIR__ . '/../uploads/' . $imagen);
        }
        $imagen = $nuevaImagen;
    }
}

$stmt = $pdo->prepare("UPDATE registros SET texto = ?, numero = ?, imagen = ? WHERE id = ?");
$stmt->execute([$texto, $numero, $imagen, $id]);

echo json_encode([
    'ok' => true,
    'mensaje' => 'Registro actualizado.',
    'registro' => [
        'id' => $id,
        'texto' => $texto,
        'numero' => $numero,
        'imagen' => $imagen
    ]
], JSON_UNESCAPED_UNICODE);
?>
