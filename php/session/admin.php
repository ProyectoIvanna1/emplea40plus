<?php
// Si existe la cookie, configura el nombre de la sesión
if (isset($_COOKIE['nombre_usuario'])) {
    session_name("session_" . $_COOKIE['nombre_usuario']);
}
session_start();

// Verifica si el usuario está logueado y es admin
$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$tipo_usuario = $_SESSION['tipo_usuario'] ?? null;

if (!$logged_in || $tipo_usuario !== 'admin') {
    // Redirige si no es admin
    header("Location: ../session/indexLog.php");
    exit;
}

$nombre_usuario = $_SESSION['nombre_usuario'] ?? 'Administrador';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - Emplea40+</title>
    <link rel="stylesheet" href="../../css/estiloGeneral.css">
    <link rel="stylesheet" href="../../css/estilosAdministracion.css">
    <script type="text/javascript" src="../../js/ofertasAdmin.js"></script>
</head>
<body>
    <header>
        <nav>
            <a href="indexLog.php"><img src="../../img/logoEmplea40plus.png" alt="Logo Emplea40+" id="logo"></a>
            <button class="menu-toggle" onclick="document.getElementById('menu').classList.toggle('active');document.getElementById('esquina').classList.toggle('active');">☰</button>
            <ul id="menu">
                <li><a href="indexLog.php">Inicio</a></li>
                <li><a href="admin.php">Panel Admin</a></li>
            </ul>
            <ul id="esquina">
                <li><a href="perfil.php">Ver Perfil</a></li>
                <li><a href="logout.php">Cerrar sesión</a></li>
            </ul>
        </nav>
    </header>

    <main style="padding: 30px;">
        <h1>Bienvenido al Panel de Administración</h1>
        <p>Hola, <strong><?php echo htmlspecialchars($nombre_usuario); ?></strong>. Desde aquí puedes gestionar el sistema.</p>

        <section class="admin-options">
            <ul>
                <li><a href="verUsuariosAdmin.php">Ver todos los usuarios</a></li>
                <li><a href="revisarOfertasAdmin.php">Revisar ofertas publicadas</a></li>
                <li><a href="ayudascursosAdmin.php">Revisar Ayudas y cursos</a></li>
            </ul>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Emplea40+. Todos los derechos reservados.</p>
    </footer>
</body>
</html>