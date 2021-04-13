<?php
//ini_set('display_errors', 1);

include "validar.php";
include "config.php";
include "include/functions.php";
include "include/Configuraciones.php";
date_default_timezone_set('America/Lima');
$xidhabitacion = $_GET['idhabitacion'];
$xidalquiler = $_GET['idalquiler'];
$xidturno = $_SESSION['idturno'];
$xidusuario = $_SESSION['xyzidusuario'];

//Viene de historial de huespedes
$historia = @$_GET['historia'];

$sqlalquiler = $mysqli->query("select
	al_venta.idalquiler,
	al_venta.idhuesped,
	al_venta.idhabitacion,
	al_venta.nrohabitacion,
	al_venta.tipooperacion,
	al_venta.total,

	cliente.idhuesped,
	cliente.nombre,

	al_venta.comentarios,
	al_venta.nroorden,
	al_venta.codigo_respuesta,
	al_venta.descuento,
	al_venta.fechafin,

	cliente.RUC,
	cliente.razon_social,
	cliente.direccion,

	cliente.tipo_documento

	from al_venta inner join cliente on cliente.idhuesped = al_venta.idhuesped
	where al_venta.idalquiler = '$xidalquiler'
	");

	$xaFila = $sqlalquiler->fetch_row();
	$xnrohabitacion = $xaFila['3'];

//Detalle Aquiler
$cuentaProductos=0;
$cuentaAlquiler=0;

$sqldetalle = $mysqli->query("select
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
	procesado,
	totalmastercard

	from al_venta_detalle
	where idalquiler = '$xidalquiler' order by idalquilerdetalle asc
	");


$sqldetalleA = $mysqli->query("select
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
	procesado,
	totalmastercard

	from al_venta_detalle
	where idalquiler = '$xidalquiler' order by idalquilerdetalle asc
	");

$sqlhabitaciontipo = $mysqli->query("select
	hab_venta.idhabitacion,
	hab_venta.idtipo,
	hab_venta.numero,
	hab_venta.nrohuespedes,
	hab_venta.nroadicional,

	hab_tipo.idtipo,
	hab_tipo.nombre,

	hab_tipo.preciohoraadicionaluno,
	hab_tipo.preciohuespedadicionaluno,

	hab_tipo.preciohoraadicionaldos,
	hab_tipo.preciohuespedadicionaldos,

	hab_tipo.preciodiariouno,
	hab_tipo.preciohorauno,

	hab_tipo.preciodiariodos,
	hab_tipo.preciohorados,

	hab_venta.preciodiariodj,
	hab_venta.preciohorasdj,

	hab_venta.preciodiariovs,
	hab_venta.preciohorasvs,

	hab_venta.costopersonaadicional,
	hab_venta.costohoraadicional,

	hab_venta.preciod_d,
	hab_venta.preciod_l,
	hab_venta.preciod_m,
	hab_venta.preciod_w,
	hab_venta.preciod_j,
	hab_venta.preciod_v,
	hab_venta.preciod_s,

	hab_venta.precioh_d,
	hab_venta.precioh_l,
	hab_venta.precioh_m,
	hab_venta.precioh_w,
	hab_venta.precioh_j,
	hab_venta.precioh_v,
	hab_venta.precioh_s

	from hab_venta inner join hab_tipo on hab_tipo.idtipo = hab_venta.idtipo
	where hab_venta.idhabitacion = '$xidhabitacion'");

	$xhFila = $sqlhabitaciontipo->fetch_row();
	$nroadicional = $xhFila['4']; //Ocupantes Adicionales permitidos
	$xidtipohabitacion = $xhFila['5'];
	$xtipohabitacion = $xhFila['6'];
	//$xpreciopordia =
	//$xprecioporhora =


	$fechahoy = Cfecha(date('Y-m-d'));

	//CONTROLAR ENTRE SEMANA Y FIN DE SEMANA
	//Domingo=0 - Lunes=1 - Martes=2 - Miercoles=3 - Jueves=4 - Viernes=5 - Sabado=6
	$xhoy = date('Y-m-d H:i:s');

	$dia = date('w', strtotime($xhoy));
	$hora = date('H:i',strtotime($xhoy));

//    $horaMediaConfig = getConfig($mysqli,"HORA_MEDIA");
//	$horamedia = date('H:i', strtotime($horaMediaConfig));

    $configs = new Configuraciones($mysqli);
    $horaMediaConfig = $configs->getConfig("HORA_MEDIA");
    $horamedia = date('H:i', strtotime($horaMediaConfig));

    $precioHorasAlquilerTarifa1 = $configs->getConfig("NRO_ALQUILER_HORAS_TARIFA1");
    $precioHorasAlquilerTarifa2 = $configs->getConfig("NRO_ALQUILER_HORAS_TARIFA2");

    $fechaHoy=date_create();
    $sqltarifaespecial = $mysqli->query("SELECT id_tarifa,    descripcion_tarifa,    fecha_tarifa,
            idtipo,    precio_dia,    precio_hora_1,    precio_hora_2,
            precio_hora_adicional,    precio_huesped_adicional,    estado_tarifa
            FROM tarifa_especial where fecha_tarifa = '".$fechaHoy->format('Y-m-d')."' and idtipo = $xhFila[1] and estado_tarifa = 1
            order by fecha_registro desc limit 1");
    $tarifaFila = $sqltarifaespecial->fetch_row();

    $row_cnt = $sqltarifaespecial->num_rows;

    $preciodiario = 0;

	switch ($dia) {
		case 0:
			if($hora > $horamedia){
				//echo "Tarifa 1 :: Domingo - Jueves ";
				$nombreprecio = "Aplica Precios Entre Semana";
				$preciodiario = $xhFila['21'];
				$preciohora = $xhFila['28'];
				$xpreciohoraadicional = $xhFila['20'];
				$xpreciohuespedadicional = $xhFila['19'];

				if($row_cnt > 0){
					$xpreciodiario = $tarifaFila['4'];
					$xpreciohora = $tarifaFila['5'];
					$xpreciohoraadicional = $tarifaFila['7'];
					$xpreciohuespedadicional = $tarifaFila['8'];
				}
			}else{
				//echo "Tarifa 2 - Viernes";
				$nombreprecio = "Aplica Precios Fin Semana";
				$preciodiario = $xhFila['27'];
				$preciohora = $xhFila['34'];
				$xpreciohoraadicional = $xhFila['20'];
				$xpreciohuespedadicional = $xhFila['19'];
			};
			break;
		case 1:
			if($hora > $horamedia){
				//echo "Tarifa 1 :: Domingo - Jueves ";
				$nombreprecio = "Aplica Precios Entre Semana";
				$preciodiario = $xhFila['22'];
				$preciohora = $xhFila['29'];
				$xpreciohoraadicional = $xhFila['20'];
				$xpreciohuespedadicional = $xhFila['19'];

				if($row_cnt > 0){
					$xpreciodiario = $tarifaFila['4'];
					$xpreciohora = $tarifaFila['5'];
					$xpreciohoraadicional = $tarifaFila['7'];
					$xpreciohuespedadicional = $tarifaFila['8'];
				}
			}else{
				//echo "Tarifa 2 - Viernes";
				$nombreprecio = "Aplica Precios Fin Semana";
				$preciodiario = $xhFila['21'];
				$preciohora = $xhFila['28'];
				$xpreciohoraadicional = $xhFila['20'];
				$xpreciohuespedadicional = $xhFila['19'];
			};
			break;
		case 2:
			if($hora > $horamedia){
				//echo "Tarifa 1 :: Domingo - Jueves ";
				$nombreprecio = "Aplica Precios Entre Semana";
				$preciodiario = $xhFila['23'];
				$preciohora = $xhFila['30'];
				$xpreciohoraadicional = $xhFila['20'];
				$xpreciohuespedadicional = $xhFila['19'];

				if($row_cnt > 0){
					$xpreciodiario = $tarifaFila['4'];
					$xpreciohora = $tarifaFila['5'];
					$xpreciohoraadicional = $tarifaFila['7'];
					$xpreciohuespedadicional = $tarifaFila['8'];
				}
			}else{
				//echo "Tarifa 2 - Viernes";
				$nombreprecio = "Aplica Precios Fin Semana";
				$preciodiario = $xhFila['22'];
				$preciohora = $xhFila['29'];
				$xpreciohoraadicional = $xhFila['20'];
				$xpreciohuespedadicional = $xhFila['19'];
			};
			break;
		case 3:
			if($hora > $horamedia){
				//echo "Tarifa 1 :: Domingo - Jueves ";
				$nombreprecio = "Aplica Precios Entre Semana";
				$preciodiario = $xhFila['24'];
				$preciohora = $xhFila['31'];
				$xpreciohoraadicional = $xhFila['20'];
				$xpreciohuespedadicional = $xhFila['19'];

				if($row_cnt > 0){
					$xpreciodiario = $tarifaFila['4'];
					$xpreciohora = $tarifaFila['5'];
					$xpreciohoraadicional = $tarifaFila['7'];
					$xpreciohuespedadicional = $tarifaFila['8'];
				}
			}else{
				//echo "Tarifa 2 - Viernes";
				$nombreprecio = "Aplica Precios Fin Semana";
				$preciodiario = $xhFila['23'];
				$preciohora = $xhFila['30'];
				$xpreciohoraadicional = $xhFila['20'];
				$xpreciohuespedadicional = $xhFila['19'];
			};
			break;
		case 4:
			if($hora > $horamedia){
				//echo "Tarifa 1 :: Domingo - Jueves ";
				$nombreprecio = "Aplica Precios Entre Semana";
				$preciodiario = $xhFila['25'];
				$preciohora = $xhFila['32'];
				$xpreciohoraadicional = $xhFila['20'];
				$xpreciohuespedadicional = $xhFila['19'];

				if($row_cnt > 0){
					$xpreciodiario = $tarifaFila['4'];
					$xpreciohora = $tarifaFila['5'];
					$xpreciohoraadicional = $tarifaFila['7'];
					$xpreciohuespedadicional = $tarifaFila['8'];
				}
			}else{
				//echo "Tarifa 2 - Viernes";
				$nombreprecio = "Aplica Precios Fin Semana";
				$preciodiario = $xhFila['26'];
				$preciohora = $xhFila['31'];
				$xpreciohoraadicional = $xhFila['20'];
				$xpreciohuespedadicional = $xhFila['19'];
			};
			break;
		case 5:
			if($hora > $horamedia){
				//echo "Tarifa 1 :: Domingo - Jueves ";
				$nombreprecio = "Aplica Precios Entre Semana";
				$preciodiario = $xhFila['26'];
				$preciohora = $xhFila['33'];
				$xpreciohoraadicional = $xhFila['20'];
				$xpreciohuespedadicional = $xhFila['19'];

				if($row_cnt > 0){
					$xpreciodiario = $tarifaFila['4'];
					$xpreciohora = $tarifaFila['5'];
					$xpreciohoraadicional = $tarifaFila['7'];
					$xpreciohuespedadicional = $tarifaFila['8'];
				}
			}else{
				//echo "Tarifa 2 - Viernes";
				$nombreprecio = "Aplica Precios Fin Semana";
				$preciodiario = $xhFila['25'];
				$preciohora = $xhFila['32'];
				$xpreciohoraadicional = $xhFila['20'];
				$xpreciohuespedadicional = $xhFila['19'];
			};
			break;
		case 6:
			if($hora > $horamedia){
				//echo "Tarifa 1 :: Domingo - Jueves ";
				$nombreprecio = "Aplica Precios Entre Semana";
				$preciodiario = $xhFila['27'];
				$preciohora = $xhFila['34'];
				$xpreciohoraadicional = $xhFila['20'];
				$xpreciohuespedadicional = $xhFila['19'];

				if($row_cnt > 0){
					$xpreciodiario = $tarifaFila['4'];
					$xpreciohora = $tarifaFila['5'];
					$xpreciohoraadicional = $tarifaFila['7'];
					$xpreciohuespedadicional = $tarifaFila['8'];
				}
			}else{
				//echo "Tarifa 2 - Viernes";
				$nombreprecio = "Aplica Precios Fin Semana";
				$preciodiario = $xhFila['26'];
				$preciohora = $xhFila['33'];
				$xpreciohoraadicional = $xhFila['20'];
				$xpreciohuespedadicional = $xhFila['19'];
			};
			break;
	}

	//Uso de Switch Case
	/*switch ($dia) {
	case 0:

		if($hora < $horamedia){
			//echo "Tarifa 2 - Viernes";
			$nombreprecio = "Aplica Precios Fin Semana";
			$xpreciohoraadicional = $xhFila['20'];
			$xpreciohuespedadicional = $xhFila['19'];
			$preciodiario = $xhFila['17'];
			$preciohora = $xhFila['18'];

            if($row_cnt > 0){
                $xpreciodiario = $tarifaFila['4'];
                $xpreciohora = $tarifaFila['5'];
                $xpreciohoraadicional = $tarifaFila['7'];
                $xpreciohuespedadicional = $tarifaFila['8'];
                $xpreciohora12=$tarifaFila['6'];
            }

		}else{
			//echo "Tarifa 1 :: Domingo - Jueves ";
			$nombreprecio = "Aplica Precios Entre Semana";
			$xpreciohoraadicional = $xhFila['20'];
			$xpreciohuespedadicional = $xhFila['19'];
			$preciodiario = $xhFila['15'];
			$preciohora = $xhFila['16'];
		}
		break;
	case 1:
	case 2:
	case 3:
	case 4:
		//echo "Tarifa 1 :: Domingo - Jueves ";
		$nombreprecio = "Aplica Precios Entre Semana";
		$xpreciohoraadicional = $xhFila['20'];
		$xpreciohuespedadicional = $xhFila['19'];
		$preciodiario = $xhFila['15'];
		$preciohora = $xhFila['16'];

        if($row_cnt > 0){
            $xpreciodiario = $tarifaFila['4'];
            $xpreciohora = $tarifaFila['5'];
            $xpreciohoraadicional = $tarifaFila['7'];
            $xpreciohuespedadicional = $tarifaFila['8'];
            $xpreciohora12=$tarifaFila['6'];
        }


		break;
	case 5:
		if($hora < $horamedia){
			//echo "Tarifa 1 :: Domingo - Jueves ";
			$nombreprecio = "Aplica Precios Entre Semana";
			$xpreciohoraadicional = $xhFila['20'];
			$xpreciohuespedadicional = $xhFila['19'];
			$preciodiario = $xhFila['15'];
			$preciohora = $xhFila['16'];

            if($row_cnt > 0){
                $xpreciodiario = $tarifaFila['4'];
                $xpreciohora = $tarifaFila['5'];
                $xpreciohoraadicional = $tarifaFila['7'];
                $xpreciohuespedadicional = $tarifaFila['8'];
                $xpreciohora12=$tarifaFila['6'];
            }

		}else{
			//echo "Tarifa 2 - Viernes";
			$nombreprecio = "Aplica Precios Fin Semana";
			$xpreciohoraadicional = $xhFila['20'];
			$xpreciohuespedadicional = $xhFila['19'];
			$preciodiario = $xhFila['17'];
			$preciohora = $xhFila['18'];
		}
		break;
	case 6:
		//echo "Tarifa 2 - Viernes";

        if($hora < $horamedia){
            //echo "Tarifa 1 ::  ";
            $nombreprecio = "Aplica Precios Fin Semana";
            $xpreciohoraadicional = $xhFila['20'];
            $xpreciohuespedadicional = $xhFila['19'];
            $preciodiario = $xhFila['17'];
            $preciohora = $xhFila['18'];
        }else{
            //echo "Tarifa 2 - sabado";
            $nombreprecio = "Aplica Precios Fin Semana";
            $xpreciohoraadicional = $xhFila['20'];
            $xpreciohuespedadicional = $xhFila['19'];
            $preciodiario = $xhFila['17'];
            $preciohora = $xhFila['18'];

            if($row_cnt > 0){
                $xpreciodiario = $tarifaFila['4'];
                $xpreciohora = $tarifaFila['5'];
                $xpreciohoraadicional = $tarifaFila['7'];
                $xpreciohuespedadicional = $tarifaFila['8'];
                $xpreciohora12=$tarifaFila['6'];
            }
        }

		break;
	}*/

/*while ($tmpFilaA = $sqldetalleA->fetch_row()) {
    if ($tmpFilaA[2] == "7"){
        $preciodiario = $tmpFilaA[8];
    }
}*/

/*LEER SERIES*/

	$serie=$mysqli->query("SELECT * FROM series WHERE estado=1 and iddocumento in(1,2)");

//Consumos *****
$sqlventa = $mysqli->query("select
	venta.idventa,
	venta.idalquiler,
	ventadetalle.idventadetalle,
	ventadetalle.idventa,
	ventadetalle.nombre,
	ventadetalle.cantidad,
	ventadetalle.precio,
	ventadetalle.importe,
	ventadetalle.procesado

	from venta left join ventadetalle on ventadetalle.idventa = venta.idventa
	where venta.idalquiler = '$xidalquiler'	order by ventadetalle.idventadetalle asc");


$sqladicional = $mysqli->query("select
	a.idpersona,
	a.idalquiler,
	a.idcliente,
	a.nombre,
	a.dni,
	a.nacimiento,
	a.id_tipo,
	b.tipoperadic_descrip,
	a.sexo

	from al_venta_personaadicional a
	left join al_tipo_peradicional b on b.id_tipo = a.id_tipo
	where idalquiler = '$xidalquiler'
	order by idpersona asc");

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Administrador</title>

<?php include "head-include.php"; ?>

<!-- Datetimerpicker-->
<link rel="stylesheet" type="text/css" href="datetimepicker/jquery.datetimepicker.css"/>
<script type="text/javascript" src="datetimepicker/jquery.datetimepicker.js"></script>

<script>
	function calcularHuespedAdicional(){
		var nroadicional = parseInt(document.form1.txtnrocupanteadicional.value);
		var costoocupanteadicional = parseFloat(document.form1.txtprecioocupanteadicional.value);
		document.form1.txtimporteocupanteadicional.value = formatCurrency(parseFloat(nroadicional*costoocupanteadicional).toFixed(2));
	}
	function calcularHoraAdicional(){
		var nrohoraadicional = parseInt(document.form2.txtnrohoraadicional.value);
		var costohoraadicional = parseFloat(document.form2.txtpreciohoraadicional.value);
		document.form2.txtimportehoraadicional.value = formatCurrency(parseFloat(nrohoraadicional*costohoraadicional).toFixed(2));
	}
	jQuery(document).ready(function($) {
		var now = new Date();
		$('#txtFechaDiaAdicional').datetimepicker({
                            mask:'39-19-9999 29:59',
                            lang:'es',
                            hours12: false,
                            format: 'd-m-Y H:i',
                            step: 1,
                            opened: false,
                            validateOnBlur: false,
                            closeOnDateSelect: false,
                            closeOnTimeSelect: false,
                            minDate: '-1970/01/03',
                            //minTime:'8:30',
                            //maxTime:'21:00',
                            dayOfWeekStart: 1,
                            maxDate:'+1970/01/07'//hasta un mes
                    });
		$('#txtFechaDiaAdicional').val(getDateNow() + " " + now.getHours()+":"+now.getMinutes());
	});

	function getDateNow() {
		var d = new Date(),
			month = '' + (d.getMonth() + 1),
			day = '' + d.getDate(),
			year = d.getFullYear();

		if (month.length < 2) 
			month = '0' + month;
		if (day.length < 2) 
			day = '0' + day;

		return [day, month, year].join('-');
	}
</script>


<script language="javascript">
    function objAjax(){
    var req = false;
    try{
    req = new XMLHttpRequest(); /* Para Firefox */
    }catch(error1){
        try{
            req = new ActiveXObject("Msxml2.XMLHTTP"); /* Algunas versiones de IE */
        }catch(error2){
            try{
                req = new ActiveXObject("Microsoft.XMLHTTP"); /* Algunas versiones de IE */
            }catch(error3){
                req = false;
            }
        }
    }
    return req;
	}

	var req = objAjax();

	function ImprimirOrden(){
        window.open("imprimir/print_alquiler-orden.php?idhabitacion=<?php echo $xidhabitacion.'&idalquiler='.$xidalquiler;?>","modelo","width=1000, height=350, scrollbars=yes" );
    }
	function PersonasAdicionales(){
        window.open("persona-adicional.php?idhabitacion=<?php echo $xidhabitacion.'&idalquiler='.$xidalquiler.'&nombrecliente='.$xaFila['7'].'&idcliente='.$xaFila['6'];?>","modelo","width=1000, height=350, scrollbars=yes" );
    }

      function abrirCliente(){
        window.open('buscar-cliente2.php?idalquiler='+ <?php echo $xidalquiler; ?>,'modelo','width=800, height=300, scrollbars=yes, location=no, directories=no,resizable=no, top=200,left=300', 'socialPopupWindow');
    }

    function abrirDatosFiscales(){

    	window.open('datos-fiscales.php?idcliente='+ <?php echo $xaFila[1]; ?>,'modelo','width=400, height=400, scrollbars=yes, location=no, directories=no,resizable=no, top=200,left=300', 'socialPopupWindow');
    }

    function cambiaTipoDoc(tipo){
    	$.ajax({
            url:'include/huesped/cambia_tipo_doc.php',
            type:'post',
            data:{
            	'idcliente':<?php echo $xaFila[1]; ?>,
            	'tipo_documento':tipo
            },
            success:function(data){
            	console.log(data);
            },
            error:function(data){
            	console.log(data);
            }
        });
    }

	<?php
		$xtipoalq = $xaFila['4'];
		if($xtipoalq==1){
			$xabrirventa = "ImprimirOrdenHora";
		}elseif($xtipoalq==2){
			$xabrirventa = "ImprimirOrdenDia";
		}
	?>

function PendientedePago(){
		var nroadicional = parseFloat(document.frmdeuda.txtpendientepago.value);
		if(nroadicional > 0){
			alert("No puede finalizar mientras hay pendiente de pago.");
            loading = document.getElementById('div_loading');
            if (loading){
                padre = loading.parentNode;
                padre.removeChild(loading);
            }
			return false;
		}else{
			var href = $("#btnFinalizarHab").attr('href');
			var fecsh = $("#txtFechaDiaAdicional").val();
			$("#btnFinalizarHab").attr("href", href + '&fechasalidahuesped=' + fecsh );
		}
		return true;
	}
</script>


<script type="text/javascript" language="javascript">
	$(document).ready(function(){
		$("#mostrar").click(function(){
			$('.div1').show();
			$('.div2').show();
		});
		$("#ocultar").click(function(){
			$('.div1').hide();
			$('.div2').hide();
		});
	});
</script>

</head>
<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0">

    <tr>
      <td height="25" colspan="3"><?php include ("head.php"); ?></td>
    </tr>
    <tr>
      <td width="185" height="25" align="left" valign="top"><?php include ("menu_nav.php"); ?></td>
      <td width="25">&nbsp;</td>
      <td width="793" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">

          <tr>
            <td width="460" height="30"> <h3 style="color:#E1583E;"> <i class="fa fa-users"></i> Habitación Alquilada (Detalle)</h3></td>
            <td width="203"><span class="textoContenido"><strong>ORDEN #: <?php echo $xaFila['9'];?><span class="textoContenido" style="font-size:28px;color:#E1583E;">
              <input name="txtidalquiler" type="hidden" id="txtidalquiler" value="<?php echo $xidalquiler;?>">
            </span></strong></span></td>
            <td width="242" align="center">  <button type="button" onclick="window.location.href='control-habitaciones.php';" class="btngris" style="border:0px; cursor:pointer;"> <i class="fa fa-arrow-left"></i> Volver </button> </td>
          </tr>
          <tr>
            <td height="30" colspan="3"><table width="100%" border="0" cellpadding="1" cellspacing="1">

                <tr>
                  <td width="911" height="30"><div class="lineahorizontal" style="background:#BFBFBF;">

                  <?php if (isset($_SESSION['msgerror'])){ ?>
                  <div class="alert alert-success alert-dismissable textoContenidoMenor">
                  	<?php echo $_SESSION['msgerror'];$_SESSION['msgerror']="";?>
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                  </div>
                  <?php } ?>

                  </div></td>
                </tr>
                <tr>
                  <td height="30">
                    <form name="form_adicional" id="form_adicional" action="include/alquiler/prg_cobrar-adicionales.php?formapago=efectivo&idalquiler=<?php echo $xidalquiler.'&idhabitacion='.$xidhabitacion.'&idalquilerdetalle='.$tmpFila['0'];?>" method="post">
                    <table width="95%" border="0" cellpadding="1" cellspacing="1">
                        <tr>
                          <td width="400" height="25" align="left" valign="middle">
                              <p>
                              <h3>
                              	<B>CLIENTE:</B> <?php echo $xaFila['7'];?>
                              	<?php if($xaFila['14'] !== ""){ ?>
                              	<input type="radio" name="sel_doc" value="1"
                              		<?php if($xaFila['16'] == "1"){ ?>
                              		checked
                              		<?php } ?>
                              	onclick="cambiaTipoDoc(this.value);" />
                              	<?php } ?>
                              </h3>
                          	    <button type="button" onclick="abrirCliente(); return false" class="btnmodificar tooltip" tooltip="Cambiar Huésped" style="border:0px; cursor:pointer;"> <i class="fa fa-search-plus"></i></button>
                          	    <button type="button" onclick="window.location.href='huespedes-editor.php?xdesdealquiler=1&<?php echo 'idhabitacion='.$xidhabitacion.'&nrohabitacion='.$xnrohabitacion.'&xestado='.$xestadohabitacion.'&idtipohab='.$idtipohab;?>';" class="btnmodificar tooltip" tooltip="Agregar Huésped" style="border:0px; cursor:pointer;"> <i class="fa fa-plus-square"></i></button>
                          	    <button type="button" onclick="abrirDatosFiscales(); return false" class="btnmodificar tooltip" tooltip="Agrega o edita los datos fiscales del cliente" style="border:0px; cursor:pointer;">
                          	    	Datos fiscales
                          	    </button>
                          	 </p>
                          	 <p>
                          	 	<h3>
                          	 		<B>RAZÓN SOCIAL:</B>
                          	 		<?php echo ($xaFila['14'] == "" ? "<i>Sin definir</i>" : $xaFila['14']);?>
                          	 		<?php if($xaFila['14'] !== ""){ ?>
                          	 		<input type="radio" name="sel_doc" value="6"
	                              		<?php if($xaFila['16'] == "6"){ ?>
	                              		checked
	                              		<?php } ?>
	                              	onclick="cambiaTipoDoc(this.value);" />
                          	 		<?php } ?>
                          	 	</h3>
                          	 </p>
                                <p>
                                    <?php
                                        $totalHuesp = $sqladicional->num_rows;
                                        if($totalHuesp>0){
                                    ?>
                              <table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#F0F0F0">
                                  <tr class="textoContenidoMenor">
                                      <td width="5%" height="25" bgcolor="#F4F4F4" ><div align="center"><strong>#</strong></div></td>
                                      <td width="5%" height="25" bgcolor="#F4F4F4" ><div align="center"><strong>Tipo</strong></div></td>
                                      <td width="38%" height="25" align="left" valign="middle" bgcolor="#F4F4F4" ><div align="left">Nombres y Apellidos</div></td>
                                      <td width="20%" align="left" valign="middle" bgcolor="#F4F4F4" ><div align="left">DNI </div></td>
                                      <td width="11%" align="left" valign="middle" bgcolor="#F4F4F4" ><div align="left">SEXO </div></td>
                                      <td width="15%" height="25" align="left" valign="middle" bgcolor="#F4F4F4" ><div align="center">Fecha de Nacimiento</div></td>
                                  </tr>
                                <?php
                                    while($aFila = $sqladicional->fetch_row())
                                    {
                                        $suma++;
                                    ?>
                                        <tr class="textoContenidoMenor">
                                          <td height="25" bgcolor="#FFFFFF" class="textoContenidoNegro"><div align="center"><? echo $suma; ?></div></td>
                                           <td height="25" bgcolor="#FFFFFF" class="textoContenidoNegro"><?php echo $aFila['7'];?></td>
                                          <td height="25" bgcolor="#FFFFFF" class="textoContenidoNegro"><?php echo $aFila['3'];?></td>
                                          <td bgcolor="#FFFFFF" class="textoContenidoNegro"><?php echo $aFila['4'];?></td>
                                          <td bgcolor="#FFFFFF" class="textoContenidoNegro"><?php echo $aFila['8'];?></td>
                                          <td height="25" bgcolor="#FFFFFF" class="textoContenidoNegro" align="center"><?php echo $aFila['5'];?></td>
                                        </tr>
                                        <?php  } ?>
                                  <?php  } ?>
                              </table>
                            </p>
                            <a href="#" onClick="PersonasAdicionales();" class="btnnegro"> Personas Adicionales </a>
                          </td>
                          <td width="266" height="25" align="right" valign="middle"><span class="textoContenido">
                              <strong>Hab. <?php echo $xtipohabitacion;?> #: </strong></span>
                              <span class="textoContenido" style="font-size:28px;color:#E1583E;">
                                  <?php echo $xaFila['3'];?>
                              <input name="txtidhabitacion" type="hidden" id="txtidhabitacion" value="<?php echo $xidhabitacion;?>">
                              <span class="textoContenido" style="font-size:28px;color:#00A230;">
                                  <input name="txtnrohabitacion" type="hidden" id="txtnrohabitacion" value="<?php echo $xnrohabitacion;?>">
                              </span>
                              </span>
                          </td>
                          <td width="160" height="25" align="left" valign="middle" class="textoContenido">&nbsp;</td>
                        </tr>
                        <tr>
                          <td height="25" colspan="3" valign="top"><div class="lineahorizontal" style="background:#BFBFBF;"></div></td>
                        </tr>
                        <tr>
                          <td height="30" colspan="3" valign="top"><table width="100%" border="0" cellspacing="1" cellpadding="1">
                            <tr class="textoContenido" style="font-weight: bold">
                              <td width="6%" height="25" align="center">#</td>
                              <td width="46%" height="25">Concepto</td>
                              <td width="11%" height="25">Precio  (S/ )</td>
                              <td width="10%">Total (S/ )</td>
                              <td width="10%">&nbsp;</td>
                              <td width="8%">&nbsp;</td>
                              <td width="4%" height="25">&nbsp;</td>
                            </tr>
                            <tr>
                              <td height="25" colspan="7"><div class="lineahorizontal" style="background:#00A230;"></div></td>
                            </tr>
                            <?php
                            	$ultimotipoalquiler = 0;
                            	$estadoDet = 0;
								$num=0;
								$xprecioalquiler=0;
								$precioalquilerpendiente=0;
                             while ($tmpFila = $sqldetalle->fetch_row()){
                             	$ultimotipoalquiler = $tmpFila[2];
                             	$estadoDet = $tmpFila[12];


                             if($tmpFila[23]==0): $cuentaAlquiler+=1; endif; $num++; ?>
                            <tr class="textoContenidoMenor">
                              <td height="25" align="center" class="textoContenido"><?php echo $num;?></td>
                              <td height="25"><span class="textoContenido">
                                <?php

								if($tmpFila['12']==1){ //Estado Pago
									$xprecioalquiler = ($xprecioalquiler + $tmpFila['20']) /*- $xaFila[11]*/;
								}elseif($tmpFila['12']==0){
									$precioalquilerpendiente = ($precioalquilerpendiente + $tmpFila['20']) /*-  $xaFila[11]*/;
								}
								echo tipoAlquiler($tmpFila['2']).' ('.$tmpFila['19'].')';
								if($tmpFila['2'] != 4 &&  $tmpFila['2'] != 5){
									echo fechadesdehasta($tmpFila['3'],$tmpFila['4']);
								}
								?></span></td>
                              <td height="25" align="center" class="textoContenido"><?php echo number_format($tmpFila['18'],2);?></td>
                              <td height="25" align="center" class="textoContenido"><?php echo number_format($tmpFila['20'],2);?></td>
                              <td height="25" align="center" class="textoContenido"><?php echo estadoPago($tmpFila['12'],2);?></td>
                              <td height="25" align="center" class="textoContenido">

                              <?php if($tmpFila['12']==0){ ?>
                              <!--Boto Efectivo-->
                              <a href="include/alquiler/prg_cobrar-adicionales.php?formapago=efectivo&idalquiler=<?php echo $xidalquiler.'&idhabitacion='.$xidhabitacion.'&idalquilerdetalle='.$tmpFila['0'].'&monto='.$tmpFila['20'];?>" onClick="return confirm('¿Confirma pagar con Efectivo?');" id="botonefectivo" class="btnnegro" style="border:0px; padding-left:10px; padding-right:10px; cursor:pointer;"> <i class="fa fa-money fa-lg"></i> </a>
                              <?php } ?>

                              <?php if($tmpFila['12']==0){ ?>
                              <!--Boton Visa-->
                              <a href="include/alquiler/prg_cobrar-adicionales.php?formapago=visa&idalquiler=<?php echo $xidalquiler.'&idhabitacion='.$xidhabitacion.'&idalquilerdetalle='.$tmpFila['0'].'&monto='.$tmpFila['20'];?>" onClick="return confirm('¿Confirma pagar con Visa?');" id="botonevisa" class="btnnegro" style="border:0px; padding-left:10px; padding-right:10px; cursor:pointer;"> <i class="fa fa-cc-visa fa-lg"></i> </a>
                              <?php } ?>

                              <?php if($tmpFila['12']==0){ ?>
                              <!--Boton Master Card-->
                              <!-- <a href="include/alquiler/prg_cobrar-adicionales.php?formapago=mastercard&idalquiler=<?php echo $xidalquiler.'&idhabitacion='.$xidhabitacion.'&idalquilerdetalle='.$tmpFila['0'].'&monto='.$tmpFila['20'];?>" onClick="return confirm('¿Confirma pagar con Master Card?');" id="botonemastercard" class="btnnegro" style="border:0px; padding-left:10px; padding-right:10px; cursor:pointer; display: none"> <i class="fa fa-cc-mastercard fa-lg"></i> </a> -->
                              <?php } ?>

                              </td>
                              <?php if($tmpFila['12']==0){?>
                              <td>
                              	<!--Campo Efectivo-->
                              	 <input name="txtmontoefectivo<?=$num?>" type="text" placeholder="Efectivo" title="Efectivo" class="textbox" id="txtmontoefectivo<?=$num?>" style="text-align:right; width:50px; display:none;" value="<?php echo $xpreciohora;?>" onBlur="document.getElementById('txtmontoefectivo<?=$num?>').value = formatCurrency(txtmontoefectivo<?=$num?>.value);" onFocus = "txtmontoefectivo<?=$num?>.value = EliminarComa(this.value)">

                              </td>

                               <?php } ?>

                              <?php if($tmpFila['12']==0){?>
                              <td>
                              	<!--Campo Visa-->
                              	 <input  name="txtmontovisa<?=$num?>" type="text" title="Visa" class="textbox" id="txtmontovisa<?=$num?>" style="text-align:right; width:50px; display:none;" value="<?php echo $xpreciohora;?>" placeholder="Visa"   onBlur="document.getElementById('txtmontovisa<?=$num?>').value = formatCurrency(txtmontovisa<?=$num?>.value);" onFocus = "txtmontovisa<?=$num?>.value = EliminarComa(this.value)">

                              </td>
                              <td style="display: none">
                              	<!--Campo Master Card-->
                              	 <input  name="txtmontomastercard<?=$num?>" type="text" title="Master Card" class="textbox" id="txtmontomastercard<?=$num?>" style="text-align:right; width:50px; display:none;" value="<?php echo $xpreciohora;?>" placeholder="Master Card"   onBlur="document.getElementById('txtmontomastercard<?=$num?>').value = formatCurrency(txtmontomastercard<?=$num?>.value);" onFocus = "txtmontomastercard<?=$num?>.value = EliminarComa(this.value)">

                              </td>
                              <td>
                              	<!--Boton guardar-->
                              	 <!--<button ti class="btnrojo" id="GuardaAdicional<?=$num?>" style="display:none;" name="GuardaAdicional<?=$num?>"><i class="fa fa-save fa-lg"></i></button>-->

                              	 <a href="#" onClick="prueba('include/alquiler/prg_cobrar-adicionales2.php?formapago1=visa&formapago2=efectivo&formapago3=mastercard&idalquiler=<?php echo $xidalquiler.'&idhabitacion='.$xidhabitacion.'&idalquilerdetalle='.$tmpFila['0'].'&monto1='?>','txtmontovisa<?=$num?>','txtmontoefectivo<?=$num?>', '<?php echo number_format($tmpFila['20'],2);?>');" id="GuardaAdicional<?=$num?>" class="btnnegro" style="border:0px; padding-left:10px; padding-right:10px; cursor:pointer;display:none;"  title="Guardar Pago"> <i class="fa fa-save fa-lg"></i> </a>
                              </td>
                               <?php } ?>
                               <?php if($tmpFila['12']==0){?>
                              <td>


                              	 <label for="radio4">
                                    <input type="radio" name="txtformadepago" id="radio4" value="3" onClick="mostrando(<?=$num?>);">
                                    <strong>Combinar pago</strong> </label>

                              </td>
                              <?php } ?>
                              <td height="25" align="center">

							  	<?php $xidtmp = $tmpFila['0'];?>

                              	<?php if($tmpFila['12']==0){?>
								<a href="include/alquiler/prg_alquiler-anular-item.php?iddetalle=<?php echo $xidtmp.'&idalquiler='.$xidalquiler.'&idhabitacion='.$xidhabitacion; ?>" onClick="return confirm('¿Confirma la Anulación?');" class="btnquitar" style="border:0px; cursor:pointer;background:#515151;"> <i class="fa fa-close"></i> </a>
                            	<?php } ?>

                              </td>

                            </tr>
                            <?php } ?>

                          </table>
                          </form>



					<?php if($configs->getConfig("OPCION_COMPLETAR_DIA") == 1) { ?>
						<?php if($ultimotipoalquiler == 1 && $estadoDet == 1){ ?>
							<form  name="frmcompletardia" method="post">
								<input name="txtprecioporhora" type="hidden" value="<?php echo $preciohora;?>">
								<input name="txtpreciopordia" type="hidden" value="<?php echo $preciodiario;?>">
								<input type="hidden" name="txtnrodias" value="1" />
								<input type="hidden" name="hiddenCompletar" value="1" />
							<button type="button" onClick="document.frmcompletardia.action='include/alquiler/prg_alquiler-agregar.php?idhabitacion=<?php echo $xidhabitacion.'&idalquiler='.$xidalquiler.'&idtipo=2&idturno='.$xidturno,'&idusuario='.$xidusuario;?>'; document.frmcompletardia.submit(); " class="btnmodificar tooltip" tooltip="Da click para completar el día y cobrar la diferencia" style="border:0px; cursor:pointer;">
                          	    	Completar dia
                          	    </button>
                          	</form>
						<?php } ?>

					<?php } ?>

                      </td>
                        </tr>
                        <tr>
                          <td height="52" colspan="3" valign="bottom">
                          <a href="#" id="mostrar" class="btnmodificar"> <i class="fa fa-chevron-down"></i> </a>
                          <a href="#" id="ocultar" class="btnmodificar"> <i class="fa fa-chevron-up"></i> </a>
                          <span class="textoContenido" style="font-weight: bold"> Renovar / Adicionar </span></td>
                      </tr>
                        <tr>
                          <td height="30" valign="top">

                          <div class="div1" style="display:display;">
                            <form id="frmrenovar" name="frmrenovar" method="post">
                              <table width="98%" border="0" cellspacing="1" cellpadding="1">
                                <tr>
                                  <td width="456" height="30" class="textoContenido"><table width="100%" border="0" cellspacing="1" cellpadding="1">
                                    <tr>
                                      <td width="134" height="30" class="textoContenido">Renovar Por <?php echo $precioHorasAlquilerTarifa1;?> Horas</td>
                                      <td width="71" height="30" class="textoContenido">S/ <?php echo $preciohora;?>
                                        <input name="txtprecioporhora" type="hidden" id="txtprecioporhora" value="<?php echo $preciohora;?>"></td>
                                      <td width="244" class="textoContenido"><select name="txtnrocupanteadicional2" disabled class="textbox" style="width:30%;" onChange="return calcularHuespedAdicional();">
                                        <option selected> </option>
                                        <?php
                                      for($i=1;$i<=$nroadicional;$i++){
										  echo "<option value=".$i.">".$i."</option>";
									  }
									  ?>
                                      </select>
                                        <button type="button" class="btnnegro" style="border:0px; cursor:pointer;" onClick="document.frmrenovar.action='include/alquiler/prg_alquiler-agregar.php?idhabitacion=<?php echo $xidhabitacion.'&idalquiler='.$xidalquiler.'&idtipo=1&idturno='.$xidturno,'&idusuario='.$xidusuario;?>'; document.frmrenovar.submit(); "> <i class="fa fa-save"></i>  </button>
                                        </td>
                                    </tr>
                                  </table></td>
                                </tr>
                                <tr>
                                  <td height="30" class="textoContenido"><table width="100%" border="0" cellspacing="1" cellpadding="1">
                                    <tr>
                                      <td width="134" height="30" class="textoContenido">Renovar Por Día</td>
                                      <td width="73" class="textoContenido"><!--<S/--> <?php //echo $preciodiario;?>
                                        <input name="txtpreciopordia" type="text" class="textbox" id="txtpreciopordia" value="<?php echo $preciodiario;?> "  ></td>
                                      <td width="240" class="textoContenido"><select name="txtnrodias" id="txtnrodias" class="textbox" style="width:30%;" onChange="return calcularHoraAdicional();">
                                        <option value="0">#Días</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="4">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                      </select>
                                        <button type="button" class="btnnegro" style="border:0px; cursor:pointer;" onClick="document.frmrenovar.action='include/alquiler/prg_alquiler-agregar.php?idhabitacion=<?php echo $xidhabitacion.'&idalquiler='.$xidalquiler.'&idtipo=2&idturno='.$xidturno,'&idusuario='.$xidusuario;?>'; document.frmrenovar.submit(); "> <i class="fa fa-save"></i>  </button>

                                      </td>
                                    </tr>
                                  </table></td>
                                </tr>

                                <tr>
                                  <td height="30" class="textoContenido"><div class="lineahorizontal" style="background:#00A230;"></div></td>
                                </tr>
                              </table>
                            </form>
                          </div>



                          </td>
                          <td height="30" colspan="2" valign="top">

                          <div class="div2"  style="display:display;">
                            <form id="frmagregar" name="frmagregar" method="post">
                              <table width="98%" border="0" cellspacing="1" cellpadding="1">
                                <tr>
                                  <td width="456" height="30" class="textoContenido"><table width="100%" border="0" cellspacing="1" cellpadding="1">
                                    <tr>
                                      <td width="134" height="30" class="textoContenido">Huésped Adicional</td>
                                      <td width="71" height="30" class="textoContenido">S/ <?php echo $xpreciohuespedadicional;?>
                                        <input name="txtprecioocupanteadicional" type="hidden" id="txtprecioocupanteadicional" value="<?php echo $xpreciohuespedadicional;?>"></td>
                                      <td width="244" class="textoContenido"><select name="txtnrocupanteadicional" class="textbox" style="width:30%;" onChange="return calcularHuespedAdicional();">
                                        <option value="0">#Adicional</option>
                                        <?php
                                      for($i=1;$i<=$nroadicional;$i++){
										  echo "<option value=".$i.">".$i."</option>";
									  }
									  ?>
                                      </select> <button type="button" class="btnnegro" style="border:0px; cursor:pointer;" onClick="document.frmagregar.action='include/alquiler/prg_alquiler-agregar.php?idhabitacion=<?php echo $xidhabitacion.'&idalquiler='.$xidalquiler.'&idtipo=4&idturno='.$xidturno,'&idusuario='.$xidusuario;?>'; document.frmagregar.submit(); "> <i class="fa fa-save"></i></button></td>
                                    </tr>
                                  </table></td>
                                </tr>
                                <tr>
                                  <td height="30" class="textoContenido"><table width="100%" border="0" cellspacing="1" cellpadding="1">
                                    <tr>
                                      <td width="134" height="30" class="textoContenido">Hora Adicional </td>
                                      <td width="73" class="textoContenido">S/ <?php echo $xpreciohoraadicional;?>
                                        <input name="txtpreciohoraadicional" type="hidden" id="txtpreciohoraadicional" value="<?php echo $xpreciohoraadicional;?>"></td>
                                      <td width="240" class="textoContenido"><select name="txtnrohoraadicional" id="txtnrohoraadicional" class="textbox" style="width:30%;" onChange="return calcularHoraAdicional();">
                                        <option value="0">#Horas</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                      </select> <button type="button" class="btnnegro" style="border:0px; cursor:pointer;" onClick="document.frmagregar.action='include/alquiler/prg_alquiler-agregar.php?idhabitacion=<?php echo $xidhabitacion.'&idalquiler='.$xidalquiler.'&idtipo=3&idturno='.$xidturno,'&idusuario='.$xidusuario;?>'; document.frmagregar.submit(); "> <i class="fa fa-save"></i></button></td>
                                    </tr>
                                  </table></td>
                                </tr>




                                <tr>
                                  <td height="30" class="textoContenido"><div class="lineahorizontal" style="background:#FFAF03;"> </div></td>
                                </tr>
                              </table>
                            </form>
                          </div>

                          </td>
                        </tr>
                        <tr>
                          <td height="30"><table width="100%" border="0" cellpadding="1" cellspacing="1">
                            <tr>
                              <td width="201" height="21" align="left" bgcolor="#FFFFFF" class="textoContenido"> <strong>Consumo</strong></td>
                              <td width="107" align="right" bgcolor="#FFFFFF"><a href="venta.php?idhabitacion=<?php echo $xidhabitacion.'&idalquiler='.$xidalquiler;?>"
                               <?php /*echo   $cuentaProductos.'-'.$cuentaAlquiler;*/ if ($xaFila[10]=='0' ){ echo 'style="display:  "'; }?>class="btnrojo"
                              	> <i class="fa fa-plus-square"></i></a> &nbsp;</td>
                            </tr>
                          </table>
                            <table width="100%" border="0" cellpadding="4" cellspacing="1" bgcolor="#E0E0E0">
                            <tr class="textoContenidoMenor">
                              <td width="225" height="25" bgcolor="#F4F4F4">Producto</td>
                              <td width="79" height="25" align="right" bgcolor="#F4F4F4">Precio  (S/)</td>
                              <td width="78" height="25" align="right" bgcolor="#F4F4F4">Importe (S/)</td>
                              </tr>
                            <?php $xprodtotal = 0; $num = 0; while($vFila = $sqlventa->fetch_row()){?>
                            <tr class="textoContenidoMenor">
                              <td height="25" bgcolor="#FFFFFF">(<?php echo $vFila['5']; ?>)  <?php echo $vFila['4']; ?></td>
                              <td height="25" align="right" bgcolor="#FFFFFF"><?php echo $vFila['6']; ?></td>
                              <td height="25" align="right" bgcolor="#FFFFFF"><?php echo $vFila['7']; ?></td>
                              </tr>
                            <?php
							$_SESSION['xcliente']="";
							$_SESSION['xidcliente']="";
							if($vFila[8]==0):  $cuentaProductos+=1; endif;
							$xprodtotal = $xprodtotal + $vFila['7'];
							$num++;
							}
						?>
                          </table></td>
                          <td height="30" colspan="2"><table width="100%" border="0" align="right" cellpadding="1" cellspacing="1">
                            <tbody>
                              <tr>
                                <td width="395" height="25"><form id="frmcomentario" name="frmcomentario" method="post" action="include/alquiler/prg_anotaciones.php?idhabitacion=<?php echo $xidhabitacion.'&idalquiler='.$xidalquiler;?>">
                                  <table width="396" border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tbody>
                                      <tr>
                                        <td width="352" height="25"><textarea name="txtanotaciones" class="textbox" id="txtanotaciones" placeholder="Guardar Anotaciones" style="height:60px;"><?php echo $xaFila['8'];?></textarea></td>
                                        <td width="44" height="25"><button type="submit" class="btnnegro" style="border:0px; cursor:pointer;"> <i class="fa fa-save"></i></button></td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </form></td>
                              </tr>
                            </tbody>
                          </table></td>
                        </tr>
                        <tr>
                          <td height="30" colspan="3"><div class="lineahorizontal" style="background:#FFAF03;"> </div></td>
                        </tr>
                        <tr>
                          <td height="30" colspan="3"><table width="100%" border="0" cellspacing="1" cellpadding="1">
                            <tbody>
                              <tr class="textoContenido">
                                <td width="21%" height="24"><strong>Resumen del Cliente</strong></td>
                                <td width="17%" height="24">Habitación: S/ <strong><?php echo number_format($xprecioalquiler - $xaFila[11],2);?></strong></td>
                                <td width="18%" height="24">Consumo: S/ <?php echo number_format($xprodtotal,2);?></td>
                                <td width="21%" height="24"><strong>Descuento: S/ <?php echo number_format(($xaFila[11]),2) ?></strong></td>
                                <td width="21%" height="24"><strong>Importe Total: S/ <?php echo number_format(($xprecioalquiler + $xprodtotal) - $xaFila[11],2) ?></strong></td>
                                <td width="23%" height="24">
                                <form name="frmdeuda" id="frmdeuda">
                                <span style="color:#E1583E; font-weight:600;"> Pendiente de Pago: S/ <?php echo number_format($precioalquilerpendiente,2); ?> </span> <input name="txtpendientepago" type="hidden" id="txtpendientepago" value="<?php echo $precioalquilerpendiente;?>">
                                </form>
                                </td>
                              </tr>
                            </tbody>
                          </table></td>
                        </tr>
                        <tr>
                          <td height="30" colspan="3"><div class="lineahorizontal" style="background:#FFAF03;"> </div></td>
                        </tr>
						<tr>
							<td height="30" colspan="3">
								<table width="100%" border="0" cellspacing="1" cellpadding="1">
								<tbody>
								<tr class="textoContenido">
									<td width="30%" height="24"></td>
									<td width="30%" height="24"></td>
									<td width="10%" height="24"></td>
									<td width="30%" height="24">
										<strong>Hora de Salida Huesped:</strong>
										<input name="txtFechaSalidaHuesped" type="text" id="txtFechaDiaAdicional" class="textbox" style="width:45%;" 
											placeholder="Seleccione Fecha/Hora">
									</td>
								</tr>
								</tbody>
							</table>
						  </td>
						</tr>
						<tr>
                          <td height="30" colspan="3"><div class="lineahorizontal" style="background:#FFAF03;"> </div></td>
                        </tr>
                        <tr>
                          <td height="30" colspan="3"><table width="100%" border="0" cellspacing="0" cellpadding="1">
                            <tbody>
                              <tr>
                                <td width="276" height="30" align="left" valign="middle">

                                <a href="#" onClick="ImprimirOrden(); return false" class="btnrojo"> <i class="fa fa-print"></i> Imprimir </a>
                                <a href="control-habitaciones.php" class="btnnegro"> <i class="fa fa-close"></i> Salir </a>

                                </td>
                                <td><strong <?php if ($xaFila[10]=='0' && $cuentaAlquiler==0 && $cuentaProductos==0 ){ echo 'style="display: none"'; }?>>Tipo de Documento:</strong></td>
                                <td>

                                	<select name="serie" id="series" class="select" <?php if ($xaFila[10]=='0' && $cuentaAlquiler==0 && $cuentaProductos==0 ){ echo 'style="display: none; "'; }?> >
                                		<option value=""></option>
                                		<?php while ($tmpSeries = $serie->fetch_row()){ $num++; ?>

                                			<option value="<?php echo $tmpSeries[1];?>"><strong><?php echo $tmpSeries[2];?></strong></option>

                                		<?php }?>
                                	</select>
                                </td>
                                <td>

                                	<button id="ProcesaEnvio" <?php if ($xaFila[10]=='0' && $cuentaAlquiler==0 && $cuentaProductos==0 ){ echo 'style="display: none;"'; }?> class="btnrojo"><i class="fa fa-arrow-up" id="liAnula"></i> Enviar Sunat</button>
                                </td>
                                <td width="620" height="30" align="right" valign="middle">

                                <a href="alquilar-cambiarhabitacion.php?idalquiler=<?php echo $xidalquiler.'&idhabitacion='.$xidhabitacion.'&idtipohabitacion='.$xidtipohabitacion; ?>" class="btnnegro"> <i class="fa fa-refresh"></i> Cambiar de Habitación </a>
<?php
//if($_SESSION['xyztipo']=='4') {
if($configs->getConfigRol("ANULAR_HABITACION", $_SESSION['xyztipo']) == 1){
?>
                                <a  href="alquilar-anulacion.php?idalquiler=<?php echo $xaFila['0'].'&idhabitacion='.$xaFila['2']; ?>" class='confirm btnnegro' onClick="return confirm('&iquest;Confirma la Anulación de la Orden de la Habitación?');"> <i class="fa fa-close"></i> Anular </a>
<?php } ?>

                                <a  href="include/alquiler/prg_alquiler-finalizar.php?idalquiler=<?php echo $xaFila['0'].'&idhabitacion='.$xaFila['2']; ?>" id="btnFinalizarHab" class='confirm btnrojo' onClick="return PendientedePago(); return confirm('&iquest;Confirma finalizar el Alquiler de la  Habitación?');"> <i class="fa fa-close"></i> Finalizar Habitación </a>

                                </td>
                              </tr>
                            </tbody>
                          </table></td>
                        </tr>

                    </table>
                  </td>
                </tr>

            </table></td>
            </tr>

      </table></td>
    </tr>
    <tr>
      <td height="25" colspan="3"></td>
    </tr>

</table>
<p>&nbsp;</p>

<!-- Load MODAL MSG JS
<script type='text/javascript' src='modalmsg/jquery.js'></script>
<script type='text/javascript' src='modalmsg/jquery.simplemodal.js'></script>
<script type='text/javascript' src='modalmsg/confirm.js'></script> -->
</body>
</html>
<?php include ("footer.php") ?>

<script type="text/javascript">

	$(function(){



		$("#ProcesaEnvio").on('click',function(e){
			e.preventDefault();
			if($("#series").val()==""){
				swal("Advertencia!",'Seleccione Tipo de Documento a Enviar..!', "warning");
				$("#series").focus();
				return;
			}
			swal({
			  title: "Enviar Documento a SUNAT?",
			  text: "Una vez enviado no se puede revertir el proceso..!",
			  icon: "warning",
			  buttons: true,
			  dangerMode: true,
			  closeOnClickOutside: false,
			  closeOnEsc: false
			})
			.then((willDelete) => {

			  if (willDelete) {

			      $url = "";

                  if($("#series").val() == "01"){
                      $url = "FE/EnviarDocumento_2_1.php";
                  }else{
                      $url = "FE/Generaxml.php";
                  }

			  	//Enviar proceso por ajax
			  	$.ajax({
		            url: $url,
		            type:'post',
		            data:{'idalquiler':<?php echo $xidalquiler; ?>,'tipo_documento':$("#series").val(),'tipo_servicio':'AL'},
		            //dataType:'json',
		            beforeSend:function(){
		               $('#ProcesaEnvio').attr('disabled','disabled');
		               $("#liAnula").removeClass();
		               $("#liAnula").addClass('fa fa-spinner fa-spin');
		              	swal('Enviando...!',{
						  	closeOnClickOutside: false,
			  				closeOnEsc: false,
			  				 buttons: false
						});

		            },
		            success:function(data){

		               data = eval("("+data+")");
		               console.log(data);
		                if(typeof data.success != "undefined"){
		                    if(data.errors==0){
                                window.open('FE/PDF/'+data.success['nombre_archivo']);

                                if($("#series").val() == "01"){

                                    $.ajax({
                                        url:'include/backupNube/registro_data_nube_por_doc.php',
                                        type:'post',
                                        data:{'idalquiler':<?php echo $xidalquiler; ?>,'tipo_documento':$("#series").val(),'tipoVenta':'01'},
                                        success:function(data){
                                            console.log(data);
                                        },
                                        error:function(data){
                                            console.log(data);
                                        }

                                    });

                                }
		                       swal({
								  title: "Enviado...!",
								  text: data.success['Description'],
								  icon: "success",
								  buttons: true,
								  dangerMode: true

								})
								.then((willDelete) => {
								  if (willDelete) {
									    swal("Transacción Finalizada!", {
									      icon: "success",
									    });

									    window.location.href='control-habitaciones.php';
								  }else{
                                      swal("Envío Cancelado!");
                                      loading = document.getElementById('div_loading');
                                      if (loading){
                                          padre = loading.parentNode;
                                          padre.removeChild(loading);
                                      }
								  }
                                });

		                        $("#liAnula").removeClass();
		                        $("#liAnula").addClass('fa fa-arrow-up');
		                        $('#ProcesaEnvio').removeAttr('disabled');


		                    }else{
		                        if(typeof data.errors != "undefined"){
		                            if(data.success==0){
		                                swal("Error!",data.errors['getCode']+' '+data.errors['getMessage'], "error");
		                                $("#liAnula").removeClass();
		                                $("#liAnula").addClass('fa fa-file-code-o');
		                                $('#ProcesaEnvio').removeAttr('disabled');
		                                window.open('FE/PDF/'+data.nombre_archivo);

                                        loading = document.getElementById('div_loading');
                                        if (loading){
                                            padre = loading.parentNode;
                                            padre.removeChild(loading);
                                        }
		                                //window.location.href='control-habitaciones.php';

		                            }else{
		                                 if(data.success==2){

		                                    swal("Error!",data.errors, "error");
		                                    $("#liAnula").removeClass();
		                                    $("#liAnula").addClass('fa fa-arrow-up');
		                                    $('#ProcesaEnvio').removeAttr('disabled');
		                                    window.open('FE/PDF/'+data.nombre_archivo);

                                             loading = document.getElementById('div_loading');
                                             if (loading){
                                                 padre = loading.parentNode;
                                                 padre.removeChild(loading);
                                             }
		                                    //window.location.href='control-habitaciones.php';
		                                }
		                            }

		                        }
		                    }
		                }


		            },
		            error:function(rpta){
		             swal("Error!","Ocurió un Error al Realizar Petición..!", "error");
		             $("#liAnula").removeClass();
		             $("#liAnula").addClass('fa fa-arrow-up');
		            $('#ProcesaEnvio').removeAttr('disabled');
		             console.log(rpta);


		            }

		        });
			    /*swal("algo", {
			      icon: "success",
			    });*/
			  } else {
			    swal("El proceso de envío fue cancelado..!!");
                  loading = document.getElementById('div_loading');
                  if (loading){
                      padre = loading.parentNode;
                      padre.removeChild(loading);
                  }
			  }
			});
		})

		//Form Adicional
		$("#GuardaAdicional").on('click',function(e){
			e.preventDefault();
			swal({
			  title: "Guardar Datos Adicionales?",
			  text: "Una vez enviado no se puede revertir el proceso..!",
			  icon: "warning",
			  buttons: true,
			  dangerMode: true,
			  closeOnClickOutside: false,
			  closeOnEsc: false
			})
			.then((willDelete) => {

			  if (willDelete) {
			  	var x = document.getElementsByName('form_adicional');
				x[0].submit();
			  }
			})

		})

	})


function mostrando(id){
		document.getElementById('txtmontoefectivo'+id).style.display = 'inline-block';
		document.getElementById('txtmontovisa'+id).style.display = 'inline-block';
		//document.getElementById('txtmontomastercard'+id).style.display = 'inline-block';
		document.getElementById('GuardaAdicional'+id).style.display= 'inline-block';
		//document.getElementById('botonevisa').style.display = 'none';
		//document.getElementById('botonefectivo').style.display = 'none';
	}
function calcularpagocompartido() {
		var costoadividir = parseFloat(document.form1.txtcostototal.value);
		var	costodividido = parseFloat(costoadividir/3);

			//document.form1.txtmontoefectivo.value = costodividido.toFixed(2);
			//document.form1.txtmontovisa.value = costodividido.toFixed(2);
	}

	function prueba (dato,visa,efectivo, montoTotal){
		 var visa=$("#"+visa).val(),
		 efectivo=$("#"+efectivo).val();
		 var total = parseFloat(visa) + parseFloat(efectivo);
		 if(parseFloat(montoTotal) == parseFloat(total)){
			//mastercard=$("#"+mastercard).val();
			window.location = dato+visa+"&monto2="+efectivo+"&monto3="+0;
		}else{
			alert("Los montos no coinciden con el total: S/ " + montoTotal);
		}
		
	}

</script>
