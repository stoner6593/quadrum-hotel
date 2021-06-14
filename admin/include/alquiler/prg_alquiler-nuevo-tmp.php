<?php
session_start();
include "../../config.php";
include "../functions.php";
include "../Configuraciones.php";

date_default_timezone_set('America/Lima');

//$horaMediaConfig = getConfig($mysqli,"HORA_MEDIA");
$configs = new Configuraciones($mysqli);
$horaMediaConfig = $configs->getConfig("HORA_MEDIA");
$horamedia = date('H:i', strtotime($horaMediaConfig));

$precioHorasAlquilerTarifa1 = $configs->getConfig("NRO_ALQUILER_HORAS_TARIFA1");
$precioHorasAlquilerTarifa2 = $configs->getConfig("NRO_ALQUILER_HORAS_TARIFA2");

//--------------------------------------------------------------
$TblMax = $mysqli->query("select max(idtmp) from alhab_detalle_tmp");
$Contador = $TblMax->fetch_row();
$xidprimario = $Contador['0'] + 1 ;

//Generar Numero
/*$TblMaxo = $mysqli->query("select max(nroorden) from al_venta");
$cont = $TblMaxo->fetch_row();
$txtnumero = $cont['0']; */

$xtxthuesped = $_POST['txtidcliente'];
$xtxtcliente = $_POST['txtcliente'];

$xtxtidhabitacion = $_POST['txtidhabitacion'];
$xtxthrohabitacion = $_POST['txtnrohabitacion'];
$xtipohabitacion = $_POST['txtidtipohabitacion'];

$xtxttipoalquiler = $_GET['idtipo'];

$txttipooperacion = @$_GET['txttipooperacion']; //1. Venta - 2. Cortesia

$_SESSION['xidcliente'] = $_POST['txtidcliente'];
$_SESSION['xcliente'] = $_POST['txtcliente'];


$idusuario = $_SESSION['xyzidusuario'];
//1. ALQUILER POR 4 HORAS 35 SOLES *********************************************************************************
if($xtxttipoalquiler == 1){
	$xcostohoras = $_POST['txtcostohoras'];
	$xnrohoras = $precioHorasAlquilerTarifa1;
	$xfechadesde = date('Y-m-d H:i:s'); //fecha de Hoy
	$xfechahasta = sumarhoraafecha($xnrohoras,$xfechadesde); //Fecha hasta adicionando 4 horas
	//echo $xfechadesde ;
	$xtotal = $xcostohoras;
	
	$txtcortesiahoras = $_POST['txtcortesiahoras']; //Si es cortesia el Alquiler
	if($txtcortesiahoras == 1){
		$xtotal = 0;
	}
	
	
	$consulta="insert into alhab_detalle_tmp(
		idtmp,
		tipoalquiler,
		fechadesde,
		fechahasta,
		nrohoras,
		costohora,
		preciounitario,
		cantidad,
		total,
		idusuario
		
		)values(
		
		'$xidprimario',
		'$xtxttipoalquiler',
		'$xfechadesde',
		'$xfechahasta',
		'$xnrohoras',
		'$xcostohoras',
		'$xcostohoras',
		'$xnrohoras',
		'$xtotal',
		'$idusuario'
		
		)";
		if($mysqli->query($consulta)){
			$Men = "Grabado"	;
		}
		$mysqli->close();	
		$_SESSION['msgerror'] = $Men;
		header("Location: ../../alquilar.php?idhabitacion=$xtxtidhabitacion&nrohabitacion=$xtxthrohabitacion&idtipohab=$xtipohabitacion&desdeactualizando=si"); 
		exit; 
}

