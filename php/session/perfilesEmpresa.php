<?php
// Si existe la cookie, usa su valor para configurar el nombre de la sesión
if (isset($_COOKIE['nombre_usuario'])) {
    session_name("session_" . $_COOKIE['nombre_usuario']);
}
session_start();

// Verifica si el usuario está logueado
$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$nombre_usuario = $logged_in ? $_SESSION['nombre_usuario'] : ($_COOKIE['nombre_usuario'] ?? null);
$tipo_usuario = $logged_in ? $_SESSION['tipo_usuario'] : null; // 'trabajador', 'empresa', 'admin', etc.

// Carga el contenido del HTML de perfiles
$html = file_get_contents('../../log/perfilesEmpresasLog.html');
if ($html === false) {
    die("Error: No se pudo cargar el archivo HTML.");
}

// Lógica para los botones del header
if ($logged_in) {
    if ($tipo_usuario === 'empresa') {
        $botones = '<li><a href="../ofertas/crearOferta.html">Crear Oferta</a></li>
                    <li><a href="../misOfertas/misOfertas.php">Mis Ofertas</a></li>
                    <li><a href="perfil.php">Ver Perfil</a></li>
                    <li><a href="logout.php">Cerrar sesión</a></li>';
    } elseif ($tipo_usuario === 'admin') {
        $botones = '<li><a href="admin.php">Panel de administración</a></li>
                    <li><a href="perfil.php">Ver Perfil</a></li>
                    <li><a href="logout.php">Cerrar sesión</a></li>';
    } else {
        // Usuario trabajador u otro tipo
        $botones = '<li><a href="perfil.php">Ver Perfil</a></li>
                    <li><a href="logout.php">Cerrar sesión</a></li>';
    }
} else {
    $botones = '<li><a href="login.html">Iniciar sesión</a></li>
                <li><a href="registro.html">Registrarse</a></li>';
}

// Inserta los botones en el HTML
$html = str_replace('{{botones}}', $botones, $html);

// Muestra el HTML final
echo $html;

