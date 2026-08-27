<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../db.php';

$texto = trim($_POST['texto'] ?? '');
$numero = filter_var($_POST['numero'] ?? null, FILTER_VALIDATE_INT);

if ($texto === '' || $numero === false || $numero === null) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'mensaje' => 'Texto y número son obligatorios.']);
    exit;
}

$imagen = 'default.svg';

if (!empty($_FILES['imagen']['name'])) {
    $permitidas = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($_FILES['imagen']['type'], $permitidas, true)) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'mensaje' => 'Formato de imagen no permitido.']);
        exit;
    }

    $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
    $imagen = bin2hex(random_bytes(12)) . '.' . $ext;
    move_uploaded_file($_FILES['imagen']['tmp_name'], __DIR__ . '/../uploads/' . $imagen);
}

$stmt = $pdo->prepare("INSERT INTO registros (texto, numero, imagen) VALUES (?, ?, ?)");
$stmt->execute([$texto, $numero, $imagen]);

$id = (int)$pdo->lastInsertId();

echo json_encode([
    'ok' => true,
    'mensaje' => 'Registro creado.',
    'registro' => [
        'id' => $id,
        'texto' => $texto,
        'numero' => $numero,
        'imagen' => $imagen
    ]
], JSON_UNESCAPED_UNICODE);
?>
