$(document).ready(function () {
    $('#id_registro_btn').click(function () {
        // Obtiene los valores de los campos de entrada
        let usuario = $('#id_usuario').val();
        let contrasenia = $('#id_password').val();
        let tipoUsuario = $('#id_tipo_usuario').val();
        let correo = $('#id_correo').val();

        // Verifica que los campos no estén vacíos
        if (!usuario || !contrasenia || !tipoUsuario || !correo) {
            alert("Por favor, completa todos los campos.");
            return;
        }
        console.log(contrasenia + " - " + tipoUsuario + " - " + correo + " - " + usuario);
        // Envía la solicitud AJAX al servidor
        $.ajax({
            type: "POST",
            url: "php/RegUser.php",
            data: {"usuario": usuario,"password": contrasenia,"tipo_usuario": tipoUsuario,"correo": correo, "nocache": Math.random()},
            dataType: "json",
            success: function (response) {
                console.log("Respuesta del servidor:", response);
                if (response.success) {
                    alert(response.message);
                    // Llama a enviarCodigo.php para enviar el código de verificación
                    $.ajax({
                        type: "POST",
                        url: "php/enviarCodigo.php",
                        data: { "correo": correo },
                        dataType: "json",
                        success: function (response) {
                            console.log("Respuesta de enviarCodigo.php:", response);
                            if (response.success) {
                                alert("Código enviado al correo.");
                                window.location.href = "dobleFactor.html"; // Redirige a la página de verificación
                            } else {
                                alert(response.error);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("Error en la solicitud a enviarCodigo.php:", error);
                            console.log("Respuesta completa del servidor:", xhr.responseText);
                            alert("Hubo un problema al enviar el código.");
                        }
                    });
                } else {
                    alert(response.error);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error en la solicitud:", error);
                console.log("Respuesta completa del servidor:", xhr.responseText);
                alert("Hubo un problema al procesar la solicitud.");
            }
        });
    });
});