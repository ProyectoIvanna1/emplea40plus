<?php
if (!isset($_GET['id'])) {
    die("Error: No se proporcionó el ID de la oferta.");
}

require_once("conexion.php");

$id = intval($_GET['id']); // Sanitiza el ID de la oferta

try {
    $conexion = new Conexion();
    $conectar = $conexion->conectar();

    // Consulta para obtener los detalles de la oferta
    $sql = "SELECT titulo, descripcion, requisitos, ubicacion, tipo_horario, fecha_publicacion FROM ofertas_empleo WHERE id = :id";
    $stmt = $conectar->prepare($sql);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    $oferta = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$oferta) {
        die("Error: No se encontró la oferta.");
    }
} catch (Exception $e) {
    die("Error al cargar los detalles de la oferta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Oferta</title>
    <link rel="stylesheet" href="../css/estiloGeneral.css"> 
    <link rel="stylesheet" href="../css/estilosDetalleOferta.css">
</head>
<body>
    <header>
        <nav>
            <a href="../index.html"><img src="../img/logoEmplea40plus.png" alt="Logo de Emplea40+" id="logo"></a>
            <button class="menu-toggle" onclick="document.getElementById('menu').classList.toggle('active');document.getElementById('esquina').classList.toggle('active');">☰</button>
            <ul id="menu">
                <li><a href="../index.html">Inicio</a></li>
                <li><a href="../cursos.html">Ayudas y cursos</a></li>
                <li><a href="../perfilesEmpresas.html">Perfiles y empresas</a></li>
            </ul>
            <ul id="esquina">
                <li><a href="../login.html">Iniciar sesión</a></li>
                <li><a href="../registro.html">Registrarse</a></li>
            </ul>
        </nav>
    </header>
    <div>
        <h1>Detalles de la Oferta</h1>
        <h2><?php echo htmlspecialchars($oferta['titulo']); ?></h2>
        <p><strong>Descripción:</strong> <?php echo htmlspecialchars($oferta['descripcion']); ?></p>
        <p><strong>Requisitos:</strong> <?php echo htmlspecialchars($oferta['requisitos']); ?></p>
        <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($oferta['ubicacion']); ?></p>
        <p><strong>Horario:</strong> <?php echo htmlspecialchars($oferta['tipo_horario']); ?></p>
        <p><strong>Fecha de publicación:</strong> <?php echo htmlspecialchars($oferta['fecha_publicacion']); ?></p>

        <h3>Enviar Archivos</h3>
        <form action="../envio.html" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="oferta_id" value="<?php echo $id; ?>">
            <label for="archivo">Selecciona un archivo:</label>
            <input type="file" name="archivo" id="archivo" accept=".pdf, .doc, .docx" required>
            <button type="submit">Enviar</button>
        </form>
    </div>
</body>
</html>