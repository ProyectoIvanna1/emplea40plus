<?php
ob_start();

try {
    require_once("conexion.php");
    $conexion = new Conexion();
    $conectar = $conexion->conectar();

    $stmt = $conectar->prepare("SELECT id, nombre_curso, descripcion, enlace_curso, imagen, fecha_publicacion FROM cursos ORDER BY fecha_publicacion DESC");
    $stmt->execute();
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cursos as &$curso) {
        if (!empty($curso['imagen'])) {
            $curso['imagen'] = 'data:image/jpeg;base64,' . base64_encode($curso['imagen']);
        } else {
            $curso['imagen'] = '';
        }
    }

    header('Content-Type: application/json; charset=utf-8');

    if (empty($cursos)) {
        echo json_encode(["error" => "No hay cursos disponibles."]);
        exit;
    }

    echo json_encode($cursos);
    exit;
} catch (PDOException $e) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["error" => "Error al conectar con la base de datos: " . $e->getMessage()]);
    exit;
}

$output = ob_get_clean();
if (!empty(trim($output))) {
    error_log("Salida inesperada en cargarCursos.php: " . $output);
}
