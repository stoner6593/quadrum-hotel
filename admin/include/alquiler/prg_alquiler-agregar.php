<?php
session_start();
include "../../config.php";
include "../functions.php";
include "../Configuraciones.php";
date_default_timezone_set('America/Lima');

/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

$xtxttipoalquiler = $_GET['idtipo'];
$xidalquiler = $_GET['idalquiler'];
$xidhabitacion = $_GET['idhabitacion'];
$xidturno = $_GET['idturno'];
$xidusuario = $_GET['idusuario'];

$configs = new Configuraciones($mysqli);
$precioHorasAlquilerTarifa1 = $configs->getConfig("NRO_ALQUILER_HORAS_TARIFA1");
$precioHorasAlquilerTarifa2 = $configs->getConfig("NRO_ALQUILER_HORAS_TARIFA2");

//--------------------------------------------------------------
$TblMax = $mysqli->query("select max(idalquilerdetalle) from al_venta_detalle");
$Contador = $TblMax->fetch_row();
$xidprimario = $Contador['0'] + 1 ;



//1. ALQUILER POR HORAS *********************************************************************************
if($xtxttipoalquiler == 1){
	$xcostohoras = $_POST['txtprecioporhora'];
	$xnrohoras = $precioHorasAlquilerTarifa1;
	
	//FECHA SACAR DE ULTIMA FECHA
	$sqlconsulta = $mysqli->query("select idalquiler, fechafin from al_venta where idalquiler = '$xidalquiler'");
	$aFila = $sqlconsulta->fetch_row();
	$xfechadesde = $aFila['1'];
	
	$xfechahasta = sumarhoraafecha($xnrohoras,$xfechadesde); //Fecha hasta adicionando 6 horas
	$xtotal = $xcostohoras;
	
	
	$consultadet = "insert al_venta_detalle (
		idalquilerdetalle,
		idalquiler,
		tipoalquiler,
		fechadesde,
		fechahasta,
		nrohoras,
		costohora,
		preciounitario,
		cantidad,
		total,
		nrodias,
		costodia,
		formapago,
		totalefectivo,
		totalvisa,
		totalmastercard,
		estadopago,
		costoingresoanticipado,
		horaadicional,
		costohoraadicional,
		huespedadicional,
		costohuespedadicional,
		idturno,
		idusuario,
		detoriginal
		
		)values(
		
		'$xidprimario',
		'$xidalquiler',
		'$xtxttipoalquiler',
		'$xfechadesde',
		'$xfechahasta',
		'$xnrohoras',
		'$xcostohoras',
		'$xcostohoras',
		'$xnrohoras',
		'$xtotal',
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		'$xidturno',
		'$xidusuario',
		0
		)";
		
		if($mysqli->query($consultadet) == 1){
			//$Men = "Grabado";
			
			// Actualizar Fecha Fin 		
			$consultaact="update al_venta set
			fechafin = '$xfechahasta'
			where idalquiler = '$xidalquiler'";
			if($mysqli->query($consultaact) == 1){}else{
				printf("line ".__LINE__." - Errormessage: %s\n", $mysqli->error); exit;
			}
			
			//echo "Hola";
		}else{
			printf("line ".__LINE__." - Errormessage: %s\n", $mysqli->error); exit;
		}

		$mysqli->close();	
		//$_SESSION['msgerror'] = $Men; 
		header("Location: ../../alquilar-detalle.php?idhabitacion=$xidhabitacion&idalquiler=$xidalquiler"); 
		exit; 
}

