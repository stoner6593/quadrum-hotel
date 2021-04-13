<?php

$ftp_server = "ftp.electronperu.com";
$ftp_user_name = "userftpfe@electronperu.com";
$ftp_user_pass = "userftpfe";

$name_directory_base = "/hotel";

$habilitar_envio_automatico_archivo_factura = 1;

$ftp_carpeta_local_pdf =  $_SERVER['DOCUMENT_ROOT'] . $name_directory_base . "/admin/FE/PDF/";
$ftp_carpeta_local_xml =  $_SERVER['DOCUMENT_ROOT'] . $name_directory_base . "/admin/FE/XMLFIRMADOS/";
$ftp_carpeta_local_cdr =  $_SERVER['DOCUMENT_ROOT'] . $name_directory_base . "/admin/FE/CDR/";

//Tener en cuenta si el usuario tiene acceso a carpeta raiz o directamente a la carpeta donde desea registrar
//En este caso registramos un usuario con solo acceso a la siguiente carpeta: /home/electron/GestorComprobantes/filevoucher
// solo basta con colocar / para que se ubica en la unica carpeta a la cual tiene permisos
$ftp_carpeta_remota= "/";

?>