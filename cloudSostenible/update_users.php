<?php
require_once 'auth.php';
require_once 'db_connect.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];
$is_admin = $_SESSION['rol'] === 'admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_admin) {
    try {
        $id = filter_input(INPUT_POST, 'iduser', FILTER_VALIDATE_INT);
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $rol = $_POST['rol'];

        if (!$id || empty($nombre) || !filter_var($email, FILTER_VALIDATE_EMAIL) || !in_array($rol, ['admin', 'usuario'])) {
            $response['message'] = 'Datos inválidos';
        } else {
            $stmt = $pdo->prepare("UPDATE usuario SET nombre = ?, email = ?, rol = ? WHERE iduser = ?");
            $stmt->execute([$nombre, $email, $rol, $id]);
            $response['success'] = true;
        }
    } catch (PDOException $e) {
        $response['message'] = $e->getMessage();
    }
} else {
    $response['message'] = 'No autorizado';
}

echo json_encode($response);