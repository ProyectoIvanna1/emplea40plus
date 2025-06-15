$(document).ready(function () {
    function getCookie(name) {
        let cookies = document.cookie.split(';');
        for (let i = 0; i < cookies.length; i++) {
            let cookie = cookies[i].trim();
            if (cookie.startsWith(name + '=')) {
                return cookie.substring(name.length + 1);
            }
        }
        return null;
    }

    let correo = getCookie('correo_verificacion'); // Obtén el correo de la cookie
    if (correo) {
        correo = decodeURIComponent(correo); // Decodifica el correo para convertir %40 en @
        $('#correo').val(correo); // Escribe el correo en el campo oculto
    } else {
        alert("No se encontró el correo. Por favor, vuelve a intentarlo.");
    }

    $('#verificarCodigoBtn').click(function () {
        let codigo = $('#codigo').val();

        if (!codigo || codigo.length !== 5 || isNaN(codigo)) {
            alert("Por favor, ingresa un código válido de 5 dígitos.");
            return;
        }

        // Envía el código al servidor para verificarlo
        $.ajax({
            type: "POST",
            url: "php/verificarCodigo.php", // Ruta al archivo PHP
            data: { "codigo": codigo, "correo": correo },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    alert(response.message);
                    window.location.href = "login.html"; // Redirige al inicio de sesión
                } else {
                    alert(response.error);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error en la solicitud:", error);
                console.log("Respuesta completa del servidor:", xhr.responseText);
                alert("Hubo un problema al verificar el código.");
            }
        });
    });
});