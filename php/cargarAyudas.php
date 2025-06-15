<?php
ob_start();

try {
    require_once("conexion.php"); // Archivo de conexión a la base de datos
    $conexion = new Conexion();
    $conectar = $conexion->conectar();

    // Consulta para obtener las ayudas
    $stmt = $conectar->prepare("SELECT * FROM ayudas ORDER BY fecha_publicacion DESC");
    $stmt->execute();
    $ayudas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json; charset=utf-8');

    if (empty($ayudas)) {
        echo json_encode(["error" => "No hay ayudas disponibles."]);
        exit;
    }

    // Si hay campo imagen y es binario, codificarlo para JSON
    foreach ($ayudas as &$ayuda) {
        if (!empty($ayuda['imagen'])) {
            // Ajusta el tipo MIME si sabes que es png, jpg, etc.
            $ayuda['imagen'] = 'data:image/jpeg;base64,' . base64_encode($ayuda['imagen']);
        } else {
            $ayuda['imagen'] = '';
        }
    }

    echo json_encode($ayudas);
    exit;
} catch (PDOException $e) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["error" => "Error al conectar con la base de datos: " . $e->getMessage()]);
    exit;
}

$output = ob_get_clean();
if (!empty(trim($output))) {
    error_log("Salida inesperada en cargarAyudas.php: " . $output);
}
