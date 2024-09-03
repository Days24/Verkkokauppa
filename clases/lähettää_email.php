<?php 

use PHPMailer\PHPMailer\{PHPMailer, SMTP, Exception};

require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require '../phpmailer/src/Exception.php';

$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      
    $mail->isSMTP();                                            
    $mail->Host       = 'smtp.example.com';                     
    $mail->SMTPAuth   = true;                                   
    $mail->Username   = 'user@example.com';                     
    $mail->Password   = 'secret';                               
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('from@example.com', 'Mailer');
    $mail->addAddress('joe@example.net', 'Joe User');     

    
    //Content
    $mail->isHTML(true);                                 
    $mail->Subject = 'Ostoksesi tiedot';
    $cuerpo = '<h4>Kiitos ostoksestasi</h4>';
    $cuerpo .= '<p>Ostotunnuksesi on <b>'. $id_transaccion .'</b></p>';
    $mail->Body    = utf8_decode($cuerpo);
    $mail->AltBody = 'Lähetämme sinulle ostoksesi tiedot';

    $mail->setLanguage('fi', '../phpmailer/language/phpmailer.lang-fi.php');

    $mail->send();
} catch (Exception $e) {
    echo "Virhe lähetettäessa ostosähköpostia: {$mail->ErrorInfo}";
    //exit;
}






?>