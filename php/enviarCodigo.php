<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$correo = $_POST['correo'];
$codigo = rand(10000, 99999); // Genera un código aleatorio de 5 dígitos

session_name(md5($correo)); // Usa un hash del correo como nombre de la sesión
session_start();

if (!isset($_POST['correo'])) {
    echo json_encode(["success" => false, "error" => "Correo no proporcionado"]);
    exit;
}

// Guarda el código y el timestamp en la sesión
$_SESSION['codigo_verificacion'] = $codigo;
$_SESSION['timestamp'] = time(); // Guarda el tiempo de generación del código
$_SESSION['correo_verificacion'] = $correo;


$mail = new PHPMailer(true);

try {
    // Configuración del servidor SMTP
    $mail->isSMTP();
    $mail->SMTPDebug = 0; // Cambiar a 2 para ver la información de depuración del servidor SMTP
    //da info en excepción. cambiar a 0 para no verla     
    $mail->SMTPAuth = true;     
    $mail->SMTPSecure = "TLS";     
    $mail->Host = "smtp.ionos.es";     
    $mail->Port = 587; //puerto para TLS   
    $mail->Username = "ivanna.ariza@emplea40plus.es"; // correo de la empresa    
    $mail->Password = "contraseniacorreo123**"; // Clave   contraseniacorreo123**
     

    // Configuración del correo
    $mail->SetFrom('ivanna.ariza@emplea40plus.es', 'Administración'); //mail y nombre de remitente  
    $mail->addAddress($correo, 'Verificación'); //correo del destinatario
    $mail->CharSet = 'UTF-8'; //para que detecte los caracteres bien 
    $mail->Subject = 'Código de Verificación';
    $mail->Body = "Tu código de verificación es: $codigo"; 

    /*$result = $mail->Send();      
        if (!$result) { //Se comprueba según se haya enviado          
            echo "Error" . $mail->ErrorInfo;     
        } else {        
            echo "Enviado";     
        }
        */
    $mail->send();
    echo json_encode(["success" => true, "message" => "Código enviado al correo."]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => "Error al enviar el correo: {$mail->ErrorInfo}"]);
}
