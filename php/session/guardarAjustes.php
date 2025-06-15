<?php
if (isset($_COOKIE['nombre_usuario'])) {
    session_name("session_" . $_COOKIE['nombre_usuario']);
}
session_start();

require_once("../conexion.php");
$conexion = new Conexion();
$conectar = $conexion->conectar();

$id_usuario = $_SESSION['id'] ?? null;

if (!$id_usuario) {
    die("Usuario no identificado.");
}

// Recoge los datos del formulario
$nombre_usuario = $_POST['nombre_usuario'] ?? null;
$experiencia = $_POST['experiencia_laboral'] ?? null;
$habilidades = $_POST['habilidades'] ?? null;
$descripcion = $_POST['descripcion_personal'] ?? null;
$educacion = $_POST['educacion'] ?? null;
$testimonio = $_POST['testimonio'] ?? null;

// Actualiza tabla usuarios solo si hay datos
$campos_usuarios = [];
$valores_usuarios = [];
if (!empty($nombre_usuario)) {
    $campos_usuarios[] = "nombre_usuario = ?";
    $valores_usuarios[] = $nombre_usuario;
}

if ($campos_usuarios) {
    $valores_usuarios[] = $id_usuario;
    $sql = "UPDATE usuarios SET " . implode(', ', $campos_usuarios) . " WHERE id = ?";
    $stmt = $conectar->prepare($sql);
    $stmt->execute($valores_usuarios);

    // ACTUALIZA SESIÓN Y COOKIE AQUÍ
    if (!empty($nombre_usuario)) {
        $_SESSION['nombre_usuario'] = $nombre_usuario;
        setcookie('nombre_usuario', $nombre_usuario, time() + 3600, '/');
    }
}

// Actualiza tabla perfiles solo si hay datos
$campos_perfil = [];
$valores_perfil = [];
if (!empty($experiencia)) {
    $campos_perfil[] = "experiencia_laboral = ?";
    $valores_perfil[] = $experiencia;
}
if (!empty($habilidades)) {
    $campos_perfil[] = "habilidades = ?";
    $valores_perfil[] = $habilidades;
}
if (!empty($descripcion)) {
    $campos_perfil[] = "descripcion_personal = ?";
    $valores_perfil[] = $descripcion;
}
if (!empty($educacion)) {
    $campos_perfil[] = "educacion = ?";
    $valores_perfil[] = $educacion;
}
if (!empty($testimonio)) {
    $campos_perfil[] = "testimonio = ?";
    $valores_perfil[] = $testimonio;
}
if ($campos_perfil) {
    $valores_perfil[] = $id_usuario;
    $sql = "UPDATE perfiles SET " . implode(', ', $campos_perfil) . " WHERE usuario_id = ?";
    $stmt = $conectar->prepare($sql);
    $stmt->execute($valores_perfil);
}

// Procesa el archivo CV si se subió uno nuevo
if (isset($_FILES['cv_archivo']) && $_FILES['cv_archivo']['error'] === UPLOAD_ERR_OK) {
    $cv_nombre = $_FILES['cv_archivo']['name'];
    $cv_tmp = $_FILES['cv_archivo']['tmp_name'];
    $cv_destino = "../../uploads/cv_" . $id_usuario . "_" . basename($cv_nombre);
    move_uploaded_file($cv_tmp, $cv_destino);

    // Guarda la ruta o nombre del archivo en la base de datos
    $stmt = $conectar->prepare("UPDATE perfiles SET cv = ? WHERE usuario_id = ?");
    $stmt->execute([$cv_destino, $id_usuario]);
}

if (!empty($nombre_usuario) && $_COOKIE['nombre_usuario'] !== $nombre_usuario) {
    // Cambió el nombre, actualiza sesión y cookie
    $_SESSION['nombre_usuario'] = $nombre_usuario;
    $_SESSION['id'] = $id_usuario;
    setcookie('nombre_usuario', $nombre_usuario, time() + 3600, '/');
}
header("Location: perfil.php");
exit;
