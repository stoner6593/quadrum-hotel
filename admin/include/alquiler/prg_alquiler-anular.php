<?php
session_start();
include "../../config.php";
include "../functions.php";

//--------------------------------------------------------------
$xidturno = $_SESSION['idturno'];
$idusuario = $_SESSION['xyzidusuario'];
$xidalquiler = $_GET['idalquiler'];
$xidhabitacion = $_GET['idhabitacion'];

//estadoalquiler: 1=Activo - 0=Anulado
$xestadoalquiler = 0;
$xmotivoanulacion = $_POST['txtmotivoanulacion'];

//Actualizar Alquiler
$consulta="update al_venta set
	estadoalquiler = '$xestadoalquiler',
	motivoanulacion = '$xmotivoanulacion',
	anulado = 1,
	anulaporusuario=1
	where idalquiler = '$xidalquiler' and idhabitacion = '$xidhabitacion'";
if($mysqli->query($consulta)){}

//Actualizar Habitacion
$consultahabitacion = "update hab_venta set
	idalquiler = 0,
	idestado = 1
	where idhabitacion = '$xidhabitacion'";

//Actualiza Ingreso
/*$consultahabitacion = "update ingresosturno set
	estadoturno = 0,
	totalhabitacion = 0,
	totaladicional=0,
	totalproducto=0,
	totalefectivo=0,
	totalvisa=0
	where idhabitacion = '$xidhabitacion'";*/

	$TotalAlquiler=0; $MontoVisa=0; $Descuento=0; $TotalProductos=0;
	$sqlalquiler = $mysqli->query("select
			al_venta.idalquiler,
			al_venta.idhuesped,
			al_venta.idhabitacion,
			al_venta.nrohabitacion,
			al_venta.tipooperacion,
			al_venta.total,
			
			cliente.idhuesped,
			cliente.nombre,
			cliente.ciudad,
			cliente.tipo_documento,
			cliente.documento,
			
			al_venta.comentarios,
			al_venta.nroorden,
			al_venta.fecharegistro,
			al_venta.totalefectivo,
			al_venta.totalvisa,
			al_venta.descuento
			
			from al_venta inner join cliente on cliente.idhuesped = al_venta.idhuesped
			where al_venta.idalquiler = '$xidalquiler' 
			");		
			$xaFila = $sqlalquiler->fetch_row();
			

	$MontoVisa=	$xaFila[15];
	$Descuento=	$xaFila[16];
	$MontoEfectivo=$xaFila[14];

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
		where idalquiler = '$xidalquiler'   and estadopago!=2 order by idalquilerdetalle asc
		");
	
	while ($tmpFila = $sqldetalle->fetch_row()){ $num++; 

		$TotalAlquiler+=$tmpFila[20];
		
	}

    $sqldetalleAnulado = $mysqli->query("select
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
		where idalquiler = '$xidalquiler'   and estadopago!=2 order by idalquilerdetalle asc
		");

	    $MontoVisaAnuladoDetalle = 0;
        $MontoEfectivoAnuladoDetalle = 0;
        while ($tmpFila = $sqldetalleAnulado->fetch_row()){ $num++;
            $MontoVisaAnuladoDetalle+=$tmpFila[11];
            $MontoEfectivoAnuladoDetalle+=$tmpFila[10];
        }
	//FIN DETALLE ALQUILER


	//INICIO DETALLE VENTAS
	$sqlventa = $mysqli->query("select
	venta.idventa,
	venta.idalquiler,
	ventadetalle.idventadetalle,
	ventadetalle.idventa,
	ventadetalle.nombre,
	ventadetalle.cantidad,
	ventadetalle.precio,
	ventadetalle.importe,
	venta.formapago
	
	from venta left join ventadetalle on ventadetalle.idventa = venta.idventa
	where venta.idalquiler = '$xidalquiler'  order by ventadetalle.idventadetalle asc");

    $montoProductosEfectivo = 0;
    $montoProductosVisa = 0;
	while($vFila = $sqlventa->fetch_row()){
		$TotalProductos+=($vFila[6] * $vFila[5]);
		if($vFila[6]=="1"){
            $montoProductosEfectivo += ($vFila[6] * $vFila[5]);
        }
        if($vFila[6]=="2"){
            $montoProductosVisa += ($vFila[6] * $vFila[5]);
        }
	}

    //INICIO DETALLE VENTAS
    $sqlventa = $mysqli->query("select
        venta.idventa,
        venta.idalquiler,
        ventadetalle.idventadetalle,
        ventadetalle.idventa,
        ventadetalle.nombre,
        ventadetalle.cantidad,
        ventadetalle.precio,
        ventadetalle.importe,
        venta.formapago
        
        from venta left join ventadetalle on ventadetalle.idventa = venta.idventa
        where venta.idalquiler = '$xidalquiler' and flag_venta_alquiler_inicial=1
        order by ventadetalle.idventadetalle asc");

    $montoProductosRegistroInicialAlquiler = 0;
    while($vFila = $sqlventa->fetch_row()){
        $montoProductosRegistroInicialAlquiler+=($vFila[6] * $vFila[5]);
    }

    if($MontoEfectivo>0){
        if($MontoEfectivo>=$montoProductosRegistroInicialAlquiler){
            $MontoEfectivo = $MontoEfectivo - $montoProductosRegistroInicialAlquiler;
        }
    }else{
        if($MontoVisa>0){
            if($MontoVisa>=$montoProductosRegistroInicialAlquiler){
                $MontoVisa = $MontoVisa - $montoProductosRegistroInicialAlquiler;
            }
        }
    }

	$consultaturno = "update ingresosturno set
		totalhabitacion = (totalhabitacion - $TotalAlquiler) ,
		totalproducto =  totalproducto - $TotalProductos,
		totalefectivo = (totalefectivo - ($TotalProductos + $MontoEfectivoAnuladoDetalle)) ,
		totalvisa = totalvisa  - $MontoVisaAnuladoDetalle,
		totaldescuento = totaldescuento - $Descuento,
		totalanulado = totalanulado + $TotalAlquiler + $TotalProductos - $Descuento,
		totalefectivoanulado = totalefectivoanulado + $MontoEfectivoAnuladoDetalle - $Descuento,
		totalvisaanulado = totalvisaanulado + $MontoVisaAnuladoDetalle,
		totalproductoanulado = totalproductoanulado + $TotalProductos
		where idturno = '$xidturno'";   //(totalefectivo - ($TotalAlquiler + $TotalProductos)) ,
		if($mysqli->query($consultaturno)){}

if($mysqli->query($consultahabitacion)){}

//echo $consultaturno;

			
$mysqli->close();	
//$_SESSION['msgerror'] = $Men;
	header("Location: ../../control-habitaciones.php"); exit;
//************************************************************
?>