<?php

try {
    require_once("conexion.php"); // Archivo de conexión a la base de datos
    $conexion = new Conexion();
    $conectar = $conexion->conectar();

    // Consulta para obtener las ofertas de empleo
    $sql = "SELECT id, titulo, descripcion, requisitos, ubicacion, tipo_horario, fecha_publicacion FROM ofertas_empleo";
    $stmt = $conectar->prepare($sql);
    $stmt->execute();

    $ofertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devuelve las ofertas en formato JSON
    echo json_encode([
        "success" => true,
        "ofertas" => $ofertas
    ]);
} catch (Exception $e) {
    // Maneja errores y devuelve un mensaje en formato JSON
    echo json_encode([
        "success" => false,
        "error" => "Error al obtener las ofertas: " . $e->getMessage()
    ]);
}