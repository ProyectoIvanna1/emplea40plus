<?php  
    use PHPMailer\PHPMailer\PHPMailer; 
    use PHPMailer\PHPMailer\Exception;  
    require 'PHPMailer/src/Exception.php'; 
    require 'PHPMailer/src/PHPMailer.php'; 
    require 'PHPMailer/src/SMTP.php';  
    $mail = new PHPMailer(true);    
    // Crear una instancia de PHPMailer  
    try {     
        //definiciones      
        $mail = new PHPMailer();     
        $mail->IsSMTP();     
        $mail->SMTPDebug = 1; 
        //da info en excepción. cambiar a 0 para no verla     
        $mail->SMTPAuth = true;     
        $mail->SMTPSecure = "TLS";     
        $mail->Host = "smtp.gmail.com";     
        $mail->Port = 587; //puerto para TLS     
        // datos user     
        $mail->Username = "ivanna.ariza@emplea40plus.es"; // Usuario de google      
        $mail->Password = "contraseniacorreo123**"; // Clave  contraseniacorreo123**     
        $mail->SetFrom('ivannamon22@gmail.com', 'ivanna'); //mail y nombre de remitente        
        //contenido mail     
        $mail->CharSet = 'UTF-8'; //para que detecte los caracteres bien     
        $mail->Subject = "Correo de prueba"; //título     
        $mail->MsgHTML('Prueba en español'); //mensaje     
  
        //info de destinatarios     
        $mail->AddAddress('ivannamon22@gmail.com', "Test");      
        //$mail->addbcc("ejemplo@ejemplo.com"); //copia oculta    
        //envío     
        $result = $mail->Send();      
        if (!$result) { //Se comprueba según se haya enviado          
            echo "Error" . $mail->ErrorInfo;     
        } else {        
            echo "Enviado";     
        } 
    } catch (Exception $ex) {     echo "Error detectado: " . $ex; } 
    