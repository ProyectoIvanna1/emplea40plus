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


// Carga el contenido del HTML
$html = file_get_contents('../../log/perfil.html');
if ($html === false) {
    die("Error: No se pudo cargar el archivo HTML.");
}

require_once("../conexion.php");
$conexion = new Conexion();
$conectar = $conexion->conectar();

// coger id
$id_usuario = $_SESSION['id'] ?? null;
if (!$id_usuario && $nombre_usuario) {
    $stmt = $conectar->prepare("SELECT id FROM usuarios WHERE nombre_usuario = ?");
    $stmt->execute([$nombre_usuario]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && isset($row['id'])) {
        $id_usuario = $row['id'];
        $_SESSION['id'] = $id_usuario;
    }
}

// subida de footo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['nueva_foto']) && $logged_in && $id_usuario) {
    $error_code = $_FILES['nueva_foto']['error'];
    if ($error_code === UPLOAD_ERR_OK) {
        if ($_FILES['nueva_foto']['size'] > 2097152) { // 2MB en bytes
            $error_foto = "El archivo es demasiado grande. Máximo 2MB.";
        } else {
            $fotoTmp = $_FILES['nueva_foto']['tmp_name'];
            $fotoBinario = file_get_contents($fotoTmp);

            $stmt = $conectar->prepare("UPDATE usuarios SET foto_perfil = ? WHERE id = ?");
            $stmt->bindParam(1, $fotoBinario, PDO::PARAM_LOB);
            $stmt->bindParam(2, $id_usuario, PDO::PARAM_INT);
            $stmt->execute();

            header("Location: perfil.php");
            exit;
        }
    } elseif ($error_code === UPLOAD_ERR_INI_SIZE || $error_code === UPLOAD_ERR_FORM_SIZE) {
        $error_foto = "El archivo supera el tamaño máximo permitido por el servidor (2MB).";
    } elseif ($error_code !== UPLOAD_ERR_NO_FILE) {
        $error_foto = "Error al subir la imagen. Código: $error_code";
    }
}

$datos_usuario = [];
$id_usuario = $_SESSION['id'] ?? null;

if ($logged_in) {
    if ($id_usuario) {
        // Si ya tienes el id, busca por id
        if ($tipo_usuario === 'empresa') {
            $stmt = $conectar->prepare("SELECT * FROM usuarios WHERE id = ?");
        } else {
            $stmt = $conectar->prepare("SELECT * FROM usuarios WHERE id = ?");
        }
        $stmt->execute([$id_usuario]);
        $datos_usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    } elseif ($nombre_usuario) {
        // Si no tienes id, busca por nombre y guarda el id en la sesión
        if ($tipo_usuario === 'empresa') {
            $stmt = $conectar->prepare("SELECT * FROM usuarios WHERE nombre_usuario = ?");
        } else {
            $stmt = $conectar->prepare("SELECT * FROM usuarios WHERE nombre_usuario = ?");
        }
        $stmt->execute([$nombre_usuario]);
        $datos_usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_usuario = $datos_usuario['id'] ?? null;
        if ($id_usuario) {
            $_SESSION['id'] = $id_usuario;
        }
    }
    // Actualiza nombre_usuario en sesión/cookie si ha cambiado
    if (!empty($datos_usuario['nombre_usuario'])) {
        $_SESSION['nombre_usuario'] = $datos_usuario['nombre_usuario'];
        setcookie('nombre_usuario', $datos_usuario['nombre_usuario'], time() + 3600, '/');
        $nombre_usuario = $datos_usuario['nombre_usuario'];
    }
}

$datos_perfil = [];
if ($logged_in && $nombre_usuario) {
    $stmt = $conectar->prepare("SELECT experiencia_laboral, habilidades, descripcion_personal, educacion, cv, testimonio FROM perfiles WHERE usuario_id = ?");
    $stmt->execute([$id_usuario]);
    $datos_perfil = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($logged_in) {
    // Si el usuario es una empresa, agrega el botón "Crear Oferta"
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
        // Para otros tipos de usuarios logueados
        $botones = '<li><a href="perfil.php">Ver Perfil</a></li>
                    <li><a href="logout.php">Cerrar sesión</a></li>';
    }
} else {
    $botones = '<li><a href="login.html">Iniciar sesión</a></li><li><a href="registro.html">Registrarse</a></li>';
}


// Genera el contenido de cada sección según el tipo de usuario
function getPerfilSection($tipo_usuario, $nombre_usuario, $datos_usuario) {
    $foto = isset($datos_usuario['foto_perfil']) ? $datos_usuario['foto_perfil'] : '../../img/default.png';
    $correo = $datos_usuario['email'] ?? '';
    $telefono = $datos_usuario['telefono'] ?? '';
    $tipo = ucfirst($tipo_usuario ?? 'Usuario');


    if (!empty($datos_usuario['foto_perfil'])) {
        $foto = 'data:image/jpeg;base64,' . base64_encode($datos_usuario['foto_perfil']);
    } else {
        $foto = '../../img/default.png';
    }

    return '
    <div class="perfil-datos">
        <div class="perfil-foto">
            <img src="'.$foto.'" alt="Foto de perfil" id="img-perfil" style="max-width:120px;max-height:120px;border-radius:50%;">
            <form id="form-foto" method="POST" enctype="multipart/form-data" style="margin-top:10px;">
                <input type="file" name="nueva_foto" id="nueva_foto" accept="image/*" style="display:none;" onchange="if(this.files[0].size > 2097152){alert(\'El archivo es demasiado grande. Máximo 2MB\'); this.value=\'\';}else{this.form.submit();}">
                <button type="button" id="btn_cambiar_foto">Cambiar foto</button>
            </form>
        </div>
        <div class="perfil-info">
            <div class="perfil-item">
                <span class="perfil-label">Nombre:</span>
                <span class="perfil-valor">'.htmlspecialchars($nombre_usuario).'</span>
            </div>
            <div class="perfil-item">
                <span class="perfil-label">Correo:</span>
                <span class="perfil-valor">'.htmlspecialchars($correo).'</span>
            </div>
            <div class="perfil-item">
                <span class="perfil-label">Teléfono:</span>
                <span class="perfil-valor">'.htmlspecialchars($telefono).'</span>
            </div>
            <div class="perfil-item">
                <span class="perfil-label">Tipo de usuario:</span>
                <span class="perfil-valor">'.$tipo.'</span>
            </div>
        </div>
    </div>
    ';
}

