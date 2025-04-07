<?php
require_once 'db_connect.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = filter_input(INPUT_POST, 'id_energia', FILTER_VALIDATE_INT);
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
        $renovable = isset($_POST['renovable']) ? 1 : 0;

        if (!$id || empty($nombre)) {
            $response['message'] = 'Datos inválidos';
        } else {
            $stmt = $pdo->prepare("UPDATE fuente SET nombre = ?, renovable = ? WHERE id_energia = ?");
            $stmt->execute([$nombre, $renovable, $id]);
            $response['success'] = true;
        }
    }
} catch (PDOException $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);