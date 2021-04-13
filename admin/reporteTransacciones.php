<?php //ini_set('display_errors', 1);
include "validar.php";
include "config.php";
include "include/functions.php";
date_default_timezone_set('America/Lima');

$xidturno = isset($_GET["idturno"]) ? $_GET["idturno"] : $_SESSION['idturno'];

$sqlusuarioturno = $mysqli->query("select 
  idturno,
  idusuario
  from ingresosturno where idturno = '$xidturno'");
  $xuFila = $sqlusuarioturno->fetch_row();
  
  $xidusuario = $xuFila["1"]; //Usuario de Turno

//RESUMEN DE PRODUCTOS
//$xidusuario = $_SESSION['xyzidusuario'];


//INGRESO HABITACION
$sqlturno = $mysqli->query("
  SELECT
    i.idturno,
    i.totalhabitacion,
    i.totaladicional,
    i.totalproducto,
    i.totalefectivo,
    i.totalvisa,
    i.idusuario,
    i.estadoturno,
    i.fechaapertura,
    i.fechacierre,
    i.totaldescuento,
    i.totalanulado,
    i.totalefectivoanulado,
    i.totalvisaanulado,
    i.totalproductoanulado,    
    (SELECT user_nombre FROM usuario u WHERE u.user_id = i.idusuario) as usu,
    i.turno,
    i.totalmastercard
  FROM ingresosturno i
  WHERE i.idturno = '$xidturno'");

$hFila = $sqlturno->fetch_row();
  
  //Habitacion
  $xhabitacion = $hFila['1'];

  //Producto
  $xproducto = $hFila['3'];
  
  //Visa/Efectivo
  $xefectivo = $hFila['4'];
  $xvisa = $hFila['5'];
  $xmastercard = $hFila['17'];
  
  //$xsumatotal = $hFila['6'];
  
  //TOTAL INGRESOS DE TURNO
  

//EGRESO O GASTOS
$sqlgastos = $mysqli->query("select
  idgasto,
  monto,
  estadoturno,
  usuario,
  tipooperacion
  from gasto 
  where idturno = '$xidturno' and usuario = '$xidusuario'");
  
  $xcompra = 0;
  $xgasto = 0;
  $xsumaegreso = 0;
  while($gFila = $sqlgastos->fetch_row()){
    if ($gFila['4']==1){ //Compras
      $xcompra = $xcompra + $gFila['1'];//total
    }elseif($gFila['4']==2){ //Gastos
      $xgasto = $xgasto + $gFila['1'];//total
    }
    $xsumaegreso = $xsumaegreso + $gFila['1'];
  }

function getTotReserva($mysqli, $xidturno, $xidusuario, $tipo){
  return $mysqli->query("
    SELECT 
      sum(total) as tot
    FROM al_venta_detalle
    WHERE idturno = '$xidturno' and idusuario = '$xidusuario' and tiporeserva = '$tipo'
  ");
}

$totCredito   = getTotReserva($mysqli, $xidturno, $xidusuario, 5)->fetch_row();
$totDeposito  = getTotReserva($mysqli, $xidturno, $xidusuario, 6)->fetch_row();
$totPagoLinea = getTotReserva($mysqli, $xidturno, $xidusuario, 7)->fetch_row();

$sqlVentasTurno = $mysqli->prepare("
  SELECT 
    v.idalquiler + 1000 as orden,
    (SELECT hv.numero FROM hab_venta hv WHERE hv.idhabitacion = v.idhabitacion) as habitacion,
    (SELECT c.nombre FROM cliente c WHERE c.idhuesped = v.idhuesped) as huesped,
    v.total as total,
    v.idalquiler as idalquiler,
    vd.idalquilerdetalle,
    vd.tipoalquiler,
    vd.totalefectivo,
    vd.totalvisa,
    vd.totalmastercard,
    vd.detoriginal,
    v.totalefectivo as totef,
    v.totalvisa as totvis,
    v.totalmastercard as totmas,
    v.fecharegistro,
     CASE(vd.tiporeserva)
    	WHEN 0 THEN ''
        WHEN 1  THEN 'Booking'
        WHEN 2  THEN 'Reserva Web'
        WHEN 3  THEN 'PROMO FDS'
        WHEN 4  THEN 'Facebook'
        WHEN 5  THEN 'Cr®¶dito'
        WHEN 6  THEN 'Deposito'
        WHEN 7  THEN 'Pago en linea'
    END AS tipo_reserva
  FROM al_venta v 
  INNER JOIN al_venta_detalle vd ON v.idalquiler = vd.idalquiler
  WHERE vd.idturno = '$xidturno'
");

$sqlVentasTurno->execute(); 
$resultVentasTurno = $sqlVentasTurno->get_result();
$resultVentasTurno = $resultVentasTurno->fetch_all(MYSQLI_ASSOC);

$sqlVentasProdTurno = $mysqli->prepare("
  SELECT 
    v.numero as orden,
    v.cliente as huesped,
    v.formapago as formapago,
    vd.idventadetalle as idvd,
    vd.nombre as prod,
    vd.cantidad as cantidad,
    vd.precio as precio,
    vd.importe as importe,
    v.fecha,
    v.hora
  FROM venta v 
  INNER JOIN ventadetalle vd ON v.idventa = vd.idventa
  WHERE v.idturno = '$xidturno'
");

$sqlVentasProdTurno->execute(); 
$resultVentasPTurno = $sqlVentasProdTurno->get_result();
$resultVentasPTurno = $resultVentasPTurno->fetch_all(MYSQLI_ASSOC);

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Reporte transacciones</title>
  <link href="opera.css" rel="stylesheet" type="text/css">
  <script src="chartjs/Chart.js"></script>
  <link href="http://fontawesome.io/assets/font-awesome/css/font-awesome.css" rel="stylesheet">
  <link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link href="dist/css/tableexport.css" rel="stylesheet" type="text/css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.14.5/xlsx.js"></script>
<script src="https://unpkg.com/tableexport@5.2.0/dist/js/tableexport.js"></script>

</head>
<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
      <td height="25" colspan="3"><?php include ("head.php"); ?></td>
    </tr>
    <tr>
      <td width="185" height="25" align="left" valign="top"><?php include ("menu_nav.php"); ?></td>
      <td width="25">&nbsp;</td>
      <td width="793" valign="top">
        

        <table id="data-report" width="90%">
          <tr>
            <td colspan="8" class="textoContenido" style="color:#E1583E; font-size: 20px;">
              Reporte transacciones turno ID: <?php echo $hFila[0]; ?>
            </td>
          </tr>
          <tr>
            <td colspan="8" class="textoContenido">
              Usuario: <?php echo $hFila[15]; ?>
            </td>
          </tr>
          <tr>
            <td colspan="8" class="textoContenido">
              Turno: <?php echo($hFila[16] == 1 ? "Dia" : "Noche"); ?>
            </td>
          </tr>
          <tr>
            <td colspan="8" class="textoContenido">
              Inicio: <?php echo $hFila[8]; ?>, 
              Fin: <?php echo($hFila[9] == "1900-01-01 00:00:00" ? "<i>En progreso</i>" : $hFila[9]); ?>
            </td>
          </tr>
          <tr>
            <td colspan="8" class="textoContenido">
              &nbsp;
            </td>
          </tr>
          <tr>
            <td colspan="8" class="textoContenido" style="font-size: 18px; font-weight: bold;">
              INGRESOS DE TURNO
            </td>
          </tr>
          <tr>
            <td colspan="8" class="textoContenido">
              Ingreso de Habitaciones: <strong>S/ <?php echo number_format($xhabitacion,2);?></strong>
            </td>
          </tr>
          <tr>
            <th class="textoContenido">Orden</th>
            <th class="textoContenido">Habitaci√≥n</th>
            <th class="textoContenido">Huesped</th>
            <!--<th class="textoContenido">Total</th>-->
            <th class="textoContenido">ID Detalle</th>
            <th class="textoContenido">Tipo</th>
            <th class="textoContenido" style="text-align: right;">Efectivo</th>
            <th class="textoContenido" style="text-align: right;">Tarjeta</th>
            <!--<th class="textoContenido" style="text-align: center;">Master Card</th>-->
            <th class="textoContenido" style="text-align: center;">Total</th>
            <th class="textoContenido" style="text-align: left;">Generado</th>
          </tr>
          <?php foreach ($resultVentasTurno as $row){
              $tefectivo = 0;
              $tvisa = 0;
              $tmastercard = 0;

              if($row["totalefectivo"] == 0 && $row["totalvisa"] == 0 && $row["totalmastercard"] == 0){
                $tefectivo = $row["totef"];
                $tvisa = $row["totvis"];
                $tmastercard = $row["totmas"];
              }else{
                $tefectivo = $row["totalefectivo"];
                $tvisa = $row["totalvisa"];
                $tmastercard = $row["totalmastercard"];
              }
           ?>
          <tr>
            <td class="textoContenido"><?php echo $row["orden"]; ?></td>
            <td class="textoContenido"><?php echo $row["habitacion"]; ?></td>
            <td class="textoContenido"><?php echo $row["huesped"]; ?></td>
            <!--<td class="textoContenido">S/ <?php echo $aFila[3]; ?></td>-->
            <td class="textoContenido"><?php echo $row["idalquilerdetalle"]; ?></td>
            <td class="textoContenido">
              <?php 
              if($row["tipoalquiler"] == 1 || $row["tipoalquiler"] == 2 || $row["tipoalquiler"] == 6){
                if($row["detoriginal"] == 0){
                  echo tipoAlquiler($row["tipoalquiler"]) . " - Renovaci√≥n"." ".$row["tipo_reserva"];
                }else{
                  echo tipoAlquiler($row["tipoalquiler"])." ".$row["tipo_reserva"];
                }
              }else{
                echo tipoAlquiler($row["tipoalquiler"])." ".$row["tipo_reserva"];
              } ?>
            </td>
            <td class="textoContenido" style="text-align: right;">
              <?php echo($tefectivo == 0 ? "--" : "S/ ".$tefectivo); ?></td>
            <td class="textoContenido" style="text-align: right;">
              <?php echo($tvisa == 0 ? "--" : "S/ ".$tvisa); ?></td>
            <!--<td class="textoContenido" style="text-align: right;">
              <?php echo($tmastercard == 0 ? "--" : "S/ ".$tmastercard); ?></td>-->
            <td class="textoContenido" style="text-align: right;">
              S/ <?php echo  number_format($tefectivo+$tvisa+$tmastercard,2); ?></td>
            <td class="textoContenido">
              &nbsp;&nbsp;&nbsp;<?php echo $row["fecharegistro"]; ?></td>
          </tr>
          <?php } ?>
          <tr>
            <td colspan="8" class="textoContenido">
              &nbsp;
            </td>
          </tr>
          <tr>
            <td colspan="8" class="textoContenido">
              Ingreso de Productos/Servicios: <strong>S/ <?php echo number_format($xproducto,2);?></strong>
            </td>
          </tr>
          <tr>
            <th class="textoContenido">Orden</th>
            <th class="textoContenido">Huesped</th>
            <th class="textoContenido">Forma pago</th>
            <th class="textoContenido">ID Detalle</th>
            <th class="textoContenido">Producto</th>
            <th class="textoContenido">Cantidad</th>
            <th class="textoContenido">Precio</th>
            <th class="textoContenido">Importe</th>
            <th class="textoContenido" style="text-align: center;">Generado</th>
          </tr>
          <?php foreach ($resultVentasPTurno as $row){ ?>
          <tr>
            <td class="textoContenido"><?php echo $row["orden"]; ?></td>
            <td class="textoContenido"><?php echo $row["huesped"]; ?></td>
            <td class="textoContenido">
              <?php echo ($row["formapago"] == 1 ? "Efectivo" : "Visa"); ?></td>
            <td class="textoContenido"><?php echo $row["idvd"]; ?></td>
            <td class="textoContenido"><?php echo $row["prod"]; ?></td>
            <td class="textoContenido"><?php echo $row["cantidad"]; ?></td>
            <td class="textoContenido">S/ <?php echo $row["precio"]; ?></td>
            <td class="textoContenido">S/ <?php echo $row["importe"]; ?></td>
            <td class="textoContenido">
              &nbsp;&nbsp;&nbsp;<?php echo $row["fecha"]; ?> <?php echo $row["hora"]; ?></td>
          </tr>
          <?php } ?>
          <tr>
            <td colspan="8" class="textoContenido">
              &nbsp;
            </td>
          </tr>
          <tr>
            <td colspan="8" class="textoContenido">
              Total Tarjeta: <strong>S/ <?php echo number_format($xvisa,2);?></strong>
            </td>
          </tr>
          <tr>
            <td colspan="8" class="textoContenido">
              &nbsp;
            </td>
          </tr>
          <!--<tr>
            <td colspan="8" class="textoContenido">
              Total master card: <strong>S/ <?php echo number_format($xmastercard,2);?></strong>
            </td>
          </tr>-->
          <tr>
            <td colspan="8" class="textoContenido">
              &nbsp;
            </td>
          </tr>
          <tr>
            <td colspan="8" class="textoContenido">
              Total Efectivo: <strong>S/ <?php echo number_format($xefectivo - $hFila[10],2);?></strong>
            </td>
          </tr>
          <tr>
            <td colspan="8" class="textoContenido">
              &nbsp;
            </td>
          </tr>
          <tr>
            <td colspan="8" class="textoContenido" style="font-size: 18px;">
              Total General: <strong>S/ <?php echo number_format(($xhabitacion+$xproducto) - $hFila[10] ,2);?></strong>
            </td>
          </tr>
        </table>

<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="FileSaver.min.js"></script>
<script src="Blob.min.js"></script>
<script src="xls.core.min.js"></script>

<script src="dist/js/tableexport.js"></script>
<script>
  $("#data-report").tableExport({
    formats: ["xlsx"],
    position:"top"
  });
//$("#data-report").tableExport({formats: ["xlsx","xls", "csv", "txt"],    });
</script>

      </td>
    </tr>
    <tr>
      <td height="25" colspan="3"></td>
    </tr>
    <tr>
      <td height="25" colspan="3"></td>
    </tr>
  </tbody>
</table>


</body>
</html>
<?php include ("footer.php") ?>