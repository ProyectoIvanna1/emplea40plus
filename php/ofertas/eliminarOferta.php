<?php
header('Content-Type: application/json');
require_once('../conexion.php'); 
$conexion = new Conexion();
$conectar = $conexion->conectar();

// Verificar que se reciba el ID por POST
if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID no recibido']);
    exit;
}

$id = intval($_POST['id']);

try {
    // Preparar y ejecutar la eliminación
    $stmt = $conectar->prepare("DELETE FROM ofertas_empleo WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se encontró la oferta para eliminar']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
