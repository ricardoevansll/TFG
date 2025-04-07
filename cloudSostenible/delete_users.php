<?php
require_once 'auth.php';
require_once 'db_connect.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];
$is_admin = $_SESSION['rol'] === 'admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_admin) {
    try {
        $id = filter_input(INPUT_POST, 'iduser', FILTER_VALIDATE_INT);
        if (!$id) {
            $response['message'] = 'ID inválido';
        } else {
            $stmt = $pdo->prepare("DELETE FROM usuario WHERE iduser = ?");
            $stmt->execute([$id]);
            $response['success'] = true;
        }
    } catch (PDOException $e) {
        $response['message'] = $e->getMessage();
    }
} else {
    $response['message'] = 'No autorizado';
}

echo json_encode($response);