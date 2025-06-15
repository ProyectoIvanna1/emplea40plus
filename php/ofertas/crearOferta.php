<?php
try {
    require_once("conexion.php"); // Archivo de conexión a la base de datos
    $conexion = new Conexion();
    $conectar = $conexion->conectar();

    // Si existe la cookie, usa su valor para configurar el nombre de la sesión
    if (isset($_COOKIE['nombre_usuario'])) {
        session_name("session_" . $_COOKIE['nombre_usuario']);
    }
    session_start();
        
    //busca por nombre y guarda el id en la sesión
    if ($tipo_usuario === 'empresa') {
        $stmt = $conectar->prepare("SELECT * FROM usuarios WHERE nombre_usuario = ?");
    } else {
        $stmt = $conectar->prepare("SELECT * FROM usuarios WHERE nombre_usuario = ?");
    }
    $stmt->execute([$nombre_usuario]);
    $datos_usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    $id_usuario = $datos_usuario['id'] ?? null;
    if ($id_usuario) {
        $_SESSION['id'] = $id_usuario;
    }

    // Recoge los datos del formulario
    $empresa_id = $_SESSION['id']; // Aquí podrías obtener el `empresa_id` desde la sesión o un dato del usuario
    $titulo = $_POST['titulo'] ?? null;
    $descripcion = $_POST['descripcion'] ?? null;
    $requisitos = $_POST['requisitos'] ?? null;
    $ubicacion = $_POST['ubicacion'] ?? null;
    $salario = $_POST['salario'] ?? null;
    $tipo_horario = $_POST['tipo_horario'] ?? null;
    $fecha_publicacion = date('Y-m-d H:i:s'); // Se genera automáticamente

    // Verificar que los datos requeridos están presentes
    if (!$titulo || !$descripcion || !$ubicacion || !$tipo_horario) {
        die(json_encode([
            "success" => false,
            "error" => "Todos los campos requeridos deben estar completos."
        ]));
    }

    // Consulta para insertar una nueva oferta de empleo
    $sql = "INSERT INTO ofertas_empleo (empresa_id, titulo, descripcion, requisitos, ubicacion, salario, tipo_horario, fecha_publicacion) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conectar->prepare($sql);
    $stmt->execute([$empresa_id, $titulo, $descripcion, $requisitos, $ubicacion, $salario, $tipo_horario, $fecha_publicacion]);

    // Devuelve una respuesta en JSON
    echo json_encode([
        "success" => true,
        "message" => "Oferta de empleo creada correctamente."
    ]);

} catch (Exception $e) {
    // Maneja errores y devuelve un mensaje en formato JSON
    echo json_encode([
        "success" => false,
        "error" => "Error al crear la oferta: " . $e->getMessage()
    ]);
}
