<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../db.php';

$stmt = $pdo->query("SELECT id, texto, numero, imagen FROM registros ORDER BY id DESC");
echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE);
?>