//2. ALQUILER POR DIA  *********************************************************************************
if($xtxttipoalquiler == 2){
	$txtcostodiario = $_POST['txtcostodiario'];
	$txtfechadesde = $_POST['txtfechadesde'];
	$txtfechahasta = $_POST['txtfechahasta'];
	$txtnrodias =isset($_POST['txtnrodias']) ? $_POST['txtnrodias'] : 0;
	
	$xfechadesde = Cfecha($txtfechadesde).' '.date('H:i:s'); //fecha de Hoy
	
	$xfechahasta = Cfecha($_POST['txtfechahasta']).' '.date("12:00:00");//date('H:i:s'); //Fecha hasta 
	//$xtotal = $txtcostodiario * $txtnrodias;
	
	//Obtener Precios
	$sqlhabitacionprecio = $mysqli->query("select
		idhabitacion,
		piso,
		numero,
		idtipo,
		
		preciodiariodj,
		preciohorasdj,
		preciodiariovs,	
		preciohorasvs,
		
		nrohuespedes,
		nroadicional,	
		
		costopersonaadicional,
		costohoraadicional,
		
		caracteristicas,
		idestado,
		idalquiler,
		ubicacion,

		preciod_d, preciod_l, preciod_m, preciod_w, preciod_j, preciod_v, preciod_s,
		precioh_d, precioh_l, precioh_m, precioh_w, precioh_j, precioh_v, precioh_s,
		precio12_d, precio12_l, precio12_m, precio12_w, precio12_j, precio12_v, precio12_s

		from hab_venta where idhabitacion = $xtxtidhabitacion");
		$haFila = $sqlhabitacionprecio->fetch_row();
	
	//Calcular si Es fin de semana o Entresemana//
	$fecha = $xfechadesde;
	$tarifauno = 0;
	$tarifados = 0;
	$xtotal =0;

	for ($i = 1; $i <= $txtnrodias; $i++) {
    	
		$dia = date('w', strtotime($fecha));
		$hora = date('H:i',strtotime($fecha));
		$horamedia = date('H:i', strtotime($horaMediaConfig));
		//print_r($fecha);
        $fechaAlquiler=($fecha);//date_create
		
        $sqltarifaespecial = $mysqli->query("SELECT id_tarifa,    descripcion_tarifa,    fecha_tarifa,
        idtipo,    precio_dia,    precio_hora_1,    precio_hora_2,
        precio_hora_adicional,    precio_huesped_adicional,    estado_tarifa
        FROM tarifa_especial where fecha_tarifa = '".$fechaAlquiler."' and idtipo = $haFila[3] and estado_tarifa = 1 
        order by fecha_registro desc limit 1");
        $tarifaFila = $sqltarifaespecial->fetch_row();

        $row_cnt = $sqltarifaespecial->num_rows;

		//Uso de Switch Case
		switch ($dia) {
		case 0:
			if($i == 1){
				if($hora > $horamedia){
					//echo "Tarifa 2 - Viernes";
					//echo $hora."-".$horamedia;
					$xpreciodiario = $haFila['16'];

                    if($row_cnt > 0){
                        $xpreciodiario = $tarifaFila['4'];
                    }

				 	$xtotal = $xtotal + $xpreciodiario;
				}else{
					//echo "Tarifa 1 :: Domingo - Jueves ";
					$xpreciodiario = $haFila['22'];
					$xtotal = $xtotal + $xpreciodiario;
				}
			}else{
				//echo "Tarifa 2 - Viernes";
				$xpreciodiario = $haFila['16'];

                if($row_cnt > 0){
                    $xpreciodiario = $tarifaFila['4'];
                }

				$xtotal = $xtotal + $xpreciodiario;
			}
			break;
		case 1:
			if($i == 1){
				if($hora > $horamedia){
					//echo "Tarifa 2 - Viernes";
					//echo $hora."-".$horamedia;
					$xpreciodiario = $haFila['17'];

                    if($row_cnt > 0){
                        $xpreciodiario = $tarifaFila['4'];
                    }

				 	$xtotal = $xtotal + $xpreciodiario;
				}else{
					//echo "Tarifa 1 :: Domingo - Jueves ";
					$xpreciodiario = $haFila['16'];
					$xtotal = $xtotal + $xpreciodiario;
				}
			}else{
				//echo "Tarifa 2 - Viernes";
				$xpreciodiario = $haFila['17'];

                if($row_cnt > 0){
                    $xpreciodiario = $tarifaFila['4'];
                }

				$xtotal = $xtotal + $xpreciodiario;
			}
			break;
		case 2:
			if($i == 1){
				if($hora > $horamedia){
					//echo "Tarifa 2 - Viernes";
					//echo $hora."-".$horamedia;
					$xpreciodiario = $haFila['18'];

                    if($row_cnt > 0){
                        $xpreciodiario = $tarifaFila['4'];
                    }

				 	$xtotal = $xtotal + $xpreciodiario;
				}else{
					//echo "Tarifa 1 :: Domingo - Jueves ";
					$xpreciodiario = $haFila['17'];
					$xtotal = $xtotal + $xpreciodiario;
				}
			}else{
				//echo "Tarifa 2 - Viernes";
				$xpreciodiario = $haFila['18'];

                if($row_cnt > 0){
                    $xpreciodiario = $tarifaFila['4'];
                }

				$xtotal = $xtotal + $xpreciodiario;
			}
			break;
		case 3:
			if($i == 1){
				if($hora > $horamedia){
					//echo "Tarifa 2 - Viernes";
					//echo $hora."-".$horamedia;
					$xpreciodiario = $haFila['19'];

                    if($row_cnt > 0){
                        $xpreciodiario = $tarifaFila['4'];
                    }

				 	$xtotal = $xtotal + $xpreciodiario;
				}else{
					//echo "Tarifa 1 :: Domingo - Jueves ";
					$xpreciodiario = $haFila['18'];
					$xtotal = $xtotal + $xpreciodiario;
				}
			}else{
				//echo "Tarifa 2 - Viernes";
				$xpreciodiario = $haFila['19'];

                if($row_cnt > 0){
                    $xpreciodiario = $tarifaFila['4'];
                }

				$xtotal = $xtotal + $xpreciodiario;
			}
			break;
		case 4:
			if($i == 1){
				if($hora > $horamedia){
					//echo "Tarifa 2 - Viernes";
					//echo $hora."-".$horamedia;
					$xpreciodiario = $haFila['20'];

                    if($row_cnt > 0){
                        $xpreciodiario = $tarifaFila['4'];
                    }

				 	$xtotal = $xtotal + $xpreciodiario;
				}else{
					//echo "Tarifa 1 :: Domingo - Jueves ";
					$xpreciodiario = $haFila['19'];
					$xtotal = $xtotal + $xpreciodiario;
				}
			}else{
				//echo "Tarifa 2 - Viernes";
				$xpreciodiario = $haFila['20'];

                if($row_cnt > 0){
                    $xpreciodiario = $tarifaFila['4'];
                }

				$xtotal = $xtotal + $xpreciodiario;
			}
			break;
		case 5:
			if($i == 1){
				if($hora > $horamedia){
					//echo "Tarifa 2 - Viernes";
					//echo $hora."-".$horamedia;
					$xpreciodiario = $haFila['21'];

                    if($row_cnt > 0){
                        $xpreciodiario = $tarifaFila['4'];
                    }

				 	$xtotal = $xtotal + $xpreciodiario;
				}else{
					//echo "Tarifa 1 :: Domingo - Jueves ";
					$xpreciodiario = $haFila['20'];
					$xtotal = $xtotal + $xpreciodiario;
				}
			}else{
				//echo "Tarifa 2 - Viernes";
				$xpreciodiario = $haFila['21'];

                if($row_cnt > 0){
                    $xpreciodiario = $tarifaFila['4'];
                }

				$xtotal = $xtotal + $xpreciodiario;
			}
			break;
		case 6:
			if($i == 1){
				if($hora > $horamedia){
					//echo "Tarifa 2 - Viernes";
					//echo $hora."-".$horamedia;
					$xpreciodiario = $haFila['22'];

                    if($row_cnt > 0){
                        $xpreciodiario = $tarifaFila['4'];
                    }

				 	$xtotal = $xtotal + $xpreciodiario;
				}else{
					//echo "Tarifa 1 :: Domingo - Jueves ";
					$xpreciodiario = $haFila['21'];
					$xtotal = $xtotal + $xpreciodiario;
				}
			}else{
				//echo "Tarifa 2 - Viernes";
				$xpreciodiario = $haFila['22'];

                if($row_cnt > 0){
                    $xpreciodiario = $tarifaFila['4'];
                }

				$xtotal = $xtotal + $xpreciodiario;
			}
			break;
		}
		/*
		echo "Total: ".$xtotal."<br>";
		echo "Dia: ".$dia."<br>";
		echo "Fecha: ".$fecha."<br>";
		*/
		$fecha = date("Y-m-d H:i:s", strtotime("$fecha + 1 day")); //Aumente en un dia
	} // Fin de For
	
	//$comentarios = 'T1: '.$tarifauno.' - T2: '.$tarifados;
	
	
	
	$txtcortesiadias = @$_POST['txtcortesiadias']; //Si es cortesia el Alquiler
	if($txtcortesiadias == 1){
		$xtotal = 0;
	}
	
	$consulta="insert into alhab_detalle_tmp (
		idtmp,
		tipoalquiler,
		fechadesde,
		fechahasta,
		nrodias,
		costodia,
		preciounitario,
		cantidad,
		total,
		comentarios,
		idusuario
		
		)values(
		
		'$xidprimario',
		'$xtxttipoalquiler',
		'$xfechadesde',
		'$xfechahasta',
		$txtnrodias,
		'$txtcostodiario',
		'$txtcostodiario',
		'$txtnrodias',
		'$xtotal',
		'$comentarios',
		'$idusuario'
		
		)";
		if($mysqli->query($consulta)){
			$Men = "Grabado"	;
		}
		$mysqli->close();	
		$_SESSION['msgerror'] = $Men;
		
		header("Location: ../../alquilar.php?idhabitacion=$xtxtidhabitacion&nrohabitacion=$xtxthrohabitacion&idtipohab=$xtipohabitacion&desdeactualizando=si");
		exit;
	
}


//3. HORA ADICIONAL


//4. HUESPED ADICIONAL
if($xtxttipoalquiler == 4){
	$txtprecioadicionalhora = $_POST['txtprecioadicionalhora'];
	$txtocupantesadicionaleshoras = $_POST['txtocupantesadicionaleshoras'];
	$xtotal = $txtprecioadicionalhora * $txtocupantesadicionaleshoras;
	$xfechadesde = date('Y-m-d H:i:s'); //fecha de Hoy

	$consulta="insert into alhab_detalle_tmp(
		idtmp,
		tipoalquiler,
		fechadesde,
		huespedadicional,
		costohuespedadicional,
		preciounitario,
		cantidad,
		total,
		idusuario
		
		)values(
		
		'$xidprimario',
		'$xtxttipoalquiler',
		'$xfechadesde',
		'$txtocupantesadicionaleshoras',
		'$txtprecioadicionalhora',
		'$txtprecioadicionalhora',
		'$txtocupantesadicionaleshoras',
		'$xtotal',
		'$idusuario'
		
		)";
		if($mysqli->query($consulta)){
			$Men = "Grabado"	;
		}
		$mysqli->close();	
		$_SESSION['msgerror'] = $Men;
		header("Location: ../../alquilar.php?idhabitacion=$xtxtidhabitacion&nrohabitacion=$xtxthrohabitacion&idtipohab=$xtipohabitacion&desdeactualizando=si"); 
		exit; 
	
}

