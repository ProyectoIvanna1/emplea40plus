// Detecta si estamos en una ruta que contiene "session"
    let basePath;
    if (window.location.pathname.includes("session")) {
        // Si estamos en algo como session/indexLog.php
        basePath = "../cargarAyudas.php";
    }else{
        // Si estamos en una ruta directa como cursosLog.html
        basePath = "php/cargarAyudas.php";
    }

    console.log("Ruta detectada para AJAX:", basePath); // Debug



// Cargar las ayudas desde la base de datos
$(document).ready(function () {
    $.ajax({
        url: basePath, // ruta del archivo php
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            const listaAyudas = $('#id_lista_ayudas');
            if (data.error) {
                listaAyudas.html(`<p>Error: ${data.error}</p>`);
            } else {
                data.forEach(function (ayuda) {
                    const ayudasDiv = $(`
                        <div class="ayuda">
                            <h2>${ayuda.nombre_ayuda}</h2>
                            <img src="${ayuda.imagen}" alt="Imagen del curso" style="max-width:100%;height:auto;">
                            <p>${ayuda.descripcion}</p>
                            <p><strong>Fecha de publicación:</strong> ${ayuda.fecha_publicacion}</p>
                            <a href="${ayuda.enlace}" class="cta-button" target="_blank">Enlace a la ayuda</a>
                        </div>
                    `);
                    listaAyudas.append(ayudasDiv);
                });
            }
        },
        error: function (xhr, status, error) {
            console.error('Error al cargar las ayudas:', error);
            $('#id_lista_cursos').html('<p>Hubo un error al cargar los cursos. Inténtalo más tarde.</p>');
        }
    });
});