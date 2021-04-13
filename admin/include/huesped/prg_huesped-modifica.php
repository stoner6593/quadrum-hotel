<?php
session_start();
include "../../config.php";
include "../functions.php";

//--------------------------------------------------------------
$xidprimario = $_POST['txtidprimario'];

$xtxtnombre = mayuscula($_POST['txtnombre']);
$xtxtnacimiento = Cfecha($_POST['txtnacimiento']);
$xtxtdocumento = $_POST['txtdocumento'];
$xtxtestadocivil = $_POST['txtestadocivil'];
$xtxtciudad = mayuscula($_POST['txtciudad']);
$xtxtpais = mayuscula($_POST['txtpais']);
$xtxtprocedencia = mayuscula($_POST['txtprocedencia']);
$xtxtdestino = mayuscula($_POST['txtdestino']);
$xtxtcomentarios = mayuscula($_POST['txtcomentarios']);
$tipo_documento=$_POST['tipo_documento'];
$txtnograta = isset($_POST["txtnograta"]) ? $_POST["txtnograta"] :0;
$xtxtprofesion = mayuscula($_POST['txtprofesion']);
$xtxtocupacion = mayuscula($_POST['txtocupacion']);
$xtxtocupacion = mayuscula($_POST['txtocupacion']);
$xtsexo = $_POST["selecsexo"];
//*****************************************************


$consulta="update cliente set

	nombre = '$xtxtnombre',
	nacimiento = '$xtxtnacimiento',
	documento = '$xtxtdocumento',
	idestadocivil = '$xtxtestadocivil',
	ciudad = '$xtxtciudad',
	pais = '$xtxtpais',
	procedencia = '$xtxtprocedencia',
	destino = '$xtxtdestino',
	comentarios = '$xtxtcomentarios',
	nograto = '$txtnograta',
	tipo_documento='$tipo_documento',
	profesion='$xtxtprofesion',
	ocupacion='$xtxtocupacion',
	sexo='$xtsexo'

	where idhuesped = '$xidprimario'";

if($mysqli->query($consulta)){
		$Men = "Los datos fueron guardados satisfactoriamente.";
}else{
		$Men = "Ha fallado, los datos no han sido registrados.";
}
$mysqli->close();
echo $Men;
$_SESSION['msgerror'] = $Men;
header("Location: ../../huespedes.php"); exit;
//************************************************************
?>