//2. ALQUILER POR DIA  *********************************************************************************
if($xtxttipoalquiler == 2){
	$txtcostodiario = $_POST['txtpreciopordia'];
	$txtnrodias = $_POST['txtnrodias'];
	//Si viene vacio
	if($txtnrodias == 0){
	$_SESSION['msgerror'] = 'Seleccione el número de días.';
	echo "<script type=\"text/javascript\">
           history.go(-1);
       </script>";
	exit;
	}
	
	//FECHA SACAR DE ULTIMA FECHA
	$sqlconsulta = $mysqli->query("select idalquiler, fechafin from al_venta where idalquiler = '$xidalquiler'");
	$aFila = $sqlconsulta->fetch_row();
	$xfechadesde = $aFila['1'];
	$xfechahasta = date("Y-m-d", strtotime("$xfechadesde + $txtnrodias day"));
	
	
	$xfechahasta = $xfechahasta.' '.'12:00:00'; //Fecha hasta 
	
	if(isset($_POST["hiddenCompletar"])){
		$xcostohoras = $_POST['txtprecioporhora'];
		$txtcostodiario = $txtcostodiario - $xcostohoras;
	}

	$xtotal = $txtcostodiario * $txtnrodias;	
	
	$consulta="insert al_venta_detalle (
		idalquilerdetalle,
		idalquiler,		
		tipoalquiler,
		fechadesde,
		fechahasta,
		nrodias,
		costodia,
		preciounitario,
		cantidad,
		total,
		nrohoras,
		costohora,
		formapago,
		totalefectivo,
		totalvisa,
		totalmastercard,
		estadopago,
		costoingresoanticipado,
		horaadicional,
		costohoraadicional,
		huespedadicional,
		costohuespedadicional,
		idturno,
		idusuario,
		detoriginal

		)values(
		
		'$xidprimario',
		'$xidalquiler',		
		'$xtxttipoalquiler',
		'$xfechadesde',
		'$xfechahasta',
		'$txtnrodias',
		'$txtcostodiario',
		'$txtcostodiario',
		'$txtnrodias',
		'$xtotal',
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		'$xidturno',
		'$xidusuario',
		0	

		)";
		if($mysqli->query($consulta) == 1){
			$Men = "Grabado";
			// Actualizar Fecha Fin 		
			$consultaact="update al_venta set
			fechafin = '$xfechahasta'
			where idalquiler = '$xidalquiler'";
			if($mysqli->query($consultaact) == 1){}else{
				printf("line ".__LINE__." - Errormessage: %s\n", $mysqli->error); exit;
			}
		}else{
			printf("line ".__LINE__." - Errormessage: %s\n", $mysqli->error); exit;
		}
		$mysqli->close();	
		$_SESSION['msgerror'] = $Men;
		header("Location: ../../alquilar-detalle.php?idhabitacion=$xidhabitacion&idalquiler=$xidalquiler");
		exit;
}





//4. HUESPED ADICIONAL
if($xtxttipoalquiler == 4){
	$txtprecioadicionalhora = $_POST['txtprecioocupanteadicional'];
	$txtocupantesadicionaleshoras = $_POST['txtnrocupanteadicional'];
	$xtotal = $txtprecioadicionalhora * $txtocupantesadicionaleshoras;
	
	//Si viene vacio
	if($txtocupantesadicionaleshoras == 0){
	$_SESSION['msgerror'] = 'Seleccione el número de Huéspedes adicionales.';
	echo "<script type=\"text/javascript\">
           history.go(-1);
       </script>";
	exit;
	}
	
	
	$consulta="insert al_venta_detalle (
		idalquilerdetalle,
		idalquiler,
		
		tipoalquiler,
		huespedadicional,
		costohuespedadicional,
		preciounitario,
		cantidad,
		total,
		fechadesde,
		fechahasta,
		nrohoras,
		nrodias,
		costohora,
		costodia,
		formapago,
		totalefectivo,
		totalvisa,
		totalmastercard,
		estadopago,
		costoingresoanticipado,
		horaadicional,
		costohoraadicional,
		idturno,
		idusuario,
		detoriginal
		
		)values(
		
		'$xidprimario',
		'$xidalquiler',
		
		'$xtxttipoalquiler',
		'$txtocupantesadicionaleshoras',
		'$txtprecioadicionalhora',
		'$txtprecioadicionalhora',
		'$txtocupantesadicionaleshoras',
		'$xtotal',
		'1900-01-01',
		'1900-01-01',
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		'$xidturno',
		'$xidusuario',
		0
		
		)";
		if($mysqli->query($consulta) == 1){
			$Men = "Grabado"	;
		}else{
			printf("line ".__LINE__." - Errormessage: %s\n", $mysqli->error); exit;
		}
		$mysqli->close();	
		$_SESSION['msgerror'] = $Men;
		header("Location: ../../alquilar-detalle.php?idhabitacion=$xidhabitacion&idalquiler=$xidalquiler");
		exit; 
	
}

