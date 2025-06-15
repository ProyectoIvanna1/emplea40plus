$(document).ready(function(){
    $('#id_btn_enviar').click(function(){
        let usuario = $('#id_usuario').val();
        let contrasenia = $('#id_password').val();

        if (!usuario || !contrasenia) {
            alert("Por favor, completa todos los campos.");
            return;
        }

        console.log("Datos enviados:", {"nombre_usuario": usuario, "password": contrasenia});

        $.ajax({
            type: "post",
            url: "php/compUser.php",
            data: {"nombre_usuario" : usuario, "password" : contrasenia, "nocache" : Math.random()},
            dataType: "json",
            success: function(response){
                console.log("Respuesta del servidor:", response);
                if (response.success) {
                    alert("Inicio de sesión exitoso");
                    window.location.href = "php/session/indexLog.php"; // Redirige al usuario
                } else if (response.error === "inactivo") {
                    alert("Tu cuenta no está activa. Por favor, verifica tu correo.");
                } else {
                    alert("Usuario o contraseña incorrectos");
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud:", error);
                console.log("Respuesta completa del servidor:", xhr.responseText);
                alert("Hubo un problema al procesar la solicitud.");
            }
        });
    });
});