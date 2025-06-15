<?php
    try {
        require_once("conexion.php");
        $conexion = new Conexion(); //nueva instancia de la clase conexión
        $conectar = $conexion->conectar();//objeto para ejecutar las consultas

        $user = $_POST['nombre_usuario'];//recibe el id del usuario
        $pass = $_POST['password'];//recibe la contraseña del usuario   

        $sql = "SELECT * FROM usuarios WHERE BINARY nombre_usuario = :usuario";
        $stmt = $conectar->prepare($sql);
        $stmt->execute([":usuario" => $user]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);     
        //Verifica si el usuario existe y si la contraseña es correcta
        if ($usuario) {
            // Verifica si la cuenta está activa
            if (!$usuario['activos']) {
                echo json_encode(["success" => false, "error" => "inactivo"]);
                exit;
            }
    
            // Verifica la contraseña
            if (password_verify($pass, $usuario['password'])) {
                // Asigna un nombre único a la sesión basado en el nombre de usuario
                session_name("session_" . $user);// Usa un hash del nombre de usuario como nombre de la sesión
                session_start(); // Inicia la sesión

                // Guarda los datos del usuario en la sesión
                $_SESSION['nombre_usuario'] = $usuario['nombre_usuario']; // Nombre del usuario
                $_SESSION['usuario_id'] = $usuario['id']; // Nombre del usuario
                $_SESSION['email'] = $usuario['email']; // Correo del usuario
                $_SESSION['tipo_usuario'] = $usuario['tipo_usuario']; // 'trabajador' o 'empresa'
                $_SESSION['logged_in'] = true; // Indica que el usuario está logueado
                // Guarda el nombre de usuario en una cookie (expira en 7 días)
                setcookie("nombre_usuario", $usuario['nombre_usuario'], time() + (7 * 24 * 60 * 60), "/");

                echo json_encode(["success" => true, "message" => "Inicio de sesión exitoso"]);
            } else {
                echo json_encode(["success" => false, "error" => "credenciales"]);
            }
        } else {
            echo json_encode(["success" => false, "error" => "credenciales"]);
        }
    } catch (Exception $e) {
        // Maneja el error de inicio de sesión
        echo json_encode(array("success" => false, "error" => "Error al iniciar sesión: " . $e->getMessage()));
        exit;
    }
    // Devuelve los datos en formato JSON
    
