<?php
ob_start(); // Captura cualquier salida no deseada
require_once("conexion.php");

$conexion = new Conexion();
$conectar = $conexion->conectar();

// Inicializa la variable $response
$response = [];

// Recibe los datos del usuario
$user = $_POST['usuario'];
$pass = $_POST['password'];
$tipo_usuario = $_POST['tipo_usuario'];
$correo = $_POST['correo'];

// Verifica que los datos no estén vacíos
if (!$user || !$pass || !$tipo_usuario || !$correo) {
    $response["error"] = "Datos incompletos";
    echo json_encode($response);
    exit;
}

// Verifica si el usuario ya existe
$sql = "SELECT * FROM usuarios WHERE BINARY nombre_usuario = :zuser OR email = :zcorreo";
$pdo = $conectar->prepare($sql);
$pdo->execute([":zuser" => $user, ":zcorreo" => $correo]);

if ($pdo->fetch(PDO::FETCH_ASSOC)) {
    $response["error"] = "El usuario o el correo ya existen";
    echo json_encode($response);
    exit;
}

// Inserta el nuevo usuario en la base de datos
setcookie("correo_verificacion", $correo, time() + 300, "/", "", false, false); // Expira en 5 minutos, sin secure y httponly
$hashed_password = password_hash($pass, PASSWORD_DEFAULT); // Cifra la contraseña

$sql = "INSERT INTO usuarios (nombre_usuario, password, tipo_usuario, email, activos) 
        VALUES (:zuser, :zpassword, :ztipo_usuario, :zcorreo, FALSE)";
$pdo = $conectar->prepare($sql);

if ($pdo->execute([
    ":zuser" => $user,
    ":zpassword" => $hashed_password,
    ":ztipo_usuario" => $tipo_usuario,
    ":zcorreo" => $correo
])) {
    $usuario_id = $conectar->lastInsertId();

    // Si el usuario es una empresa, se inserta en la tabla `empresas`
    if ($tipo_usuario === 'empresa') {
        $nombre_empresa = $_POST['nombre_empresa'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $correo_contacto = $_POST['correo_contacto'] ?? '';
        $stmt = $conectar->prepare("INSERT INTO empresas (usuario_id, nombre_empresa, descripcion, direccion, telefono, correo_contacto) 
                                    VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$usuario_id, $nombre_empresa, $descripcion, $direccion, $telefono, $correo_contacto]);
    }

    // Inserta un perfil vacío para el usuario
    $stmt = $conectar->prepare("INSERT INTO perfiles (usuario_id, descripcion_personal, experiencia_laboral, habilidades, educacion, cv, testimonio) 
                                VALUES (?, '', '', '', '', '', '')");
    $stmt->execute([$usuario_id]);

    $response = ["success" => true, "message" => "Usuario registrado exitosamente. Verifica tu correo."];
} else {
    $response = ["success" => false, "error" => "Error al registrar el usuario"];
}

// Devuelve la respuesta en formato JSON
echo json_encode($response);
