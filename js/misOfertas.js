$(document).ready(function () {
    basePath = "../ofertas/obtenerMisOfertas.php";
    
    console.log("Ruta detectada para AJAX:", basePath);

    // Cargar ofertas de la empresa
    $.ajax({
        type: "GET",
        url: basePath,
        dataType: "json",
        success: function (response) {
            console.log("Respuesta del servidor:", response);

            let ofertasContainer = $("#id_ofertas");
            ofertasContainer.empty();

            if (response.success && response.ofertas.length > 0) {
                response.ofertas.forEach(function (oferta) {
                    let ofertaHTML = `
                        <div class="job" data-id="${oferta.id}">
                            <h3>${oferta.titulo}</h3>
                            <p>${oferta.descripcion}</p>
                            <ul>
                                <li><strong>Requisitos:</strong> ${oferta.requisitos}</li>
                                <li><strong>Ubicación:</strong> ${oferta.ubicacion}</li>
                                <li><strong>Horario:</strong> ${oferta.tipo_horario}</li>
                                <li><strong>Fecha de publicación:</strong> ${oferta.fecha_publicacion}</li>
                            </ul>
                            <input type="button" class="delete-button" value="Eliminar">
                        </div>
                    `;
                    ofertasContainer.append(ofertaHTML);
                });
            } else {
                ofertasContainer.html("<p>No hay ofertas disponibles en este momento.</p>");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar las ofertas:", error);
            $("#id_ofertas").html("<p>Hubo un error al cargar las ofertas.</p>");
        }
    });

    // Botón aplicar (como ya lo tenías)
    $(document).on("click", ".apply-button", function () {
        let ofertaId = $(this).closest(".job").data("id");
        if (ofertaId) {
            window.open(`php/detalleOferta.php?id=${ofertaId}`, "_blank");
        }
    });

    // Botón eliminar
    $(document).on("click", ".delete-button", function () {
        let ofertaId = $(this).closest(".job").data("id");
        if (!ofertaId) return;

        if (!confirm("¿Estás seguro de que deseas eliminar esta oferta?")) return;

        let deleteURL = "../ofertas/eliminarOferta.php"; // Ruta para eliminar la oferta

        // Enviar petición para eliminar
        $.ajax({
            type: "POST",
            url: deleteURL,
            data: { id: ofertaId },
            success: function (response) {
                try {
                    let json = typeof response === 'string' ? JSON.parse(response) : response;
                    if (json.success) {
                        // Remover la oferta del DOM
                        $(`.job[data-id="${ofertaId}"]`).remove();

                        // Si ya no quedan ofertas, mostrar mensaje
                        if ($(".job").length === 0) {
                            $("#id_ofertas").html("<p>No hay ofertas creadas.</p>");
                        }
                    } else {
                        alert("No se pudo eliminar la oferta.");
                    }
                } catch (e) {
                    console.error("Error al analizar la respuesta del servidor:", response);
                    alert("Error inesperado.");
                }
            },
            error: function (xhr) {
                console.error("Error al eliminar la oferta:", xhr.responseText);
                alert("Error al eliminar la oferta.");
            }
        });
    });
});
