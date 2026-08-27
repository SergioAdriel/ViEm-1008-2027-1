<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../db.php';

$texto = trim($_POST['texto'] ?? '');
$numero = filter_var($_POST['numero'] ?? null, FILTER_VALIDATE_INT);

if ($texto === '' || mb_strlen($texto) > 300 || $numero === false || $numero === null || $numero < 0 || $numero > 300) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'mensaje' => 'El texto debe tener hasta 300 caracteres y el número debe estar entre 0 y 300.']);
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
    $rutaImagen = __DIR__ . '/../uploads/' . $imagen;
    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaImagen)) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'mensaje' => 'No se pudo guardar la imagen.']);
        exit;
    }
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
