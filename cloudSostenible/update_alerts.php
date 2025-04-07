<?php
require_once 'db_connect.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = filter_input(INPUT_POST, 'id_alerta', FILTER_VALIDATE_INT);
        $id_usuario = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
        $id_recurso = filter_input(INPUT_POST, 'id_recurso', FILTER_VALIDATE_INT);
        $umbral_pue = filter_input(INPUT_POST, 'umbral_pue', FILTER_VALIDATE_FLOAT);
        $umbral_carbono = filter_input(INPUT_POST, 'umbral_carbono', FILTER_VALIDATE_FLOAT);

        if (!$id || !$id_usuario || !$id_recurso || $umbral_pue === false || $umbral_carbono === false) {
            $response['message'] = 'Datos inválidos';
        } else {
            $stmt = $pdo->prepare("UPDATE alertas SET id_usuario = ?, id_recurso = ?, umbral_pue = ?, umbral_carbono = ? WHERE id_alerta = ?");
            $stmt->execute([$id_usuario, $id_recurso, $umbral_pue, $umbral_carbono, $id]);
            $response['success'] = true;
        }
    }
} catch (PDOException $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);