<?php
header('Content-Type: application/json');
require_once("../conexion.php");

$conexion = new Conexion();
$conectar = $conexion->conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['tipo'])) {
    $id = intval($_POST['id']);
    $tipo = $_POST['tipo'];

    try {
        if ($tipo !== 'curso' && $tipo !== 'ayuda') {
            throw new Exception("Tipo no válido. Solo 'curso' o 'ayuda' permitido.");
        }

        $tabla = $tipo === 'curso' ? 'cursos' : 'ayudas';

        $stmt = $conectar->prepare("DELETE FROM $tabla WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró el recurso.']);
        }

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida o datos incompletos.']);
}
?>