//5. INGRESO ANTICIPADO
if ($xtxttipoalquiler == 5){
	
	$costoingresoanticipado = $_POST['txtcostoingresoanticipado'];
	$cant = $_POST['txtnrohoras'];
	$xtotal = $costoingresoanticipado * $cant;
	
	
	$consulta="insert into alhab_detalle_tmp(
		idtmp,
		tipoalquiler,
		costoingresoanticipado,
		preciounitario,
		cantidad,
		total,
		idusuario
		
		)values(
		
		'$xidprimario',
		'$xtxttipoalquiler',
		'$costoingresoanticipado',
		'$costoingresoanticipado',
		'$cant',
		'$xtotal',
		'$idusuario'
		
		)";
		if($mysqli->query($consulta)){
			$Men = "Grabado"	;
		}
		$mysqli->close();	
		$_SESSION['msgerror'] = $Men;
		header("Location: ../../alquilar.php?idhabitacion=$xtxtidhabitacion&nrohabitacion=$xtxthrohabitacion&idtipohab=$xtipohabitacion&desdeactualizando=si"); 
		exit; 
}


/*PRUEBA ALQUILER 12 HORAS*/

//2. ALQUILER 6 POR HORAS 50 SOLES  *********************************************************************************
if($xtxttipoalquiler == 6){
	$txtcostodiario = $_POST['txtcostohoras12'];
	$xnrohoras = $precioHorasAlquilerTarifa2;
	$txtfechadesde = date('Y-m-d H:i:s'); //fecha de Hoy
	$txtfechahasta = sumarhoraafecha($xnrohoras,$txtfechadesde); //Fecha hasta adicionando 6 horas


	//$txtnrodias = $_POST['txtnrodias'];
	
	$xfechadesde = ($txtfechadesde); //fecha de Hoy
	
	$xfechahasta = $txtfechahasta; //Fecha hasta 
	//$xtotal = $txtcostodiario * $txtnrodias;
	
	//Obtener Precios
	$sqlhabitacionprecio = $mysqli->query("select
		idhabitacion,
		piso,
		numero,
		idtipo,
		
		precio12,
		preciohorasdj,
		precio12vs,	
		preciohorasvs,
		
		nrohuespedes,
		nroadicional,	
		
		costopersonaadicional,
		costohoraadicional,
		
		caracteristicas,
		idestado,
		idalquiler,
		ubicacion
		from hab_venta where idhabitacion = $xtxtidhabitacion");
		$haFila = $sqlhabitacionprecio->fetch_row();
	
	//Calcular si Es fin de semana o Entresemana//
	$fecha = $xfechadesde;
	$tarifauno = 0;
	$tarifados = 0;
	
	for ($i = 1; $i <= 1; $i++) {
    	
		$dia = date('w', strtotime($fecha));
		$hora = date('H:i',strtotime($fecha));
		$horamedia = date('H:i', strtotime($horaMediaConfig));
		//echo $dia;
		//Uso de Switch Case
		switch ($dia) {
		case 0:
			if($i == 1){
				if($hora > $horamedia){
					//echo "Tarifa 2 - Viernes";
					$xpreciodiario = $haFila['4'];
				 	$xtotal = $xtotal + $xpreciodiario;
				}else{
					//echo "Tarifa 1 :: Domingo - Jueves ";
					$xpreciodiario = $haFila['6'];
					$xtotal = $xtotal + $xpreciodiario;
				}
			}else{
				//echo "Tarifa 2 - Viernes";
				$xpreciodiario = $haFila['4'];
				$xtotal = $xtotal + $xpreciodiario;
			}
			break;
		case 1:
		case 2:
		case 3:
		case 4:
			//echo "Tarifa 1 :: Domingo - Jueves ";
			if($hora > $horamedia){
				$xpreciodiario = $haFila['4'];
			}else{
				$xpreciodiario = $haFila['4'];
			}	
			
			$xtotal = $xtotal + $xpreciodiario;
			break;
		case 5:
			//echo $fecha.'/'.$dia.'/'.$hora.'/'.$horamedia;
			if($i == 1){
				if($hora > $horamedia){
					//echo "Tarifa 1 :: Domingo - Jueves ";
					$xpreciodiario = $haFila['6'];
					$xtotal = $xtotal + $xpreciodiario;
				}else{
					//echo "Tarifa 2 - Viernes";
					$xpreciodiario = $haFila['4'];
					$xtotal = $xtotal + $xpreciodiario;
				}
			}else{
				//echo "Tarifa 2 - Viernes";
				$xpreciodiario = $haFila['4'];
				$xtotal = $xtotal + $xpreciodiario;
			}
			break;
		case 6:
			if($i == 1){
				//echo $hora ."-". $horamedia;
				if($hora > $horamedia){
					//echo "Tarifa 2 - Viernes";
					$xpreciodiario = $haFila['6'];
				 	$xtotal = $xtotal + $xpreciodiario;
				}else{
					//echo "Tarifa 1 :: Domingo - Jueves ";
					$xpreciodiario = $haFila['6'];
					$xtotal = $xtotal + $xpreciodiario;
				}
			}else{
				//echo "Tarifa 2 - Viernes";
				$xpreciodiario = $haFila['4'];
				$xtotal = $xtotal + $xpreciodiario;
			}
			break;
		}
		/*
		echo "Total: ".$xtotal."<br>";
		echo "Dia: ".$dia."<br>";
		echo "Fecha: ".$fecha."<br>";
		*/
		$fecha = date("Y-m-d H:i:s", strtotime("$fecha + 1 day")); //Aumente en un dia
	} // Fin de For
	
	//$comentarios = 'T1: '.$tarifauno.' - T2: '.$tarifados;
	
	
	
	$txtcortesiadias = $_POST['txtcortesiadias']; //Si es cortesia el Alquiler
	if($txtcortesiadias == 1){
		$xtotal = 0;
	}
	
	
	$consulta="insert into alhab_detalle_tmp (
		idtmp,
		tipoalquiler,
		fechadesde,
		fechahasta,
		nrohoras,
		costohora,
		preciounitario,
		cantidad,
		total,
		idusuario
		
		
		)values(
		
		'$xidprimario',
		'$xtxttipoalquiler',
		'$xfechadesde',
		'$xfechahasta',
		'$xnrohoras',
		'$txtcostodiario',
		'$txtcostodiario',
		'$xnrohoras',
		'$xtotal',
		'$idusuario'
		
		)";
		if($mysqli->query($consulta)){
			$Men = "Grabado"	;
		}
		$mysqli->close();	
		$_SESSION['msgerror'] = $Men;
		header("Location: ../../alquilar.php?idhabitacion=$xtxtidhabitacion&nrohabitacion=$xtxthrohabitacion&idtipohab=$xtipohabitacion&desdeactualizando=si"); 
		exit; 
	
}

