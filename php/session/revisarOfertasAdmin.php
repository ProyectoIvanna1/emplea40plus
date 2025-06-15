<?php
require_once("../conexion.php");
$conexion = new Conexion();
$conectar = $conexion->conectar();

if (isset($_COOKIE['nombre_usuario'])) {
    session_name("session_" . $_COOKIE['nombre_usuario']);
}
session_start();

// Verificación de sesión y tipo admin
if (!isset($_SESSION['logged_in']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../session/indexLog.php");
    exit;
}

// Consulta para obtener las ofertas y el nombre de la empresa asociada
$sql = "SELECT oe.id, oe.titulo, e.nombre_empresa AS empresa, oe.descripcion, oe.fecha_publicacion
        FROM ofertas_empleo oe
        JOIN empresas e ON oe.empresa_id = e.id
        ORDER BY oe.fecha_publicacion DESC";

$stmt = $conectar->prepare($sql);
$stmt->execute();
$ofertas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ofertas Publicadas</title>
    <link rel="stylesheet" href="../../css/estiloGeneral.css">
    <link rel="stylesheet" href="../../css/estiloRevOfertas.css">
</head>
<body>
    <header>
        <nav>
            <a href="indexLog.php"><img src="../../img/logoEmplea40plus.png" alt="Logo de Emplea40+" id="logo"></a>
            <button class="menu-toggle" onclick="document.getElementById('menu').classList.toggle('active');document.getElementById('esquina').classList.toggle('active');">☰</button>
            <ul id="menu">
                <li><a href="admin.php">Panel de Administración</a></li>
            </ul>
        </nav>
    </header>
    <main style="padding: 20px;">
        <?php if (count($ofertas) > 0): ?>
            <table border="1" cellpadding="10" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Empresa</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ofertas as $oferta): ?>
                        <tr>
                            <td><?= $oferta['id'] ?></td>
                            <td><?= htmlspecialchars($oferta['titulo']) ?></td>
                            <td><?= htmlspecialchars($oferta['empresa']) ?></td>
                            <td><?= htmlspecialchars($oferta['descripcion']) ?></td>
                            <td><?= $oferta['fecha_publicacion'] ?></td>
                            <td>
                                <button class="btn-eliminar" data-id="<?= $oferta['id'] ?>">Eliminar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay ofertas publicadas en el sistema.</p>
        <?php endif; ?>
    </main>
</body>
</html>

<script src="../../js/jquery/jquery-3.7.1.js"></script>
<script>
$(document).ready(function() {
  $(".btn-eliminar").click(function() {
    const ofertaId = $(this).data("id");
    if (confirm("¿Seguro que quieres eliminar esta oferta?")) {
      $.ajax({
        url: '../ofertas/eliminarOferta.php',
        method: 'POST',
        data: { id: ofertaId },
        dataType: 'json', // Asegúrate de que jQuery espere JSON
        success: function(response) {
          console.log(response);
          if (response.success) {
            alert("Oferta eliminada correctamente.");
            // Remover la fila de la tabla
            $(`button[data-id='${ofertaId}']`).closest("tr").remove();
          } else {
            alert("No se pudo eliminar la oferta: " + (response.message || "Error desconocido"));
          }
        },
        error: function() {
          alert("Error en la conexión con el servidor.");
        }
      });
    }
  });
});
</script>

