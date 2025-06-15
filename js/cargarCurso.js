$(document).ready(function () {
    // Detecta si estamos en una ruta que contiene "session"
    let basePath;
    if (window.location.pathname.includes("session")) {
        // Si estamos en algo como session/indexLog.php
        basePath = "../cargarCursos.php";
    }else{
        // Si estamos en una ruta directa como cursosLog.html
        basePath = "php/cargarCursos.php";
    }

    console.log("Ruta detectada para AJAX:", basePath); // Debug

    $.ajax({
        url: basePath,
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            const listaCursos = $('#id_lista_cursos');
            listaCursos.empty(); // Limpiamos por si hay contenido previo

            if (data.error) {
                listaCursos.html(`<p>Error: ${data.error}</p>`);
            } else if (data.length === 0) {
                listaCursos.html(`<p>No hay cursos disponibles en este momento.</p>`);
            } else {
                data.forEach(function (curso) {
                    const cursoDiv = $(`
                        <div class="curso">
                            <h2>${curso.nombre_curso}</h2>
                            <img src="${curso.imagen}" alt="Imagen del curso" style="max-width:100%;height:auto;">
                            <p>${curso.descripcion}</p>
                            <p><strong>Fecha de publicación:</strong> ${curso.fecha_publicacion}</p>
                            <a href="${curso.enlace_curso}" class="cta-button" target="_blank">Enlace al curso</a>
                        </div>
                    `);
                    listaCursos.append(cursoDiv);
                });
            }
        },
        error: function (xhr, status, error) {
            console.error('Error al cargar los cursos:', error);
            console.log("Respuesta completa:", xhr.responseText);
            $('#id_lista_cursos').html('<p>Hubo un error al cargar los cursos. Inténtalo más tarde.</p>');
        }
    });
});
