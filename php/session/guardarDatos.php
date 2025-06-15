<?php
require_once("../conexion.php");
$conexion = new Conexion();
$conectar = $conexion->conectar();

// Validar sesión admin
if (isset($_COOKIE['nombre_usuario'])) {
    session_name("session_" . $_COOKIE['nombre_usuario']);
}
session_start();

$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$tipo_usuario = $_SESSION['tipo_usuario'] ?? null;
if (!$logged_in || $tipo_usuario !== 'admin') {
    http_response_code(403);
    echo "Acceso denegado.";
    exit;
}

$tipo = $_GET['tipo'] ?? null;
if ($tipo !== 'ayuda' && $tipo !== 'curso') {
    http_response_code(400);
    echo "Tipo no válido.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST[$tipo === 'ayuda' ? 'nombre_ayuda' : 'nombre_curso'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $enlace = trim($_POST[$tipo === 'ayuda' ? 'enlace' : 'enlace_curso'] ?? '');

    $errors = [];

    if (!$nombre) {
        $errors[] = "El nombre es obligatorio.";
    }

    if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "La imagen es obligatoria.";
    } else {
        // Limitar tamaño a 2MB, opcional
        if ($_FILES['imagen']['size'] > 2 * 1024 * 1024) {
            $errors[] = "La imagen es demasiado grande (máximo 2MB).";
        }
        $imgData = file_get_contents($_FILES['imagen']['tmp_name']);
    }

    if (empty($errors)) {
        try {
            if ($tipo === 'ayuda') {
                $stmt = $conectar->prepare("INSERT INTO ayudas (nombre_ayuda, descripcion, enlace, imagen) VALUES (:nombre, :descripcion, :enlace, :imagen)");
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':descripcion', $descripcion);
                $stmt->bindParam(':enlace', $enlace);
                $stmt->bindParam(':imagen', $imgData, PDO::PARAM_LOB);
                $stmt->execute();
            } else {
                $stmt = $conectar->prepare("INSERT INTO cursos (nombre_curso, descripcion, enlace_curso, imagen) VALUES (:nombre, :descripcion, :enlace, :imagen)");
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':descripcion', $descripcion);
                $stmt->bindParam(':enlace', $enlace);
                $stmt->bindParam(':imagen', $imgData, PDO::PARAM_LOB);
                $stmt->execute();
            }

            http_response_code(200);
            echo "Registro guardado correctamente.";
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error al guardar en la base de datos: " . $e->getMessage();
        }
    } else {
        http_response_code(400);
        echo implode("\n", $errors);
    }
} else {
    http_response_code(405);
    echo "Método no permitido.";
}
