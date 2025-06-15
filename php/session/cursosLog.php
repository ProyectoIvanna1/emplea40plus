<?php
// Si existe la cookie, usa su valor para configurar el nombre de la sesión
if (isset($_COOKIE['nombre_usuario'])) {
    session_name("session_" . $_COOKIE['nombre_usuario']);
}
session_start();

// Verifica si el usuario está logueado
$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$nombre_usuario = $logged_in ? $_SESSION['nombre_usuario'] : ($_COOKIE['nombre_usuario'] ?? null);
$tipo_usuario = $logged_in ? $_SESSION['tipo_usuario'] : null; // 'trabajador', 'empresa', 'admin'

// Carga el HTML base
$html = file_get_contents('../../log/cursosLog.html');
if ($html === false) {
    die("Error: No se pudo cargar el archivo HTML.");
}

// Reemplaza marcadores de sesión
$html = str_replace('{{logged_in}}', $logged_in ? 'true' : 'false', $html);
$html = str_replace('{{nombre_usuario}}', htmlspecialchars($nombre_usuario ?? ''), $html);

// Reemplaza los botones de navegación
if ($logged_in) {
    if ($tipo_usuario === 'empresa') {
        $botones = '<li><a href="../ofertas/crearOferta.html">Crear Oferta</a></li>
                    <li><a href="../ofertas/misOfertas.php">Mis Ofertas</a></li>
                    <li><a href="perfil.php">Ver Perfil</a></li>
                    <li><a href="logout.php">Cerrar sesión</a></li>';
    } elseif ($tipo_usuario === 'admin') {
        $botones = '<li><a href="perfil.php">Ver Perfil</a></li>
                    <li><a href="admin.php">Panel de administración</a></li>
                    <li><a href="logout.php">Cerrar sesión</a></li>';
    } else {
        $botones = '<li><a href="perfil.php">Ver Perfil</a></li>
                    <li><a href="logout.php">Cerrar sesión</a></li>';
    }
} else {
    $botones = '<li><a href="login.html">Iniciar sesión</a></li>
                <li><a href="registro.html">Registrarse</a></li>';
}

$html = str_replace('{{botones}}', $botones, $html);

// Muestra el HTML procesado
echo $html;