//2. ALQUILER POR DIA RESERVA *********************************************************************************
if($xtxttipoalquiler == 7){
    $txtcostodiario = $_POST['txtcostodiarioreserva'];
    $txtfechadesde = $_POST['txtfechadesdereserva'];
    $txtfechahasta = $_POST['txtfechahastareserva'];
    $txtnrodias = $_POST['txtnrodiasreserva'];

    $cboTipoReserva = $_POST["cboTipoReserva"];

    $xfechadesde = Cfecha($txtfechadesde).' '.date('H:i:s'); //fecha de Hoy

    $xfechahasta = Cfecha($_POST['txtfechahastareserva']).' '.date("12:00:00");//date('H:i:s'); //Fecha hasta
    //$xtotal = $txtcostodiario * $txtnrodias;

    $xtotal = (float)$txtcostodiario * (int)$txtnrodias;

    $txtcortesiadias = $_POST['txtcortesiadias']; //Si es cortesia el Alquiler
    if($txtcortesiadias == 1){
        $xtotal = 0;
    }

    $consulta="insert into alhab_detalle_tmp (
		idtmp,
		tipoalquiler,
		fechadesde,
		fechahasta,
		nrodias,
		costodia,
		preciounitario,
		cantidad,
		total,
		comentarios,
		tiporeserva,
		idusuario
		
		)values(
		
		'$xidprimario',
		'$xtxttipoalquiler',
		'$xfechadesde',
		'$xfechahasta',
		'$txtnrodias',
		'$txtcostodiario',
		'$txtcostodiario',
		'$txtnrodias',
		'$xtotal',
		'$comentarios',
		'$cboTipoReserva',
		'$idusuario'
		
		)";
    if($mysqli->query($consulta)){
        $Men = "Grabado"	;
    }
    $mysqli->close();
    $_SESSION['msgerror'] = $Men;
    header("Location: ../../alquilar.php?idhabitacion=$xtxtidhabitacion&nrohabitacion=$xtxthrohabitacion&idtipohab=$xtipohabitacion&desdeactualizando=si");
    exit;

}

?>