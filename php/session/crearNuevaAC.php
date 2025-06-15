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
    header("Location: ../session/indexLog.php");
    exit;
}

$tipo = $_GET['tipo'] ?? null;
if ($tipo !== 'ayuda' && $tipo !== 'curso') {
    die("Tipo no válido.");
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST[$tipo === 'ayuda' ? 'nombre_ayuda' : 'nombre_curso'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $enlace = trim($_POST[$tipo === 'ayuda' ? 'enlace' : 'enlace_curso'] ?? '');

    // Validar campos obligatorios
    if (!$nombre) {
        $errors[] = "El nombre es obligatorio.";
    }

    // Validar y procesar imagen subida
    if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "La imagen es obligatoria.";
    } else {
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
            $success = true;
        } catch (Exception $e) {
            $errors[] = "Error al guardar en la base de datos: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Crear <?= htmlspecialchars($tipo) ?></title>
    <link rel="stylesheet" href="../../css/estiloGeneral.css" />
    <link rel="stylesheet" href="../../css/estilosCrearNuevo.css" />
</head>
<body>
<header>
    <nav>
        <a href="admin.php"><img src="../../img/logoEmplea40plus.png" alt="Logo" id="logo" /></a>
        <button class="menu-toggle" onclick="document.getElementById('menu').classList.toggle('active');document.getElementById('esquina').classList.toggle('active');">☰</button>
        <ul id="menu">
            <li><a href="ayudascursosAdmin.php">Volver a Ayudas y Cursos</a></li>
        </ul>
    </nav>
</header>

<main>
    <h1>Crear nueva <?= htmlspecialchars($tipo) ?></h1>

    <?php if ($success): ?>
        <p style="color:green;">Registro creado con éxito.</p>
        <p><a href="ayudascursosAdmin.php">Volver a la lista</a></p>
    <?php else: ?>

        <?php if (!empty($errors)): ?>
            <ul style="color:red;">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <label for="nombre"><?= $tipo === 'ayuda' ? 'Nombre de la ayuda' : 'Nombre del curso' ?> *</label><br />
            <input type="text" name="<?= $tipo === 'ayuda' ? 'nombre_ayuda' : 'nombre_curso' ?>" id="nombre" required value="<?= htmlspecialchars($_POST[$tipo === 'ayuda' ? 'nombre_ayuda' : 'nombre_curso'] ?? '') ?>" /><br />

            <label for="descripcion">Descripción</label><br />
            <textarea name="descripcion" id="descripcion"><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea><br />

            <label for="enlace"><?= $tipo === 'ayuda' ? 'Enlace' : 'Enlace del curso' ?></label><br />
            <input type="url" name="<?= $tipo === 'ayuda' ? 'enlace' : 'enlace_curso' ?>" id="enlace" value="<?= htmlspecialchars($_POST[$tipo === 'ayuda' ? 'enlace' : 'enlace_curso'] ?? '') ?>" /><br />

            <label for="imagen">Imagen *</label><br />
            <input type="file" name="imagen" id="imagen" accept="image/*" required /><br /><br />
            <small>Tamaño máximo: 2MB</small>

            <button type="submit">Crear <?= htmlspecialchars($tipo) ?></button>
        </form>

    <?php endif; ?>
</main>

<footer>
    <p>&copy; 2025 Emplea40+. Todos los derechos reservados.</p>
</footer>
</body>
</html>
