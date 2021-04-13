<?php
session_start();
include "validar.php";
include "include/functions.php";

include "config.php";
$idalquiler = $_GET['idalquiler'];

//echo $idalquiler;

$sqlalquiler = $mysqli->query("select
      al_venta.idalquiler,  
      al_venta.nrohabitacion,           
      cliente.nombre,
      cliente.ciudad,
      cliente.tipo_documento,
      cliente.documento,
      
      al_venta.comentarios,
      al_venta.nroorden,
      case when al_venta.fechaemision is null then al_venta.fecharegistro else al_venta.fechaemision end,

      al_venta.documento,
      al_venta.codigo_respuesta,
      al_venta.mensaje_respuesta,
      al_venta.nombrezip,
      al_venta.nombre_archivo,
      al_venta.total,
      al_venta.descuento,
      (SELECT sum(det.total + IFNULL(ven.total,0)) FROM al_venta_detalle det LEFT JOIN venta ven ON det.idalquiler=ven.idalquiler WHERE det.idalquiler=al_venta.idalquiler) as tot
      
      from al_venta 
      inner join cliente on cliente.idhuesped = al_venta.idhuesped
	    where idalquiler = '$idalquiler'
	    order by al_venta.fecharegistro asc");

$item = 0;
$nombreXml="";
$nombrePdf="";
$nombreFile="";
$nombreCliente="";
while ($xhFila = $sqlalquiler->fetch_row()) {
    $nombreCliente=$xhFila['2'];
    $nombreFile=$xhFila['13'];
    $nombrePdf = $xhFila['13'].'.pdf';
    $nombreXml = substr($xhFila['12'], 0, -4) . '.xml';
}
//$tFila = $sqltipoAdic->fetch_row();

?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Añadir datos de huespedes adicionales</title>
<?php include "head-include.php"; ?>

</head>
<body>
<!--<body OnLoad="form1.txtnombre.focus()">-->
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="100%" height="25" valign="middle" bgcolor="#FFFFFF">

    <form name="form1" method="post" action="PHPMailer/sendEmail.php?idalquiler=<?php echo $idalquiler;?>">

      <table width="100%" border="0" cellpadding="1" cellspacing="1">
        <tr>
          <td colspan="2">
              <p>
                <label for="rdtipo2" class="textoContenidoMenor"> </label>
                <span class="textoContenido"><strong>Envio de Factura Electrónica por Email</strong></span>
              </p>
          </td>
        </tr>
          <?php if (isset($_SESSION['msgeinfo'])){ ?>
          <tr>
              <td colspan="2">
                  <div class="alert alert-success alert-dismissable textoContenidoMenor">
                      <?php
                      echo $_SESSION['msgeinfo'];
                      $_SESSION['msgeinfo']="";
                      ?>
                      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                  </div>
              </td>
          </tr>
          <?php } ?>
          <?php if (isset($_SESSION['msgeerror'])){ ?>
              <tr>
                  <td colspan="2">
                      <div class="alert alert-danger alert-dismissable textoContenidoMenor">
                          <?php
                          echo $_SESSION['msgeerror'];
                          $_SESSION['msgeerror']="";
                          ?>
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      </div>
                  </td>
              </tr>
          <?php } ?>
        <tr>
          <td width="20%" class="textoContenidoMenor">Enviar a:</td>
          <td width="33%" class="textoContenidoMenor">
              <input name="txtcorreo" type="text" class="textbox" id="txtcorreo">
          </td>
        </tr>
        <tr>
              <td width="20%" class="textoContenidoMenor">Copia a:</td>
            <td width="33%" class="textoContenidoMenor">
                <input name="txtcorreocopia" type="text" class="textbox" id="txtcorreocopia" value="corporacionchac@gmail.com">
            </td>
        </tr>
          <tr>
              <td width="20%" class="textoContenidoMenor">Asunto:</td>
              <td width="33%" class="textoContenidoMenor">
                  <input name="txtasunto" type="text" class="textbox" id="txtasunto" value="COMPROBANTE ELECTRONICO <?php echo $nombreFile; ?>">
              </td>
          </tr>
          <tr>
              <td width="20%" class="textoContenidoMenor">Cuerpo del Mensaje:</td>
              <td width="33%" class="textoContenidoMenor">
                  <textarea name="txtcuerpo" id="txtcuerpo" rows="6" cols="60">Estimado <?php echo $nombreCliente;?>:

                      Le remitimos los comprobantes electronicos <?php echo $nombreFile; ?>.
                  </textarea>
              </td>
          </tr>
        <tr>
            <td width="20%" class="textoContenidoMenor">&nbsp;
                <input name="Pathfilexml" id="Pathfilexml" type="hidden" value="FE/XMLFIRMADOS/<?php echo $nombreXml;?>"/>
                <input name="Pathfilepdf" id="Pathfilepdf" type="hidden" value="FE/PDF/<?php echo $nombrePdf;?>"/>
            </td>
          <td>
              <button type="submit" class="btnnegro" style="border:0px; cursor:pointer;"> <i class="fa fa-save"></i> Enviar Email</button>
          </td>
        </tr>
      </table>
    </form>
    </td>
  </tr>

<?php
$sqlalquiler->free();
$mysqli->close()
?>
</table>
</body>
</html>