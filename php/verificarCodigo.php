<?php
    if (!isset($_POST['codigo']) || !isset($_POST['correo'])) {
        echo json_encode(["success" => false, "error" => "Código o correo no proporcionado"]);
        exit;
    }

    $codigoIngresado = $_POST['codigo'];
    $correo = $_POST['correo'];

    // Configura una sesión única basada en el correo
    session_name(md5($correo)); // Usa el mismo hash del correo como nombre de la sesión
    session_start();

    // Recupera el código y el timestamp de la sesión
    $codigoGuardado = $_SESSION['codigo_verificacion'] ?? null;
    $timestamp = $_SESSION['timestamp'] ?? null;

    if ($codigoGuardado && $timestamp) {
        // Verifica si el código es correcto y no ha expirado (5 minutos de validez)
        if ($codigoIngresado == $codigoGuardado && (time() - $timestamp) <= 300) {
            // Conexión a la base de datos
            require_once("conexion.php");
            $conexion = new Conexion();
            $conectar = $conexion->conectar();

            // Actualiza la columna 'activos' a TRUE(1)
            $sql = "UPDATE usuarios SET activos = TRUE WHERE email = :zcorreo";
            $stmt = $conectar->prepare($sql);
            $stmt->execute([":zcorreo" => $correo]);

            // Limpia los datos de la sesión
            session_destroy();

            echo json_encode(["success" => true, "message" => "Cuenta activada correctamente"]);
        } else {
            echo json_encode(["success" => false, "error" => "El código es incorrecto o ha expirado"]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "No se encontró un código válido"]);
    }