<?php
include "validar.php";
include "config.php";
include "include/functions.php";
date_default_timezone_set('America/Lima');

//Cuando viene desde control mantenimiento
$idalquiler = $_GET['idalquiler'];
$idhabitacion = $_GET['idhabitacion'];

  $sqlalquiler = $mysqli->query("select 
	hab_venta.idhabitacion,
	hab_venta.idtipo,
	hab_venta.idestado,
	hab_venta.piso,
	hab_venta.numero,
	hab_venta.preciodiariodj,
	hab_venta.preciohorasdj,
	hab_venta.nrohuespedes,
	hab_venta.caracteristicas,
	
	hab_tipo.idtipo,
	hab_tipo.nombre,
	hab_estado.idestado,
	hab_estado.estado,
	hab_estado.color,
	
	hab_venta.idalquiler,
	hab_venta.nroadicional,
	hab_venta.ubicacion,
	
	hab_venta.preciodiariovs,
	hab_venta.preciohorasvs,

  DATE_FORMAT(hab_venta.ultimocambio, '%d/%m/%Y %H:%i')
	
	from hab_venta inner join hab_tipo on hab_tipo.idtipo = hab_venta.idtipo
					inner join hab_estado on hab_estado.idestado = hab_venta.idestado
					where hab_venta.idhabitacion='$idhabitacion'
	order by hab_venta.idhabitacion asc");

  $xhFila = $sqlalquiler->fetch_row();

    $sqlemp = $mysqli->query("SELECT * FROM empleado where emp_estado = 1 order by emp_apaterno");
    //$empFila = $sqlemp->fetch_row();

    $sqltipos = $mysqli->query("SELECT * FROM hab_mantenimiento_tipo order by tipo");
	
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Registro Mantenimiento</title>

<?php  include "head-include.php"; ?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />

    <!-- Datetimerpicker-->
    <link rel="stylesheet" type="text/css" href="datetimepicker/jquery.datetimepicker.css"/>
    <script type="text/javascript" src="datetimepicker/jquery.datetimepicker.js"></script>

</head>
<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0">

    <tr>
      <td height="25" colspan="3">
          <?php include ("head.php"); ?>
      </td>
    </tr>
    <tr>
      <td width="185" height="25" align="left" valign="top"><?php include ("menu_nav.php"); ?></td>
      <td width="25">&nbsp;</td>
      <td width="793" valign="top">
          <table width="100%" border="0" cellpadding="0" cellspacing="0">
       
          <tr>
            <td width="638" height="30"> <h3 style="color:#E1583E;"> <i class="fa fa-users"></i> Alquiler / Mantenimiento</h3></td>
            <td width="155" align="center">
                <button type="button" onclick="window.location.href='control-mantenimiento.php';"
                                                    class="btngris" style="border:0px; cursor:pointer;"> <i class="fa fa-arrow-left"></i> Volver </button>
            </td>
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
                    <form id="form1" name="form1" method="post" action="include/mantenimiento/prg_mantenimiento.php">
                    <table width="99%" border="0" cellpadding="1" cellspacing="1">
                      <tbody>
                        <tr>
                            <td width="221" height="30"><span class="textoContenido">Habitación:</span></td>
                            <td height="30">
                              <?php echo $xhFila['4']?>
                                <input name="txtidalquiler" type="hidden" id="txtidalquiler" value="<?php echo $idalquiler; ?>">
                                <input name="txtidhabitacion" type="hidden" id="txtidhabitacion" value="<?php echo $idhabitacion; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td height="30"><span class="textoContenido"># Piso:</span></td>
                            <td height="30">
                              <?php echo $xhFila['3']?>
                            </td>
                        </tr>
                        <tr>
                            <td height="30"><span class="textoContenido">Tipo:</span></td>
                            <td height="30">
                              <?php echo $xhFila['10']?>
                            </td>
                        </tr>
                        <tr>
                            <td height="30"><span class="textoContenido">Personal Mantenimiento:</span></td>
                            <td height="30">
                                <select name="cmbEmpleado" id="cmbEmpleado" class="select">
                                    <option value="">Seleccione un empleado</option>
                                    <?php while ($tmpSeries = $sqlemp->fetch_row()){
                                        $empNombreCompleto = $tmpSeries[1]." ".$tmpSeries[2].", ".$tmpSeries[3];
                                        ?>
                                        <option value="<?php echo $tmpSeries[0];?>"><strong><?php echo $empNombreCompleto;?></strong></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td height="30"><span class="textoContenido">Fecha/Hora Inicio:</span></td>
                            <td height="30">
                                <input name="txtFechaInicio" type="text" id="txtFechaInicio" class="textbox" style="width:45%;" value="<?php echo $xhFila['19']?>"
                                       placeholder="Seleccione Fecha/Hora Inicio" readonly="true">
                            </td>
                        </tr>
                        <tr>
                          <td height="30"><span class="textoContenido">Tipo mantenimiento:</span></td>
                          <td height="30">
                                <select name="cmbTipoM" id="cmbTipoM" class="select">
                                    <option value="">Seleccione tipo</option>
                                    <?php while ($tmpSeries = $sqltipos->fetch_row()){
                                        
                                        ?>
                                        <option value="<?php echo $tmpSeries[0];?>">
                                          <strong><?php echo $tmpSeries[1];?></strong>
                                        </option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <!--<tr>
                            <td height="30"><span class="textoContenido">Fecha/Hora Fin:</span></td>
                            <td height="30">
                                <input name="txtFechaFin" type="text" id="txtFechaFin" class="textbox" style="width:45%;"
                                       placeholder="Seleccione Fecha/Hora Fin" readonly="true">
                            </td>
                        </tr>-->
                        <tr>
                          <td height="10"><span class="textoContenido">Observaciones:</span></td>
                          <td height="10">&nbsp;</td>
                          <td height="10">&nbsp;</td>
                          <td height="10">&nbsp;</td>
                        </tr>
                        <tr>
                          <td height="10" colspan="4"><textarea name="txtobs" class="form-control" id="txtobs" style="width:70%"></textarea>
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
                            
                            <button type="submit" class="btnrojo" style="border:0px; cursor:pointer;"> <i class="fa fa-save"></i> Guardar </button>
                            <button type="button" onclick="window.location.href='control-mantenimiento.php';" class="btnnegro" style="border:0px; cursor:pointer;"> <i class="fa fa-remove"></i> Cancelar </button>
                            
                          </td>
                          <td width="221" height="10">&nbsp;</td>
                          <td width="221" height="10">&nbsp;</td>
                        </tr>
                      </tbody>
                    </table>
                  </form>
                </td>
                </tr>
             
            </table>
            </td>
            </tr>
  
      </table>
      </td>
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
<script>
    jQuery(document).ready(function($) {
        /*$('#txtFechaInicio').datetimepicker({
            //mask:'9999/19/39 29:59',
            lang:'es',
            hours12: false,
            format: 'd/m/Y H:i',
            step: 5,
            opened: false,
            validateOnBlur: false,
            closeOnDateSelect: false,
            closeOnTimeSelect: false,
            minDate:'today',
            //minTime:'8:30',
            //maxTime:'21:00',
            dayOfWeekStart: 1,
            maxDate:'+1970/01/01'//
        });*/
        $('#txtFechaFin').datetimepicker({
            //mask:'9999/19/39 29:59',
            lang:'es',
            hours12: false,
            format: 'd/m/Y H:i',
            step: 5,
            opened: false,
            validateOnBlur: false,
            closeOnDateSelect: false,
            closeOnTimeSelect: false,
            minDate:'today',
            //minTime:'8:30',
            //maxTime:'21:00',
            dayOfWeekStart: 1,
            maxDate:'+1970/01/01'//
        });
    });
</script>




