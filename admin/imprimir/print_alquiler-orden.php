<?php
include "../validar.php";
include "../config.php";
include "../include/functions.php";
date_default_timezone_set('America/Lima');
include "../include/Configuraciones.php";
$configs = new Configuraciones($mysqli);
$razonSocial = $configs->getConfig("RAZON_SOCIAL");
$direccionFiscal = $configs->getConfig("DIRECCION_FISCAL");
$direccionFiscalDistrito = $configs->getConfig("DIRECCION_FISCAL_DISTRITO");
$direccionFiscalProvincia = $configs->getConfig("DIRECCION_FISCAL_PROVINCIA");
$telefonos = $configs->getConfig("TELEFONO");
$xprecioalquiler=0;
$precioalquilerpendiente =0;
$xidhabitacion = $_GET['idhabitacion'];
$xidalquiler = $_GET['idalquiler'];
$xprodtotal =0;
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
	al_venta.totalefectivo,
	al_venta.totalvisa,
	al_venta.descuento
	
	from al_venta inner join cliente on cliente.idhuesped = al_venta.idhuesped
	where al_venta.idalquiler = '$xidalquiler' 
	");

	
	$xaFila = $sqlalquiler->fetch_row();

	if($xaFila[10] > 0){
		$formapago="Efectivo";
	}elseif ($xaFila[11] > 0) {
		$formapago="Visa";
	}

//Detalle Aquiler
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
	idusuario
	
	
	from al_venta_detalle 
	where idalquiler = '$xidalquiler' and estadopago in(0,1) order by idalquilerdetalle asc
	");

//Consumos *****
$sqlventa = $mysqli->query("select
	venta.idventa,
	venta.idalquiler,
	ventadetalle.idventadetalle,
	ventadetalle.idventa,
	ventadetalle.nombre,
	ventadetalle.cantidad,
	ventadetalle.precio,
	ventadetalle.importe
	
	from venta left join ventadetalle on ventadetalle.idventa = venta.idventa
	where venta.idalquiler = '$xidalquiler'	order by ventadetalle.idventadetalle asc");
		
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>

<script type="text/javascript">
	function imprimir() {
		if (window.print) {
			window.print();
		} else {
			alert("La función de impresion no esta soportada por su navegador.");
		}
	}
</script>

<style>
	.textoContenido{
	font-family: Arial, sans-serif;
	font-size: 15px;
	color: #000000;
	text-align:left;
}
</style>


</head>

<body onload="imprimir();">
<table width="260" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td height="22" align="center"><span class="textoContenido"><strong><?php echo $razonSocial; ?> </strong></span></td>
    </tr>
    <tr>
      <td height="22" align="center"><span class="textoContenido"><?php echo $direccionFiscal; ?></span></td>
    </tr>
    <tr>
      <td height="22" align="center"><span class="textoContenido"><?php echo $direccionFiscalDistrito; ?> - <?php echo $direccionFiscalProvincia; ?> / <?php echo $telefonos; ?></span></td>
    </tr>
    <tr>
      <td height="20" align="center"><span class="textoContenido">Recepción: <?php echo $_SESSION['xyznombre'];?></span></td>
    </tr>
    <tr>
      <td height="20">&nbsp;</td>
    </tr>
    <tr>
      <td height="20"><strong><span class="textoContenido">ORDEN # <?php echo $xaFila['9'];?></span></strong></td>
    </tr>
    <tr>
      <td height="20"><span class="textoContenido">------------------------------------------------</span></td>
    </tr>
    <tr>
      <td height="20"><strong><span class="textoContenido">Habitación: <span class="textoContenido"><?php echo $xaFila['3'];?></span></span></strong></td>
    </tr>
    <tr>
      <td height="20"><span class="textoContenido">Cliente: <?php echo $xaFila['7'];?></span></td>
    </tr>
    <tr>
      <td height="20">&nbsp;</td>
    </tr>
    <tr>
      <td height="20"><span class="textoContenido">------------------------------------------------</span></td>
    </tr>
     <?php $num=0; while ($tmpFila = $sqldetalle->fetch_row()){ $num++; ?>
    <tr>
      <td height="46">
      <span class="textoContenido">
        - <?php 
								
		if($tmpFila['12']==1){ //Estado Pago
			$xprecioalquiler = $xprecioalquiler + $tmpFila['20'];
		}else{
			$precioalquilerpendiente = $precioalquilerpendiente + $tmpFila['20'];
		}
		echo tipoAlquiler($tmpFila['2']).' ('.$tmpFila['19'].')';
		if($tmpFila['2'] != 4 &&  $tmpFila['2'] != 5){
			echo fechadesdehasta($tmpFila['3'],$tmpFila['4']);
		}
		?>
      <br>
      S/ <?php echo number_format($tmpFila['20'],2);?> (<?php echo estadoPago($tmpFila['12'],2);?>)      </span></td>
    </tr>
    <?php } ?>
    <tr>
      <td height="20">&nbsp;</td>
    </tr>
    <?php $nroventa = $sqlventa->num_rows; if($nroventa!=""){ ?>
    <tr>
      <td height="20"><strong><span class="textoContenido">CONSUMO</span></strong></td>
    </tr>
    <tr>
      <td height="20"><span class="textoContenido">------------------------------------------------</span></td>
    </tr>
    <?php $xprodtotal = 0; $num = 0; while($vFila = $sqlventa->fetch_row()){?>
    <tr>
      <td height="20"><span class="textoContenido">
      (<?php echo $vFila['5']; ?>) <?php echo $vFila['4']; ?> - S/ <?php echo $vFila['7']; ?></span>
      </td>
    </tr>
     <?php
		$xprodtotal = $xprodtotal + $vFila['7']; 
	}
		} 
	?>
    <tr>
      <td height="20">&nbsp;</td>
    </tr>
    <tr>
      <td height="20"><span class="textoContenido">------------------------------------------------</span></td>
    </tr>
    <tr>
      <td height="20"><span class="textoContenido"><strong> Descuento: S/ <?php echo number_format(($xaFila[12]),2);?></strong></span></td>
    </tr>
    <tr>
      <td height="20"><span class="textoContenido">------------------------------------------------</span></td>
    </tr>
    <tr>
      <td height="20"><span class="textoContenido"><strong> Total Pagado: S/ <?php echo number_format(($xprecioalquiler+$xprodtotal)- $xaFila[12],2);?></strong></span></td>
    </tr>
    <tr>
      <td height="20"><span class="textoContenido"> Total Pendiente: S/ <?php echo number_format($precioalquilerpendiente,2);?></span></td>
    </tr>
    <tr>
      <td height="20"><span class="textoContenido">------------------------------------------------</span></td>
    </tr>
     <tr>
      <td height="20"><span class="textoContenido"><strong>Forma de Pago:</strong>  <?php echo $formapago;?></span></td>
    </tr>
    <tr>
      <td height="20">&nbsp;</td>
    </tr>
    <tr>
      <td height="20"><span class="textoContenido"><?php echo Cfecha(date("Y-m-d")).' - '.date("H:i")?></span></td>
    </tr>
    <tr>
      <td height="20" class="textoContenido">&nbsp;</td>
    </tr>

</table>

</body>
</html>