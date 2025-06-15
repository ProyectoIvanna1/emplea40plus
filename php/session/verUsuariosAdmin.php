<?php
require_once("../conexion.php");
$conexion = new Conexion();
$conectar = $conexion->conectar();

// Inicia sesión y verifica admin
if (isset($_COOKIE['nombre_usuario'])) {
    session_name("session_" . $_COOKIE['nombre_usuario']);
}
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../session/indexLog.php");
    exit;
}

// Obtener todos los usuarios
$stmt = $conectar->query("SELECT id, nombre_usuario, email, tipo_usuario, fecha_registro, activos FROM usuarios");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios Registrados</title>
    <link rel="stylesheet" href="../../css/estiloGeneral.css">
    <link rel="stylesheet" href="../../css/estiloUsuario.css">
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
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Email</th>
                <th>Tipo</th>
                <th>Registro</th>
                <th>Activo</th>
            </tr>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['id']) ?></td>
                    <td><?= htmlspecialchars($usuario['nombre_usuario']) ?></td>
                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                    <td><?= htmlspecialchars($usuario['tipo_usuario']) ?></td>
                    <td><?= htmlspecialchars($usuario['fecha_registro']) ?></td>
                    <td><?= $usuario['activos'] ? 'Sí' : 'No' ?></td>
                    <td><button class="btn-eliminar" data-id="<?= htmlspecialchars($usuario['id']) ?>">Eliminar</button></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </main>
</body>
</html>

<script src="../../js/jquery/jquery-3.7.1.js"></script>
<script>
$(document).ready(function() {
    $(".btn-eliminar").click(function() {
        const usuarioId = $(this).data("id");
        if (confirm("¿Seguro que quieres eliminar este usuario? Esta acción es irreversible.")) {
            $.ajax({
                url: 'eliminarUsuario.php',
                method: 'POST',
                data: { id: usuarioId },
                success: function(response) {
                    console.log("Respuesta cruda:", response);
                    if (response.success) {
                        alert("Usuario eliminado correctamente.");
                        $(`tr[data-id='${usuarioId}']`).remove();
                    } else {
                        alert("Error al eliminar: " + (response.message || "Error desconocido"));
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