//3. HORA ADICIONAL
if ($xtxttipoalquiler == 3){
	
	$costohoraadicional = $_POST['txtpreciohoraadicional'];
	$nrohoraadicional = $_POST['txtnrohoraadicional'];
	$xtotal = $costohoraadicional * $nrohoraadicional;
	
	//Si viene vacio
	if($nrohoraadicional == 0){
	$_SESSION['msgerror'] = 'Seleccione el número de horas adicionales.';
	echo "<script type=\"text/javascript\">
           history.go(-1);
       </script>";
	exit;
	}
	
	//FECHA SACAR DE ULTIMA FECHA
	$sqlconsulta = $mysqli->query("select idalquiler, fechafin from al_venta where idalquiler = '$xidalquiler'");
	$aFila = $sqlconsulta->fetch_row();
	$xfechadesde = $aFila['1'];
	$xfechahasta = sumarhoraafecha($nrohoraadicional,$xfechadesde);
	
	
	
	$consulta="insert al_venta_detalle(
		idalquilerdetalle,
		idalquiler,
		
		fechadesde,
		fechahasta,
		
		tipoalquiler,
		horaadicional,
		costohoraadicional,
		preciounitario,
		cantidad,
		total,
		nrohoras,
		nrodias,
		costohora,
		costodia,
		formapago,
		totalefectivo,
		totalvisa,
		totalmastercard,
		estadopago,
		costoingresoanticipado,
		huespedadicional,
		costohuespedadicional,
		idturno,
		idusuario,
		detoriginal
		
		)values(
		
		'$xidprimario',
		'$xidalquiler',
		
		'$xfechadesde',
		'$xfechahasta',
		
		'$xtxttipoalquiler',
		'$nrohoraadicional',
		'$costohoraadicional',
		'$costohoraadicional',
		'$nrohoraadicional',
		'$xtotal',
		'$nrohoraadicional',
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		'$xidturno',
		'$xidusuario',
		0
		
		)";
		if($mysqli->query($consulta) == 1){
			$Men = "Grabado";
			// Actualizar Fecha Fin 		
			$consultaact="update al_venta set
			fechafin = '$xfechahasta'
			where idalquiler = '$xidalquiler'";
			if($mysqli->query($consultaact) == 1){}else{
				printf("line ".__LINE__." - Errormessage: %s\n", $mysqli->error); exit;
			}
		}else{
			printf("line ".__LINE__." - Errormessage: %s\n", $mysqli->error); exit;
		}
		$mysqli->close();	
		$_SESSION['msgerror'] = $Men;
		header("Location: ../../alquilar-detalle.php?idhabitacion=$xidhabitacion&idalquiler=$xidalquiler");
		exit; 
}

