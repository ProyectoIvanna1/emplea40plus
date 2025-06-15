$(document).ready(function () {
    // Detecta la ubicación del archivo HTML
    console.log(window.location.pathname.includes("session"));
    let basePath;
    if (window.location.pathname.includes("session")) {
        // Si estás en php/session/indexLog.php
        basePath = "../getOfertas.php";
    } else {
        // Si estás en index.html
        basePath = "php/getOfertas.php";
    }

    console.log("Ruta detectada para AJAX:", basePath);

    // Realiza la solicitud AJAX para obtener las ofertas de empleo
    $.ajax({
        type: "GET",
        url: basePath, // Archivo PHP que devuelve las ofertas
        dataType: "json",
        success: function (response) {
            console.log("Respuesta del servidor:", response); // Depuración
            // Verifica si la respuesta contiene ofertas
            if (response.success && response.ofertas.length > 0) {
                let ofertasContainer = $("#id_ofertas"); // Contenedor de las ofertas
                ofertasContainer.empty(); // Limpia el contenedor antes de agregar nuevas ofertas

                // Itera sobre las ofertas y crea los elementos dinámicamente
                response.ofertas.forEach(function (oferta) {
                    let ofertaHTML = `
                        <div class="job">
                            <h3>${oferta.titulo}</h3>
                            <p>${oferta.descripcion}</p>
                            <ul>
                                <li><strong>Requisitos:</strong> ${oferta.requisitos}</li>
                                <li><strong>Ubicación:</strong> ${oferta.ubicacion}</li>
                                <li><strong>Horario:</strong> ${oferta.tipo_horario}</li>
                                <li><strong>Fecha de publicación:</strong> ${oferta.fecha_publicacion}</li>
                            </ul>
                            <input type="button" class="apply-button" data-id="${oferta.id}" value="Aplicar">
                        </div>
                    `;
                    ofertasContainer.append(ofertaHTML); // Agrega la oferta al contenedor
                });
            } else {
                console.log("No hay ofertas disponibles");
                // Si no hay ofertas, muestra un mensaje
                $("#id_ofertas").html("<p>No hay ofertas disponibles en este momento.</p>");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar las ofertas:", error);
            console.log("Estado:", status);
            console.log("Respuesta completa:", xhr.responseText);
            $("#id_ofertas").html("<p>Hubo un error al cargar las ofertas. Inténtalo más tarde.</p>");
        }
    });

    $(document).on("click", ".apply-button", function () {
        // Obtén el ID de la oferta desde el atributo data-id
        let ofertaId = $(this).data("id");
        // Verifica si el ID es válido
        console.log("El id es: " + ofertaId)
        if (!ofertaId) {
            console.error("El ID de la oferta no está definido.");
            return;
        }
    
        // Abre una nueva pestaña con la URL que muestra los detalles de la oferta
        window.open(`php/detalleOferta.php?id=${ofertaId}`, "_blank");
    });


});