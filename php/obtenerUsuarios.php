<?php
require_once("conexion.php");

try {
    $conexion = new Conexion();
    $conectar = $conexion->conectar();

    $sql = "SELECT id, nombre_usuario, foto_perfil FROM usuarios";
    $stmt = $conectar->prepare($sql);
    $stmt->execute();

    $usuarios = [];

    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $fotoBlob = $fila['foto_perfil'];

        // Convierte el BLOB en base64 si no está vacío
        if (!empty($fotoBlob)) {
            $base64 = base64_encode($fotoBlob);
            $fotoSrc = 'data:image/jpeg;base64,' . $base64; // Ajusta a image/png si usas PNG
        } else {
            $fotoSrc = 'img/default-profile.png'; // Imagen por defecto
        }

        $usuarios[] = [
            'id' => $fila['id'],
            'nombre_usuario' => $fila['nombre_usuario'],
            'foto_src' => $fotoSrc
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($usuarios);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
