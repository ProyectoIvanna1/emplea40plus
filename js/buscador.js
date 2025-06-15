$(document).ready(function () {
    console.log("ofertas.js cargado correctamente.");

    // Asignar el evento correctamente al botón
    $("#search-button").on("click", filtrarOfertas);

});

// Función que filtra las ofertas
function filtrarOfertas() {
    console.log("dentro de filtrar ofertas"); // Depuración
    let input = $("#search-input").val().toLowerCase();
    let ofertas = $(".job");
    let hayResultados = false;

    ofertas.each(function () {
        let textoOferta = $(this).text().toLowerCase();
        if (textoOferta.includes(input)) {
            $(this).show();
            hayResultados = true;
        } else {
            $(this).hide();
        }
    });

    // Si no hay resultados, mostrar mensaje
    if (!hayResultados) {
        $("#mensaje-no-encontrado").show();
    } else {
        $("#mensaje-no-encontrado").hide();
    }
}