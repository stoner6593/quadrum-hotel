<?php
session_start();
include "validar.php";
include "config.php";
include "include/functions.php";
date_default_timezone_set('America/Lima');
$acentos = $mysqli->query("SET NAMES 'utf8'");

$txtbuscarpor = @$_POST['txtbuscarpor'];
$txtdato = @$_POST['txtdato'];

$sql = "select 
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
	
	preciodiariouno,
	preciohorauno,
	preciohoraadicionaluno,
	preciohuespedadicionaluno,
	
	hab_tipo.preciodiariodos,
	hab_tipo.preciohorados,
	hab_tipo.preciohoraadicionaldos,
	hab_tipo.preciohuespedadicionaldos,
	hab_venta.ubicacion,
	
	hab_venta.preciodiariovs,
	hab_venta.preciohorasvs,
	
	costopersonaadicional,
	costohoraadicional,
  precio12,
  preciobooking,
  precioreservaweb
	
	from hab_venta inner join hab_tipo on hab_tipo.idtipo = hab_venta.idtipo
					left join hab_estado on hab_estado.idestado = hab_venta.idestado";


if($txtbuscarpor == 1 && $txtdato != ""){
	
	$sqlhabitacion = $mysqli->query($sql." 
	where hab_tipo.nombre regexp '$txtdato|$txtdato.'
	order by hab_venta.numero asc");
	
}else if($txtbuscarpor == 2){
	
	$sqlhabitacion = $mysqli->query($sql." 
	where hab_venta.numero  = '$txtdato'");
	
} else {
	$sqlhabitacion = $mysqli->query($sql." 
	order by hab_venta.numero asc");
}
	
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Administrador</title>

<?php include "head-include.php"; ?>

</head>

<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0">

    <tr>
      <td height="25" colspan="3"><?php include "head.php"; ?></td>
    </tr>
    <tr>
      <td width="185" height="25" align="left" valign="top"><?php include "menu_nav.php"; ?></td>
      <td width="25">&nbsp;</td>
      <td width="793" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
       
          <tr>
            <td width="330" height="30"> <h3 style="color:#E1583E;"> <i class="fa fa-hotel"></i> Habitaciones </h3></td>
            <td width="463" align="center"> 

            <button onclick="window.location.href = 'habitaciones-editor.php';" class="btngris" style="border:0px; cursor:pointer;"> <i class="fa fa-plus-circle"></i> Nueva Habitación</button>
            <button onclick="window.location.href = 'habitaciones-tipo.php';" class="btngris" style="border:0px; cursor:pointer;"> <i class="fa fa-plus-circle"></i> Tipo de Habitación y Tarifas</button>
            </td>
          </tr>
          <tr>
            <td height="30" colspan="2"><table width="99%" border="0" cellpadding="1" cellspacing="1">
             
                <tr>
                  <td height="30"><div class="lineahorizontal" style="background:#BFBFBF;"></div>
                  
                  
                  
                  </td>
                </tr>
                <tr>
                  <td height="30"><form id="form1" name="form1" method="post">
                    <table width="100%" border="0" cellpadding="1" cellspacing="1">

                        <tr>
                          <td width="17%">
                          <input name="txtbuscarpor" type="radio" id="rdtipo" value="1" checked="checked">  <label for="rdtipo" class="textoContenidoMenor"> Tipo </label>   
                          <input name="txtbuscarpor" type="radio" id="rdnumero" value="2">  <label for="rdnumero" class="textoContenidoMenor"> Número </label></td>
                          <td width="51%"><input name="txtdato" type="text" class="textbox" id="txtdato" placeholder="Ingrese el dato a buscar"></td>
                          <td width="32%">
                          <button type="submit" class="btnnegro" style="border:0px; cursor:pointer;"> <i class="fa fa-search-plus"></i> Buscar </button> 
                          </td>
                        </tr>

                    </table>
                  </form></td>
                </tr>
                <tr>
                  <td height="20">
                  
                  <?php if (isset($_SESSION['msgerror'])){ ?>
                  <div class="alert alert-success alert-dismissable textoContenidoMenor">
                  	<?php echo $_SESSION['msgerror'];$_SESSION['msgerror']="";?> 
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                  </div>
                  <?php } ?>
                  
                  </td>
              </tr>
                <tr>
                  <td height="30"><table width="100%" border="0" cellpadding="4" cellspacing="1" bgcolor="#E0E0E0">
                    
                      <tr class="textoContenidoMenor">
                        <td width="35" height="25" align="center" bgcolor="#F4F4F4">#</td>
                        <td width="40" height="25" bgcolor="#F4F4F4">Piso</td>
                        <td width="55" height="25" bgcolor="#F4F4F4">Número</td>
                        <td width="109" height="25" bgcolor="#F4F4F4">Tipo</td>
                        <td width="80" height="25" align="center" bgcolor="#DFF0D8">Precio Diario Dom-Jue (S/)</td>
                        <td width="80" height="25" align="center" bgcolor="#DFF0D8">Precio 6 Horas Dom-Jue (S/)</td>
                        <td width="80" align="center" bgcolor="#FFEEEE">Precio Diario Vie-Sab (S/)</td>
                        <td width="80" align="center" bgcolor="#FFEEEE">Precio 6 Horas Vie-Sab (S/)</td>
                        <td width="80" align="center" bgcolor="#FFEEEE">Persona Adicional</td>
                        <td width="80" align="center" bgcolor="#FFEEEE">Precio Reserva Web (S/)</td>
                        <td width="80" height="25" align="center" bgcolor="#F4F4F4">Precio Booking (S/)</td>
                        <td width="80" bgcolor="#F4F4F4">Hora Adicional</td>
                        <td width="80" bgcolor="#F4F4F4">12 Horas</td>
                        <td width="48" align="center" bgcolor="#F4F4F4">Ubi</td>
<!--                        <td width="29" bgcolor="#F4F4F4" align="center">Est</td>-->
                        <td width="43" bgcolor="#F4F4F4" align="center">Edit</td>
                    </tr>
                      
                    <?php
						$item = 0;
						while ($xhFila = $sqlhabitacion->fetch_row())
						{		
						$item++;
					?>
                      <tr class="textoContenidoMenor">
                        <td height="35" align="center" bgcolor="#FFFFFF"><span style="color:#BEBEBE;"><?php echo $item; ?></span></td>
                        <td height="35" align="center" bgcolor="#FFFFFF"><?php echo $xhFila['3']; ?></td>
                        <td height="35" bgcolor="#F6EDF7" align="center"><strong class="textoContenido" style="color:#<?php echo $xhFila['13']; ?>"><?php echo $xhFila['4']; ?></strong></td>
                        <td height="35" bgcolor="#FFFFFF"><?php echo $xhFila['10']; ?></td>
                        <td width="80" height="35" align="right" bgcolor="#DFF0D8"><?php echo number_format($xhFila['5'],2); ?></td>
                        <td width="80" height="35" align="right" bgcolor="#DFF0D8"><?php echo number_format($xhFila['6'],2); ?></td>
                        <td width="80" height="35" align="right" bgcolor="#FFEEEE"><?php echo number_format($xhFila['23'],2); ?></td>
                        <td width="80" height="35" align="right" bgcolor="#FFEEEE"><?php echo number_format($xhFila['24'],2); ?></td>
                        <td width="80" height="35" align="right" bgcolor="#FFFFFF"><?php echo number_format($xhFila['25'],2); ?></td>

                        <td width="80" height="35" align="right" bgcolor="#FFFFFF"><?php echo number_format($xhFila['28'],2); ?></td>
                        <td width="80" height="35" align="right" bgcolor="#FFFFFF"><?php echo number_format($xhFila['29'],2); ?></td>

                        <td width="80" height="35" align="right" bgcolor="#FFFFFF"><?php echo number_format($xhFila['26'],2); ?></td>
                        <td width="80" height="35" align="right" bgcolor="#FFFFFF"><?php echo number_format($xhFila['27'],2); ?></td>
                        <td height="35" align="center" bgcolor="#FFFFFF"><?php echo  habitacionUbicacion($xhFila['22']); ?></td>
<!--                        <td height="35" align="center" bgcolor="#FFFFFF">-->
<!--                        -->
<!--                        <button type="button" class="btnestado" style="border:0px; cursor:pointer; background:#--><?php //echo $xhFila['13']; ?><!--"> <i class="fa fa-angle-up"></i> </button>-->
<!--                        -->
<!--                        </td>-->
                        <td height="35" align="center" bgcolor="#FFFFFF">
                        <button type="button" onclick="window.location.href='habitaciones-editor.php?idprimario=<?php echo $xhFila['0'].'&estado=modifica';?>';" class="btnmodificar" style="border:0px; cursor:pointer;"> <i class="fa fa-edit"></i> </button>
                        </td>
                      </tr>
                      
                    <?php
						}
						$sqlhabitacion->free();
					?>
                    
                  </table></td>
                </tr>
                <tr>
                  <td height="30">&nbsp;</td>
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
</body>
</html>
<?php include ("footer.php") ?>




