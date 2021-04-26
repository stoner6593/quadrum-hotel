<?php
	session_start();
	include "../../config.php";
	include "../functions.php";
	date_default_timezone_set('America/Lima');
	
	
	$xidturno = isset($_SESSION['idturno']) ? $_SESSION['idturno'] : 0;
	$idusuario = $_SESSION['xyzidusuario'];
	$Men='';
	//--------------------------------------------------------------
	$TblMax = $mysqli->query("select max(idalquiler) from al_venta");
	$Contador = $TblMax->fetch_row();
	$xidprimario = $Contador['0'] + 1 ;
	echo $xidprimario ;
	//Generar Numero
	$TblMaxo = $mysqli->query("select max(nroorden) from al_venta");
	$cont = $TblMaxo->fetch_row();
	$txtnumero = $cont['0'];
	
	if($txtnumero == 0){
		$txtnumero = 1000 + 1;
	}else{
		$txtnumero = $txtnumero + 1;
	}

	$xtxthuesped = $_POST['txtidcliente'];
	$xtxtidhabitacion = $_POST['txtidhabitacion'];
	$xtxtnrohabitacion = $_POST['txtnrohabitacion'];
	
	$xtxttipoalquiler = @$_POST['txttipoalquiler'];
	$xformapago = $_POST['txtformadepago'];
	$txttipooperacion = isset($_POST['txttipooperacion']) ? $_POST['txttipooperacion'] : 1; //1-VENTA / 2-CORTESIA
	
	$xtotal = str_replace(",", "", $_POST['txtcostototal']);
	$xtotalhabitacion = $_POST['txtcostototalhabitacion']; //
	$xtotalproducto = $_POST['txtcostototalproducto']; //

	$xingresoEfectivo = $_POST["txtingresoEfectivo"];
	
	$opcionReserva = @$_POST["opcionReserva"] ;//? $_POST["opcionReserva"]  : "";
	//echo $opcionReserva ;
	$descuento = $_POST['descuentoglobal']; //
	if($descuento =="" || $descuento==0){
		$descuento=0;
	}else{
		$descuento=$descuento;
	}
	//echo $descuento;
	
	if($xformapago == 1){
		$montoefectivo = $xtotal;
		$montovisa = 0;
		$montomastercard = 0;
		
	}else if($xformapago == 2){
		$montoefectivo = 0;
		$montovisa = $xtotal;
		$montomastercard = 0;
		
	}else if($xformapago == 3){
		$montoefectivo = str_replace(",", "", $_POST['txtmontoefectivo']);
		$montovisa = str_replace(",", "", $_POST['txtmontovisa']);
		$montomastercard = str_replace(",", "", $_POST['txtmontomastercard']);
	}else if($xformapago == 4){
		$montoefectivo = 0;
		$montovisa = 0;
		$montomastercard = $xtotal;
	}
	
	//GRABAR INFORMACION DE ALQUILER
	
	$consulta="insert into al_venta (
		idalquiler,
		idhuesped,
		idhabitacion,
		nrohabitacion,
		tipooperacion,
		totalefectivo,
		totalvisa,
		totalmastercard,
		motivoanulacion,
		total,
		idturno,
		idusuario,
		fechafin,
		renovacion,
		nroorden,
		comentarios,
		descuento,
		estadoalquiler,
		reserva
		
		) values (
		
		'$xidprimario',
		'$xtxthuesped',
		'$xtxtidhabitacion',
		'$xtxtnrohabitacion',
		'$txttipooperacion',
		'$montoefectivo',
		'$montovisa',
		'$montomastercard',
		'',
		'$xtotal',
		'$xidturno',
		'$idusuario',
		'1900-01-01',
		'0',
		'$txtnumero',
		'',
		'$descuento',
		'1',
		'$opcionReserva'
		
		)";
		
	if($mysqli->query($consulta) == 1){
	
		//GRABAR DETALLE ALQUILER HABITACION
		$sqltmp = $mysqli->query("select
			idtmp,
			tipoalquiler,
			fechadesde,
			fechahasta,
			nrohoras,
			nrodias,
			costohora,
			costodia,
			formapago,
			totalefectivo,
			totalvisa,			
			estadopago,
			costoingresoanticipado,
			horaadicional,
			costohoraadicional,
			huespedadicional,
			costohuespedadicional,
			preciounitario,
			cantidad,
			total,
			tiporeserva,
			totalmastercard
			
			from alhab_detalle_tmp where idusuario='$idusuario ' order by idtmp asc ");

			$orig = 1;

		$sqltmpC = $mysqli->query("select
			count(*)			
			from alhab_detalle_tmp where idusuario='$idusuario ' order by idtmp asc ");
		$aFilaC = $sqltmpC->fetch_row();

			$montoEfXDet = $montoefectivo/$aFilaC[0];
			$montoVisXDet = $montovisa/$aFilaC[0];
			$montoMCxDet = $montomastercard/$aFilaC[0];
		
			while ($aFila = $sqltmp->fetch_row()){
				
				$TblMax = $mysqli->query("select max(idalquilerdetalle) from al_venta_detalle");
				$Contador = $TblMax->fetch_row();
				$xidprimariodetalle = $Contador['0'] + 1 ;
				
				$tipoalquiler = $aFila['1'];
				$fechadesde = isset($aFila['2']) ? $aFila['2'] : date('Y-m-d H:i:s');
				$fechahasta =  isset($aFila['3']) ? $aFila['3'] : date('Y-m-d H:i:s');
				$nrohoras = $aFila['4'] == "" ? 0 : $aFila['4'];
				$nrodias = $aFila['5'] == "" ? 0 : $aFila['5'];
				$costohora = $aFila['6'] == "" ? 0.00 : $aFila['6'];
				$costodia = $aFila['7'] == "" ? 0 : $aFila['7'];
				$formapago = $aFila['8'] == "" ? 0 : $aFila['8'];
				$totalefectivo = $aFila['9'] == "" ? $montoEfXDet : $aFila['9'];
				$totalvisa = $aFila['10'] == "" ? $montoVisXDet : $aFila['10'];
				$totalmastercard = $aFila['21'] == "" ? $montoMCxDet : $aFila['21'];
				$estadopago = 1;
				$costoingresoanticipado = $aFila['12'] == "" ? 0 : $aFila['12'];
				$horaadicional = $aFila['13'] == "" ? 0 : $aFila['13'];
				$costohoraadicional = $aFila['14'] == "" ? 0 : $aFila['14'];
				$huespedadicional = $aFila['15'] == "" ? 0 : $aFila['15'];
				$costohuespedadicional = $aFila['16'] == "" ? 0 : $aFila['16'];
				$preciounitario = $aFila['17'];
				$cantidad = $aFila['18'];
				$total = $aFila['19'];
				$tiporeserva = $aFila['20'];
		
				$sqlconsultadetalle = "insert into al_venta_detalle (
				
				idalquilerdetalle,	
				idalquiler,	
				tipoalquiler,	
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
				huespedadicional,	
				costohuespedadicional,	
				preciounitario,	
				cantidad,	
				total,	
				idturno,	
				idusuario,
				tiporeserva,
				detoriginal
				
				)values(
				
				'$xidprimariodetalle',
				'$xidprimario',
				'$tipoalquiler',
				'$fechadesde',
				'$fechahasta',
				'$nrohoras',
				'$nrodias',
				'$costohora',
				'$costodia',
				'$formapago',
				'$totalefectivo',
				'$totalvisa',
				'$totalmastercard',
				'$estadopago',
				'$costoingresoanticipado',
				'$horaadicional',
				'$costohoraadicional',
				'$huespedadicional',
				'$costohuespedadicional',
				'$preciounitario',
				'$cantidad',
				'$total',
				'$xidturno',
				'$idusuario',
				'$tiporeserva',
				'$orig'
				
				)";
				if($tipoalquiler == 1 || $tipoalquiler == 2  || $tipoalquiler == 6 || $tipoalquiler == 7){
					// Actualizar Fecha Fin 		
					$consultaact="update al_venta set
					fechafin = '$fechahasta'
					where idalquiler = '$xidprimario'";
					if($mysqli->query($consultaact)){}
				}
				if($mysqli->query($sqlconsultadetalle) == 1){}else{
					printf("line ".__LINE__." - Errormessage: %s\n", $mysqli->error); exit;
				}

				$orig = 0;
				
			}//Fin de While
			
			//Eliminando Temporal de Alquiler
			$sSQL = "delete from alhab_detalle_tmp where idusuario='$idusuario '";
			if($mysqli->query($sSQL)){}
	
		//Actualizar Estado de Habitacion
		$consultaupdate = "update hab_venta set
		idestado = 2,
		idalquiler = '$xidprimario'
		where idhabitacion = '$xtxtidhabitacion'";
		if($mysqli->query($consultaupdate)){}
		
		
		//GUARDAR PRODUCTOS DE HABITACION
		if($_POST['txtnumeroproducto'] > 0){
		//Generar ID
		$TblMaxv = $mysqli->query("select max(idventa) from venta");
		$Contador = $TblMaxv->fetch_row();
		$xidprimariov = $Contador['0'] + 1 ;
		
		//Generar Numero
		$TblMax = $mysqli->query("select max(numero) from venta");
		$cont = $TblMax->fetch_row();
		$txtnumero = $cont['0'];
		
		if($txtnumero == 0){
			$txtnumero = 1000 + 1;
		}else{
			$txtnumero = $txtnumero + 1;
		}
		
		$txtcliente = $_POST['txtcliente'];
		$txtfecha = date("Y-m-d");
		$xhora = date('H:i:s');
		$txttotal = str_replace(',','',$_POST['txttotalproducto']);
		
		$tipooperacion = 1; //$_POST['tipooperacion']; //(1:venta/0:cortesia)
		$txtformadepago = 1; //$_POST['txtformadepago']; //(1:efectivo/2:visa)
		
		$estado = 1; // (1:pagado/0:anulado)
		$estadoturno = 1; // (1:abierto/0:cerrado)
		$idusuario = $_SESSION['xyzidusuario'];
		
		$xidcliente = $_POST['txtidcliente'];
		
		$consultav = "insert into venta (
			
			idventa,
			numero,
			cliente,
			fecha,
			hora,
			total,
			operacion,
			formapago,
			estado,
			estadoturno,
			idusuario,
			idcliente,
			idturno,
			idalquiler
				
			) values (
		
			'$xidprimariov',
			'$txtnumero',
			'$txtcliente',
			'$txtfecha',
			'$xhora',
			'$txttotal',
			'$tipooperacion',
			'$txtformadepago',
			'$estado',
			'$estadoturno',
			'$idusuario',
			'$xidcliente',
			'$xidturno',
			'$xidprimario'
					
			)";
			
		if($mysqli->query($consultav) == 1){
			$Men = "Los datos fueron guardados satisfactoriamente.";
			//Guardar Detalle Venta
			$sqltmp = $mysqli->query("select id, idproducto, nombre, cantidad, precio, importe from ventas_tmp where idusuario='$idusuario ' order by id asc");
			
			while($tmpFila = $sqltmp->fetch_row()){
				//Generar ID Venta Detalle
				$TblMaxDetalle = $mysqli->query("select max(idventadetalle) from ventadetalle");
				$ContaD = $TblMaxDetalle->fetch_row();
				$xidventadetalle = $ContaD['0'] + 1 ;
		
				$xidproducto = $tmpFila['1'];
				$xnombre = $tmpFila['2'];
				$xcantidad = $tmpFila['3'];
				$xprecio = $tmpFila['4'];
				$ximporte = $tmpFila['5'];
				
				$consultatmp = "insert into ventadetalle (
					idventadetalle,
					idventa,
					idproducto,
					nombre,
					cantidad,
					precio,
					importe,
					flag_venta_alquiler_inicial
					
					) values (
					
					'$xidventadetalle',
					'$xidprimariov',
					'$xidproducto',
					'$xnombre',
					'$xcantidad',
					'$xprecio',
					'$ximporte',
					1
					)";
				if($mysqli->query($consultatmp)){
					//Descontar Stock de Producto
					$sqlrestarcantidad = "update producto set
						cantidad = 	cantidad - '$xcantidad',
						vendidoturno = vendidoturno + '$xcantidad'		
						where idproducto = '$xidproducto'";
					if($mysqli->query($sqlrestarcantidad)){}
					
					
				}
			}		
			//Eliminando Temporales
			$sSQL="delete from ventas_tmp where idusuario='$idusuario '";
			if($mysqli->query($sSQL)){}
		}else{
			printf("line ".__LINE__." - Errormessage: %s\n", $mysqli->error); exit;
		}
		}
	
	}else{
		printf("line ".__LINE__." - Errormessage: %s\n", $mysqli->error); exit;
	}

	$xie = $xingresoEfectivo == "" ? 0 : $xingresoEfectivo;
	
	$consultaturno = "update ingresosturno set
		totalhabitacion = (totalhabitacion + $xie) ,
		totalproducto =  totalproducto + $xtotalproducto,
		totalefectivo = (totalefectivo + $montoefectivo) ,
		totalvisa = totalvisa + $montovisa,
		totalmastercard = totalmastercard + $montomastercard,
		totaldescuento = totaldescuento + $descuento
		where idturno = '$xidturno'";
		if($mysqli->query($consultaturno) == 1){}else{
			printf("line ".__LINE__." - Errormessage: %s\n", $mysqli->error); exit;
		}
		
	$mysqli->close();	
	$_SESSION['msgerror'] = $Men;
	header("Location:../../alquilar-detalle.php?idhabitacion=$xtxtidhabitacion&idalquiler=$xidprimario"); exit; 
	echo "<script>location.href = '../../alquilar-detalle.php?idhabitacion=$xtxtidhabitacion&idalquiler=$xidprimario';</script>";

?>