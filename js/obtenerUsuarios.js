
$(document).ready(function () {
    // Detecta si estamos en una ruta que contiene "session"
    let basePath;
    if (window.location.pathname.includes("log")) {
        // Si estamos en session/perfilesEmpresasLog.php
        basePath = "../php/obtenerUsuarios.php";
    } else if (window.location.pathname.includes("session")) {
        // Si estamos en session/perfilesEmpresasLog.php
        basePath = "../obtenerUsuarios.php";
    } 
    else {
        // Si estamos en perfilesEmpresasLog.html fuera de /session
        basePath = "php/obtenerUsuarios.php";
    }

    console.log("Ruta detectada para AJAX:", basePath); // Debug

    $.ajax({
        url: basePath,
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            if (Array.isArray(data) && data.length > 0) {
                let html = '<div id="usuarios-container">';
                data.forEach(function (usuario) {
                    html += `
                        <div class="usuario-card">
                            <img src="${usuario.foto_src}" alt="Foto de ${usuario.nombre_usuario}" class="usuario-foto" />
                            <p><strong>Usuario:</strong> ${usuario.nombre_usuario}</p>
                        </div>
                    `;
                });
                html += '</div>';
                $('#perfiles-section').html(html);
            } else {
                $('#perfiles-section').html('<p>No hay usuarios registrados.</p>');
            }
        },
        error: function () {
            $('#perfiles-section').html('<p>Error al cargar los usuarios.</p>');
        }
    });
});
