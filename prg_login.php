<?php
session_start();
include("admin/config.php");



$xusuario  = $mysqli->real_escape_string($_POST['username']);
$xpassword = md5($mysqli->real_escape_string($_POST['password']));
$xvar = 1;
$sqlusuario = $mysqli->query("select 
	a.user_id, 
	a.user_nombre, 
	a.user_user, 
	a.user_password,
	a.user_categoria,
	a.user_estado,
	b.rol_id,
	b.rol_descripcion,
	b.rol_descripcion_corta,
	rol_pagina_inicio
	from usuario a
	inner join usuario_rol b on b.rol_id = a.user_categoria
	where a.user_user ='". $xusuario ."' and a.user_password = '". $xpassword ."' and a.user_estado = '".$xvar."' ");
	
$uFila = $sqlusuario->fetch_row(); //Obtiene filas // fetch_assoc() = campos por nombre / fetch_row = campos por indice
$num = $sqlusuario->num_rows; //Obtiene nro de filas

if($num == 0 || $num == "")	{
	$_SESSION['msgerror'] = "Usuario o password inv&aacute;lido.";
	header("Location: index.php"); exit;
}else{
	$_SESSION['xyzcodigo'] = "e10adc3949ba59abbe56e057f20f883z";
	$_SESSION['xyzidusuario'] = $uFila['0'];
	$_SESSION['xyznombre'] = $uFila['1'];
	$_SESSION['xyzusuario'] = $uFila['2'];
	$_SESSION['xyztipo'] = $uFila['4'];
	$_SESSION['xyzrol'] = $uFila['8'];
    $_SESSION['xyzrolpaginicio'] = $uFila['9'];
	header("Location: ".$uFila['9']); exit;
	//Redirigir al Admin
}

/* cerrar el resultset */
$sqlusuario->close();
/* cerrar la conexión */
$mysqli->close();
?>