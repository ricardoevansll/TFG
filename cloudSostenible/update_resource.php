<?php
require_once 'db_connect.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = filter_input(INPUT_POST, 'id_recurso', FILTER_VALIDATE_INT);
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
        $tipo = $_POST['tipo'];
        $estado = $_POST['estado'];
        $id_energia = filter_input(INPUT_POST, 'id_energia', FILTER_VALIDATE_INT) ?: null;
        $energia_renovable = isset($_POST['energia_renovable']) ? 1 : 0;

        if (!$id || empty($nombre) || !in_array($tipo, ['servidor', 'db', 'otro']) || !in_array($estado, ['activo', 'inactivo'])) {
            $response['message'] = 'Datos inválidos';
        } else {
            $stmt = $pdo->prepare("UPDATE recurso SET nombre = ?, tipo = ?, estado = ?, id_energia = ?, energia_renovable = ? WHERE id_recurso = ?");
            $stmt->execute([$nombre, $tipo, $estado, $id_energia, $energia_renovable, $id]);
            $response['success'] = true;
        }
    }
} catch (PDOException $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);