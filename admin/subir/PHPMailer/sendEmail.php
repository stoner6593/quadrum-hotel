<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

$idalquiler = $_GET['idalquiler'];
$txtCorreo = $_POST['txtcorreo'];
$txtCopia = $_POST['txtcorreocopia'];
$txtAsunto = $_POST['txtasunto'];
$txtCuerpo = $_POST['txtcuerpo'];

$txtPathfilexml = $_POST['Pathfilexml'];
$txtPathfilepdf = $_POST['Pathfilepdf'];

$mail = new PHPMailer(true);
try {
    //$mail->SMTPDebug = 2;  // Sacar esta línea para no mostrar salida debug
    $mail->isSMTP();
    $mail->CharSet="UTF-8";
    $mail->Host = 'smtp.gmail.com';  // Host de conexión SMTP
    $mail->SMTPAuth = true;
    $mail->Username = 'sendfesunat@gmail.com';                 // Usuario SMTP
    $mail->Password = 'fe951753';                           // Password SMTP
    $mail->SMTPSecure = 'tls';                            // Activar seguridad TLS
    $mail->Port = 587;                                    // Puerto SMTP
 
    $mail->setFrom('hotel@hotel21.com');		// Mail del remitente
    $mail->addAddress($txtCorreo);     // Mail del destinatario
    $mail->AddReplyTo('hotel@hotel21.com', 'Information'); //Mail informacion responder
    $mail->addCC($txtCopia); //copia
    $mail->isHTML(true);
    $mail->Subject = $txtAsunto;  // Asunto del mensaje
    $mail->Body    = $txtCuerpo;    // Contenido del mensaje (acepta HTML)
    $mail->AltBody = $txtCuerpo;    // Contenido del mensaje alternativo (texto plano)
    //$mail->AddAttachment('README.md', 'pricelist.doc');
    $mail->AddAttachment('../'.$txtPathfilexml);
    $mail->AddAttachment('../'.$txtPathfilepdf);
 
    $mail->send();
    $_SESSION['msgeinfo'] = 'El mensaje ha sido enviado a '.$txtCorreo;

    header("Location: ../enviar_fe_cliente.php?idalquiler=".$idalquiler); exit;
} catch (Exception $e) {
    $_SESSION['msgeerror'] = 'El mensaje no se ha podido enviar, error: '.$mail->ErrorInfo;

    header("Location: ../enviar_fe_cliente.php?idalquiler=".$idalquiler); exit;
}
