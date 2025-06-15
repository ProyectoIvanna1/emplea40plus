<?php
$user = $_COOKIE["nombre_usuario"]; // Obtiene el nombre de usuario de la cookie
session_name("session_" . $user);
session_start();

require_once("../conexion.php");

try {
    $conexion = new Conexion();
    $conectar = $conexion->conectar();

    // Buscar al usuario y guardar su ID en la sesión
    $stmt = $conectar->prepare("SELECT id FROM usuarios WHERE nombre_usuario = ?");
    $stmt->execute([$_SESSION['nombre_usuario']]);
    $datos_usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    $id_usuario = $datos_usuario['id'] ?? null;

    if (!$id_usuario) {
        die("Error: Usuario no encontrado.");
    }

    $_SESSION['id'] = $id_usuario;

    // **Realizamos una NUEVA consulta para buscar la empresa**
    $stmt = $conectar->prepare("SELECT id FROM empresas WHERE usuario_id = ?");
    $stmt->execute([$id_usuario]);
    $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$empresa) {
        die("Error: No se encontró una empresa asociada a este usuario.");
    }

    $empresa_id = $empresa['id'];

    // Recoger datos del formulario
    $nombre_empresa = $_POST['nombre_empresa'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $correo_contacto = $_POST['correo_contacto'] ?? '';

    // Inserta la nueva oferta en la base de datos
    $sql = "INSERT INTO ofertas_empleo (empresa_id, titulo, descripcion, requisitos, ubicacion, salario, tipo_horario)
            VALUES (:empresa_id, :titulo, :descripcion, :requisitos, :ubicacion, :salario, :tipo_horario)";
    $stmt = $conectar->prepare($sql);
    $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
    $stmt->bindParam(':titulo', $_POST['titulo'], PDO::PARAM_STR);
    $stmt->bindParam(':descripcion', $_POST['descripcion'], PDO::PARAM_STR);
    $stmt->bindParam(':requisitos', $_POST['requisitos'], PDO::PARAM_STR);
    $stmt->bindParam(':ubicacion', $_POST['ubicacion'], PDO::PARAM_STR);
    $stmt->bindParam(':salario', $_POST['salario'], PDO::PARAM_STR);
    $stmt->bindParam(':tipo_horario', $_POST['tipo_horario'], PDO::PARAM_STR);

    $stmt->execute();

    // Mensaje no bloqueante en pantalla
    echo "<script>
        window.location.href = 'crearOferta.html';
      </script>";
} catch (Exception $e) {
    // Muestra un mensaje de error
    echo "<script>
        alert('Error al crear la oferta: " . $e->getMessage() . "');
        window.location.href = 'crearOferta.php';
    </script>";
}
