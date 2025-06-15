<?php
require_once("../conexion.php");
$conexion = new Conexion();
$conectar = $conexion->conectar();

// Establece el nombre de la sesión si existe la cookie
if (isset($_COOKIE['nombre_usuario'])) {
    session_name("session_" . $_COOKIE['nombre_usuario']);
}
session_start();

// Verifica si el usuario está logueado
$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$nombre_usuario = $logged_in ? $_SESSION['nombre_usuario'] : ($_COOKIE['nombre_usuario'] ?? null);

// Redirige si no está logueado
if (!$logged_in) {
    header("Location: login.php");
    exit;
}

// Obtener ID de usuario desde sesión o cookie
$id_usuario = $_SESSION['id'] ?? null;
if (!$id_usuario && $nombre_usuario) {
    $stmt = $conectar->prepare("SELECT id FROM usuarios WHERE nombre_usuario = ?");
    $stmt->execute([$nombre_usuario]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && isset($row['id'])) {
        $id_usuario = $row['id'];
        $_SESSION['id'] = $id_usuario;
    }
}

// Si no se pudo obtener el ID del usuario
if (!$id_usuario) {
    die("Error: No se pudo obtener el ID de usuario.");
}

// Obtener el ID de la empresa desde la tabla empresas
$stmt = $conectar->prepare("SELECT id FROM empresas WHERE usuario_id = ?");
$stmt->execute([$id_usuario]);
$empresa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$empresa || !isset($empresa['id'])) {
    die("Error: El usuario no está asociado a una empresa.");
}

$empresa_id = $empresa['id'];

// Obtener ofertas de empleo para esa empresa
$stmt = $conectar->prepare("SELECT * FROM ofertas_empleo WHERE empresa_id = ?");
$stmt->execute([$empresa_id]);
$ofertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Devolver el JSON
if ($ofertas) {
    echo json_encode(['success' => true, 'ofertas' => $ofertas]);
} else {
    echo json_encode(['success' => true, 'ofertas' => []]); // No hay ofertas, pero success es true
}
