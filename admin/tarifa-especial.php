<?php
include "validar.php";
include "config.php";
include "include/functions.php";
date_default_timezone_set('America/Lima');

$txtdato = @$_POST['txtdato'];

if($txtdato==""){
	$sqlproducto = $mysqli->query("SELECT a.id_tarifa,
    a.descripcion_tarifa,
    a.fecha_tarifa,
    a.idtipo,
    a.precio_dia,
    a.precio_hora_1,
    a.precio_hora_2,
    a.precio_hora_adicional,
    a.precio_huesped_adicional,
    a.estado_tarifa,
    a.fecha_registro,
    b.nombre
FROM tarifa_especial a 
inner join hab_tipo b on b.idtipo=a.idtipo order by a.fecha_tarifa desc");
}else{
	$sqlproducto = $mysqli->query("SELECT a.id_tarifa,
    a.descripcion_tarifa,
    a.fecha_tarifa,
    a.idtipo,
    a.precio_dia,
    a.precio_hora_1,
    a.precio_hora_2,
    a.precio_hora_adicional,
    a.precio_huesped_adicional,
    a.estado_tarifa,
    a.fecha_registro,
    b.nombre
FROM tarifa_especial a 
inner join hab_tipo b on b.idtipo=a.idtipo
	where a.descripcion_tarifa regexp '$txtdato|$txtdato.'
	order by a.fecha_tarifa asc");
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
      <td height="25" colspan="3"><?php include ("head.php"); ?></td>
    </tr>
    <tr>
      <td width="185" height="25" align="left" valign="top"><?php include ("menu_nav.php"); ?></td>
      <td width="25">&nbsp;</td>
      <td width="793" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
       
          <tr>
            <td width="729" height="30"> <h3> <i class="fa fa-users"></i> Tarifas Especiales </h3></td>
            <td width="175" align="center"> 

            <button onclick="window.location.href = 'tarifa-especial-editor.php';" class="btngris" style="border:0px; cursor:pointer;"> <i class="fa fa-plus-circle"></i> Nuevo </button>
            
            </td>
          </tr>
          <tr>
            <td height="30" colspan="2"><table width="100%" border="0" cellpadding="1" cellspacing="1">
             
                <tr>
                  <td height="30"><div class="lineahorizontal" style="background:#EFEFEF;"></div></td>
                </tr>
                <tr>
                  <td height="30">
                      <form id="form1" name="form1" method="post">
                    <table width="100%" border="0" cellpadding="1" cellspacing="1">

                        <tr>
                          <td width="65%"><input name="txtdato" type="text" class="textbox" id="txtdato" placeholder="Ingrese la descripción de la tarifa"></td>
                          <td width="35%">
                          <button type="submit" class="btnnegro" style="border:0px; cursor:pointer;"> <i class="fa fa-search-plus"></i> Buscar </button> 
                          </td>
                        </tr>

                    </table>
                  </form>
                  </td>
                </tr>
                <tr>
                  <td height="10">
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
                        <td width="20" height="25" bgcolor="#F4F4F4">ID</td>
                        <td width="220" height="25" bgcolor="#F4F4F4">Descripción</td>
                        <td width="50" height="25" align="center" bgcolor="#F4F4F4">Fecha</td>
                        <td width="109" height="25" align="center" bgcolor="#F4F4F4">Tipo Habitación</td>
                        <td width="40" bgcolor="#F4F4F4">Precio Día (S/)</td>
                          <td width="40" bgcolor="#F4F4F4">Precio Hora Especial 1 (S/)</td>
                          <td width="40" bgcolor="#F4F4F4">Precio Hora Especial 2 (S/)</td>
                          <td width="40" bgcolor="#F4F4F4">Precio Hora Adic. (S/)</td>
                          <td width="40" bgcolor="#F4F4F4">Precio Huesped Adic. (S/)</td>
                        <td width="39" align="center" bgcolor="#F4F4F4">Est</td>
                        <td width="43" align="center" bgcolor="#F4F4F4">Edit</td>
                      </tr>
                      
                      <?php 
					  $num = 0;
					  while($xpFila = $sqlproducto->fetch_row()) { 
					  $num++;
					  ?>
                      <tr class="textoContenidoMenor">
                        <td height="25" align="center" bgcolor="#FFFFFF"><?php echo $xpFila['0'];?></td>
                        <td height="25" bgcolor="#FFFFFF"><?php echo $xpFila['1'];?></td>
                          <td height="25" bgcolor="#FFFFFF"><?php echo $xpFila['2'];?></td>
                        <td height="25" bgcolor="#FFFFFF"><?php echo $xpFila['11'];?></td>
                        <td height="25" align="center" bgcolor="#FFFFFF"><?php echo $xpFila['4'];?></td>
                        <td height="25" align="right" bgcolor="#FFFFFF"><?php echo $xpFila['5'];?></td>
                          <td height="25" align="right" bgcolor="#FFFFFF"><?php echo $xpFila['6'];?></td>
                          <td height="25" align="right" bgcolor="#FFFFFF"><?php echo $xpFila['7'];?></td>
                          <td height="25" align="right" bgcolor="#FFFFFF"><?php echo $xpFila['8'];?></td>
                        <td height="25" bgcolor="#FFFFFF"><?php echo $xpFila['9'];?></td>
                        <td align="center" bgcolor="#FFFFFF"><button type="button" onclick="window.location.href='tarifa-especial-editor.php?idprimario=<?php echo $xpFila['0'].'&estado=modifica';?>';" class="btnmodificar tooltip" tooltip="Modificar" style="border:0px; cursor:pointer;"> <i class="fa fa-edit"></i></button></td>
                      </tr>
                    
                    <?php
					  }
					  $sqlproducto->free();
					?>  
                    
                  </table></td>
                </tr>
                <tr>
                  <td height="30">&nbsp;</td>
                  </tr>
                <tr>
                  <td height="30">&nbsp;</td>
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