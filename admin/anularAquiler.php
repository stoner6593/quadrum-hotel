<?php
include "validar.php";
include "config.php";
include "include/functions.php";
date_default_timezone_set('America/Lima');

//Cuando viene desde resumen diario
$idalquiler = $_GET['idalquiler'];
$tipoventa = $_GET['tipoVenta'];

if($tipoventa=='1'){
  $sqlalquiler = $mysqli->query("select
        al_venta.idalquiler,  
        al_venta.nrohabitacion,           
        cliente.nombre,
        cliente.ciudad,
        cliente.tipo_documento,
        cliente.documento,
        
        al_venta.comentarios,
        al_venta.nroorden,
        al_venta.fecharegistro,

        al_venta.documento,
        al_venta.codigo_respuesta,
        al_venta.mensaje_respuesta,
        al_venta.nombrezip,
        al_venta.nombre_archivo,
        al_venta.total,
        al_venta.descuento,
        (SELECT sum(det.total + IFNULL(ven.total,0)) FROM al_venta_detalle det 
        LEFT JOIN venta ven ON det.idalquiler=ven.idalquiler WHERE det.idalquiler=al_venta.idalquiler) as tot,
        al_venta.anulado,
        al_venta.anulado_motivo,
        al_venta.iddocumento,
        tab_tipodocumento.num_docu,
        tab_tipodocumento.nombre_docu
        
        from al_venta 
        inner join cliente on cliente.idhuesped = al_venta.idhuesped
        left join tab_tipodocumento on tab_tipodocumento.id_docu = al_venta.iddocumento
        where al_venta.codigo_respuesta = 0 and al_venta.ticket is null and al_venta.iddocumento=1 
        and al_venta.enviado=1 and al_venta.idalquiler='$idalquiler'");

  $xhFila = $sqlalquiler->fetch_row();
}else{
  $sqlalquiler = $mysqli->query("select
        venta.idventa,  
        '-',           
        cliente.nombre,
        cliente.ciudad,
        cliente.tipo_documento,
        cliente.documento,
        
        venta.anotaciones,
        venta.numero,
        venta.fecha,

        venta.documento,
        venta.codigo_respuesta,
        venta.mensaje_respuesta,
        venta.nombrezip,
        venta.nombre_archivo,
        venta.total,
        venta.descuento,
        (SELECT sum(det.importe) FROM ventadetalle det 
        WHERE det.idventa=venta.idventa) as tot,
        venta.anulado,
        venta.anulado_motivo,
        venta.iddocumento,
        tab_tipodocumento.num_docu,
        tab_tipodocumento.nombre_docu
        
        from venta 
        left join cliente on cliente.idhuesped = venta.idcliente
        left join tab_tipodocumento on tab_tipodocumento.id_docu = venta.iddocumento
        where venta.codigo_respuesta = 0 and venta.ticket is null and venta.iddocumento=1 
        and venta.enviado=1 and venta.idventa='$idalquiler'");

  $xhFila = $sqlalquiler->fetch_row();
}
	
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Administrador</title>

<?php  include "head-include.php"; ?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />    

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
            <td width="638" height="30"> <h3 style="color:#E1583E;"> <i class="fa fa-users"></i> COMPROBANTE / Anular</h3></td>
            <td width="155" align="center"> <button type="button" onclick="window.location.href='resumendiario.php';" class="btngris" style="border:0px; cursor:pointer;"> <i class="fa fa-arrow-left"></i> Volver </button> </td>
          </tr>
          <tr>
            <td height="30" colspan="2"><table width="100%" border="0" cellpadding="1" cellspacing="1">
             
                <tr>
                  <td height="30">
                  <div class="lineahorizontal" style="background:#BFBFBF;"></div><br>
                  
				  <?php if (isset($_SESSION['msgerror'])){ ?>
                  <div class="alert alert-danger alert-dismissable textoContenidoMenor">
                  	<?php echo $_SESSION['msgerror'];$_SESSION['msgerror']="";?> 
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                  </div>
                  <?php } ?>
                  
                  </td>
                </tr>
                <tr>
                  <td height="30">
                    <form id="form1" name="form1" method="post" action="include/alquiler/prg_alquiler_anular_comprobante.php">
                    <table width="99%" border="0" cellpadding="1" cellspacing="1">
                      <tbody>
                        <tr>
                            <td width="221" height="30"><span class="textoContenido">
                              Tipo de Documento:  </span>                      
                            </td>
                            <td>
                              <?php echo $xhFila['21']?>
                              <input name="txtidalquiler" type="hidden" id="txtidalquiler" value="<?php echo $idalquiler; ?>">
                              <input name="txttipoventa" type="hidden" id="txttipoventa" value="<?php echo $tipoventa; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td height="30"><span class="textoContenido">Documento:</span></td>
                            <td height="30">
                              <?php echo $xhFila['9']?>
                            </td>
                        </tr>
                        <tr>
                            <td height="30"><span class="textoContenido"># Orden:</span></td>
                            <td height="30">
                              <?php echo $xhFila['7']?>
                            </td>
                        </tr>
                        <tr>
                            <td height="30"><span class="textoContenido">Fecha:</span></td>
                            <td height="30">
                              <?php echo $xhFila['8']?>
                            </td>
                        </tr>
                        <tr>
                            <td height="30"><span class="textoContenido">Huesped:</span></td>
                            <td height="30">
                              <?php echo $xhFila['5']?> - <?php echo $xhFila['2']?>
                            </td>
                        </tr>
                        <tr>
                            <td height="30"><span class="textoContenido">Total:</span></td>
                            <td height="30">
                              S/ <?php echo $xhFila['16']?>
                            </td>
                        </tr>
                        <tr>
                          <td height="10"><span class="textoContenido">Motivo:</span></td>
                          <td height="10">&nbsp;</td>
                          <td height="10">&nbsp;</td>
                          <td height="10">&nbsp;</td>
                        </tr>
                        <tr>
                          <td height="10" colspan="4"><textarea name="txtmotivo" class="form-control" id="txtmotivo" style="width:70%"></textarea>
                          </td>
                        </tr>
                        <tr>
                          <td height="10">&nbsp;</td>
                          <td height="10">&nbsp;</td>
                          <td height="10">&nbsp;</td>
                          <td height="10">&nbsp;</td>
                        </tr>
                        <tr>
                          <td height="10" colspan="2">                          
                            
                            <button type="submit" class="btnnegro" style="border:0px; cursor:pointer;"> <i class="fa fa-save"></i> Guardar </button>
                            <button type="button" onclick="window.location.href='resumendiario.php';" class="btnnegro" style="border:0px; cursor:pointer;"> <i class="fa fa-remove"></i> Cancelar </button>                    
                            
                          </td>
                          <td width="221" height="10">&nbsp;</td>
                          <td width="221" height="10">&nbsp;</td>
                        </tr>
                      </tbody>
                    </table>
                  </form>
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
<blockquote>&nbsp;	</blockquote>

</body>
</html>
<?php include ("footer.php") ?>
<script src="../vendor/jossmp/sunatphp/example/js/ajaxview.js"></script>
<script type="text/javascript">
  $(function(){
    
  })
</script>