//1. ALQUILER POR HORAS - ACTUALIZACION 12 HORAS*********************************************************************************
if($xtxttipoalquiler == 6){
	$xcostohoras = $_POST['txtprecioporhora12'];
	$xnrohoras = $precioHorasAlquilerTarifa2;
	
	//FECHA SACAR DE ULTIMA FECHA
	$sqlconsulta = $mysqli->query("select idalquiler, fechafin from al_venta where idalquiler = '$xidalquiler'");
	$aFila = $sqlconsulta->fetch_row();
	$xfechadesde = $aFila['1'];
	
	$xfechahasta = sumarhoraafecha($xnrohoras,$xfechadesde); //Fecha hasta adicionando 6 horas
	$xtotal = $xcostohoras;
	
	
	$consultadet = "insert al_venta_detalle (
		idalquilerdetalle,
		idalquiler,
		tipoalquiler,
		fechadesde,
		fechahasta,
		nrohoras,
		costohora,
		preciounitario,
		cantidad,
		total,
		detoriginal
		
		)values(
		
		'$xidprimario',
		'$xidalquiler',
		'$xtxttipoalquiler',
		'$xfechadesde',
		'$xfechahasta',
		'$xnrohoras',
		'$xcostohoras',
		'$xcostohoras',
		'$xnrohoras',
		'$xtotal',
		0 	
		)";
		
		if($mysqli->query($consultadet) == 1){
			//$Men = "Grabado";
			
			// Actualizar Fecha Fin 		
			$consultaact="update al_venta set
			fechafin = '$xfechahasta'
			where idalquiler = '$xidalquiler'";
			if($mysqli->query($consultaact) == 1){}else{
				printf("line ".__LINE__." - Errormessage: %s\n", $mysqli->error); exit;
			}
			
			//echo "Hola";
		}else{
			printf("line ".__LINE__." - Errormessage: %s\n", $mysqli->error); exit;
		}

		$mysqli->close();	
		//$_SESSION['msgerror'] = $Men; 
		header("Location: ../../alquilar-detalle.php?idhabitacion=$xidhabitacion&idalquiler=$xidalquiler"); 
		exit; 
}


//1. ALQUILER POR HORAS - ACTUALIZACION 1 DIA*********************************************************************************
if($xtxttipoalquiler == 7){
	$xcostohoras = $_POST['txtpreciodia12'];
	$xnrohoras = 12;
	
	//FECHA SACAR DE ULTIMA FECHA
	$sqlconsulta = $mysqli->query("select idalquiler, fechafin from al_venta where idalquiler = '$xidalquiler'");
	$aFila = $sqlconsulta->fetch_row();
	$xfechadesde = $aFila['1'];
	
	//$xfechahasta = sumarhoraafecha2(12,$xfechadesde); //Fecha hasta adicionando 6 horas
	$xtotal = $xcostohoras;
	
	$xFechaDiaAdicional = $_POST['txtFechaDiaAdicional'];
	$fh=explode(" ",$xFechaDiaAdicional);
	$f1=explode("/",$fh[0]);
	$newfecha1=$f1[2].'-'.$f1[1].'-'.$f1[0];
	//print_r ($xFechaDiaAdicional);
	$finalfechahasta = $newfecha1." ".$fh[1].":00";
	
	//debug_to_console( $xFechaDiaAdicional );

	$consultadet = "insert al_venta_detalle (
		idalquilerdetalle,
		idalquiler,
		tipoalquiler,
		fechadesde,
		fechahasta,
		nrohoras,
		costohora,
		preciounitario,
		cantidad,
		total,
		detoriginal
		
		)values(
		
		'$xidprimario',
		'$xidalquiler',
		'$xtxttipoalquiler',
		'$xfechadesde',
		'$finalfechahasta',
		'$xnrohoras',
		'$xcostohoras',
		'$xcostohoras',
		'$xnrohoras',
		'$xtotal',
		0 	
		)";
		
		if($mysqli->query($consultadet) == 1){
			//$Men = "Grabado";
			
			// Actualizar Fecha Fin 		
			$consultaact="update al_venta set
			fechafin = '$finalfechahasta'
			where idalquiler = '$xidalquiler'";
			if($mysqli->query($consultaact) == 1){}else{
				printf("line ".__LINE__." - Errormessage: %s\n", $mysqli->error); exit;
			}
			
			//echo "Hola";
		}else{
			printf("line ".__LINE__." - Errormessage: %s\n", $mysqli->error); exit;
		}

		$mysqli->close();	
		//$_SESSION['msgerror'] = $Men; 
		header("Location: ../../alquilar-detalle.php?idhabitacion=$xidhabitacion&idalquiler=$xidalquiler"); 
		exit; 
}


?>