function getPerfilExtraSection($datos_perfil) {
    return '
        <div class="perfil-info">
            <div class="perfil-item">
                <span class="perfil-label">Experiencia laboral:</span>
                <span class="perfil-valor">'.htmlspecialchars($datos_perfil['experiencia_laboral'] ?? '').'</span>
            </div>
            <div class="perfil-item">
                <span class="perfil-label">Habilidades:</span>
                <span class="perfil-valor">'.htmlspecialchars($datos_perfil['habilidades'] ?? '').'</span>
            </div>
            <div class="perfil-item">
                <span class="perfil-label">Descripción personal:</span>
                <span class="perfil-valor">'.htmlspecialchars($datos_perfil['descripcion_personal'] ?? '').'</span>
            </div>
            <div class="perfil-item">
                <span class="perfil-label">Educación:</span>
                <span class="perfil-valor">'.htmlspecialchars($datos_perfil['educacion'] ?? '').'</span>
            </div>
            <div class="perfil-item">
                <span class="perfil-label">CV:</span>
                <span class="perfil-valor">'.htmlspecialchars($datos_perfil['cv'] ?? '').'</span>
            </div>
            <div class="perfil-item">
                <span class="perfil-label">Testimonio:</span>
                <span class="perfil-valor">'.htmlspecialchars($datos_perfil['testimonio'] ?? '').'</span>
            </div>
        </div>
    ';
}

function getAjustesSection($tipo_usuario, $datos_usuario, $datos_perfil) {
    $nombre = htmlspecialchars($datos_usuario['nombre_usuario'] ?? '');
    $correo = htmlspecialchars($datos_usuario['email'] ?? '');
    $tipo = ucfirst($tipo_usuario ?? 'Usuario');
    $experiencia = htmlspecialchars($datos_perfil['experiencia_laboral'] ?? '');
    $habilidades = htmlspecialchars($datos_perfil['habilidades'] ?? '');
    $descripcion = htmlspecialchars($datos_perfil['descripcion_personal'] ?? '');
    $educacion = htmlspecialchars($datos_perfil['educacion'] ?? '');
    $cv = htmlspecialchars($datos_perfil['cv'] ?? '');
    $testimonio = htmlspecialchars($datos_perfil['testimonio'] ?? '');

    return '
    <form class="perfil-info" method="POST" action="guardarAjustes.php">
        <div class="perfil-item">
            <span class="perfil-label">Usuario:</span>
            <input class="perfil-valor" type="text" name="nombre_usuario" value="'.$nombre.'" required>
        </div>
        <div class="perfil-item">
            <span class="perfil-label">Correo:</span>
            <input class="perfil-valor" type="email" name="email" value="'.$correo.'" readonly>
        </div>
        <div class="perfil-item">
            <span class="perfil-label">Tipo de usuario:</span>
            <input class="perfil-valor" type="text" name="tipo_usuario" value="'.$tipo.'" readonly>
        </div>
        <div class="perfil-item">
            <span class="perfil-label">Experiencia laboral:</span>
            <textarea class="perfil-valor" name="experiencia_laboral">'.$experiencia.'</textarea>
        </div>
        <div class="perfil-item">
            <span class="perfil-label">Habilidades:</span>
            <textarea class="perfil-valor" name="habilidades">'.$habilidades.'</textarea>
        </div>
        <div class="perfil-item">
            <span class="perfil-label">Descripción personal:</span>
            <textarea class="perfil-valor" name="descripcion_personal">'.$descripcion.'</textarea>
        </div>
        <div class="perfil-item">
            <span class="perfil-label">Educación:</span>
            <textarea class="perfil-valor" name="educacion">'.$educacion.'</textarea>
        </div>
        <div class="perfil-item">
            <span class="perfil-label">CV:</span>
        <input class="perfil-valor" type="file" name="cv_archivo" accept=".pdf,.doc,.docx">
        '.(!empty($cv) ? '<span style="margin-left:10px;">Archivo actual: '.htmlspecialchars($cv).'</span>' : '').'
        </div>
        <div class="perfil-item" style="justify-content:center;">
            <button type="submit" class="btn-guardar">Guardar cambios</button>
        </div>
    </form>
    ';
}

function getTestimoniosSection($tipo_usuario) {
    return '<h1>Testimonios</h1>
            <p>Aquí puedes ver y agregar testimonios.</p>';
}

// Reemplaza los marcadores en el HTML
$html = str_replace('{{perfil_section}}', getPerfilSection($tipo_usuario, $nombre_usuario, $datos_usuario), $html);
$html = str_replace('{{datos_personales_section}}', getPerfilExtraSection($datos_perfil), $html);
$html = str_replace('{{ajustes_section}}', getAjustesSection($tipo_usuario, $datos_usuario, $datos_perfil), $html);
$html = str_replace('{{testimonios_section}}', getTestimoniosSection($datos_perfil), $html);
$html = str_replace('{{botones}}', $botones, $html);

// Muestra el HTML final
echo $html;