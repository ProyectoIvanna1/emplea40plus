<?php
// Si existe la cookie, usa su valor para configurar el nombre de la sesión
if (isset($_COOKIE['nombre_usuario'])) {
    session_name("session_" . $_COOKIE['nombre_usuario']);
}
session_start();

// Verifica si el usuario está logueado
$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$nombre_usuario = $logged_in ? $_SESSION['nombre_usuario'] : ($_COOKIE['nombre_usuario'] ?? null);
$tipo_usuario = $logged_in ? $_SESSION['tipo_usuario'] : null; // 'trabajador' o 'empresa'

// Carga el HTML
$html = file_get_contents('../../log/misOfertas.html');  // Asegúrate de que este archivo existe
if ($html === false) {
    die("Error: No se pudo cargar el archivo HTML.");
}

// Reemplazo de variables
$html = str_replace('{{logged_in}}', $logged_in ? 'true' : 'false', $html);
$html = str_replace('{{nombre_usuario}}', htmlspecialchars($nombre_usuario ?? ''), $html);

// Botones según el tipo de usuario
if ($logged_in) {
    if ($tipo_usuario === 'empresa') {
        $botones = '<li><a href="../ofertas/crearOferta.html">Crear Oferta</a></li>
                    <li><a href="../ofertas/misofertas.php">Mis Ofertas</a></li>
                    <li><a href="../session/perfil.php">Ver Perfil</a></li>
                    <li><a href="../session/logout.php">Cerrar sesión</a></li>';
    } elseif ($tipo_usuario === 'admin') {
        $botones = '<li><a href="perfil.php">Ver Perfil</a></li>
                    <li><a href="admin.php">Panel de administración</a></li>
                    <li><a href="logout.php">Cerrar sesión</a></li>';
    } else {
        $botones = '<li><a href="perfil.php">Ver Perfil</a></li>
                    <li><a href="logout.php">Cerrar sesión</a></li>';
    }
    $mensaje_bienvenida = "<p>Hola, " . htmlspecialchars($nombre_usuario) . ". Aquí están tus ofertas.</p>";
} else {
    $botones = '<li><a href="login.html">Iniciar sesión</a></li><li><a href="registro.html">Registrarse</a></li>';
    $mensaje_bienvenida = '<a href="registro.html" class="cta-button">Regístrate ahora</a>';
}

// Inserta contenido dinámico
$html = str_replace('{{botones}}', $botones, $html);
$html = str_replace('{{mensaje_bienvenida}}', $mensaje_bienvenida, $html);

// Muestra el HTML procesado
echo $html;
