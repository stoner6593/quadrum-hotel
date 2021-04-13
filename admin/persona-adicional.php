<?php
session_start();
include "validar.php";
include "include/functions.php";

include "config.php";
$nombrecliente = $_GET['nombrecliente'];
$idhabitacion = $_GET['idhabitacion'];
$idalquiler = $_GET['idalquiler'];
$idcliente = $_GET['idcliente'];

//echo $idalquiler;

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
	where idalquiler = '$idalquiler'
	order by idpersona asc");

$sqltipoAdic = $mysqli->query("SELECT * FROM al_tipo_peradicional where tipoperadic_estado=1 order by id_tipo");
//$tFila = $sqltipoAdic->fetch_row();

$sqltipoIdef = $mysqli->query("SELECT * FROM tipo_identificacion WHERE mostrar = 1 order by identificacion");

$idpersonaR      = "";
$idalquilerR     = "";
$idclienteR      = "";
$nombreR         = "";
$dniR            = "";
$nacimientoR     = "";
$id_tipoR        = "";
$fecha_registroR = "";
$tipo_docR       = "";
$sexoR           = "";
$nacionalidadR   = "";

if(isset($_GET["nombreAd"])){
  $nombreAd = $_GET["nombreAd"];
  $dniAd    = $_GET["dniAd"];

  $result = $mysqli->query("
    select * from al_venta_personaadicional 
    where nombre='".$nombreAd."' and dni='".$dniAd."'");

  $reg = $result->fetch_assoc();

  $idpersonaR      = $reg["idpersona"];
  $idalquilerR     = $reg["idalquiler"];
  $idclienteR      = $reg["idcliente"];
  $nombreR         = $reg["nombre"];    
  $dniR            = $reg["dni"];
  $nacimientoR     = $reg["nacimiento"];
  $id_tipoR        = $reg["id_tipo"];
  $fecha_registroR = $reg["fecha_registro"];
  $tipo_docR       = $reg["tipo_doc"];
  $sexoR           = $reg["sexo"];
  $nacionalidadR   = $reg["nacionalidad"];

  $arrNacR = explode("-", $nacimientoR);

  $nacimientoR = $arrNacR[2]."/".$arrNacR[1]."/".$arrNacR[0];
}

?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Añadir datos de huespedes adicionales</title>
<?php include "head-include.php"; ?>

<script type="text/javascript">
  window.onload = function(){
    $("#btnBuscar").click(function(){
      var nomPersonaBus = $("#txtbusqueda").val();
      window.open("persona-adicionales-encontradas.php?nom="+nomPersonaBus,"modelo","width=1000, height=350, scrollbars=yes" );
    });
  }
</script>

</head>
<!--<body OnLoad="form1.txtnombre.focus()">-->
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="100%" height="25" valign="middle" bgcolor="#FFFFFF">

    <form name="form1" method="post" action="include/alquiler/prg_persona-adicional.php?idalquiler=<?php echo $idalquiler.'&idhabitacion='.$idhabitacion.'&nombrecliente='.$nombrecliente.'&idcliente='.$idcliente;?>">
<h3>Personas Adicionales</h3>
      <table width="100%" border="0" cellpadding="1" cellspacing="1">
        <tr>
          <td class="textoContenidoMenor">Nombre a buscar</td>
        </tr>
        <tr>
          <td colspan="4">
            <input name="txtbusqueda" type="text" class="textbox" id="txtbusqueda">
          </td>
          <td>
            <button type="button" class="btnnegro" id="btnBuscar" style="border:0px; cursor:pointer;">
            <i class="fa fa-search"></i> Buscar </button>
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="textoContenidoMenor">Tipo Huesped Adicional</td>
          <td class="textoContenidoMenor">Nombres y Apellidos</td>
					<td class="textoContenidoMenor">Tipo Doc.</td>
          <td class="textoContenidoMenor">Num. documento</td>
        </tr>
        <tr>
            <td>
                <select name='cboTipoHuesped' id='cboTipoHuesped' class='form-control' >
                <?php
                while ($xtFila = $sqltipoAdic->fetch_row()){
                        echo 
                  "<option value='".$xtFila['0']."' ".($id_tipoR==$xtFila['0'] ? "selected" : "").">".$xtFila['1']."</option>";
                }
                $sqltipoAdic->free();
                ?>
                </select>
            </td>
          <td>
              <input name="txtnombre" type="text" class="textbox" id="txtnombre" 
              value="<?php echo $nombreR; ?>">
          </td>
					<td>
						<select name='txttipodoc' id='txttipodoc'>
						<?php
						while ($xtFila = $sqltipoIdef->fetch_row()){
										echo 
              "<option value='".$xtFila['0']."' ".($tipo_docR==$xtFila['0'] ? "selected" : "").">".$xtFila['1']."</option>";
						}
						 $sqltipoIdef->free();
						?>
						</select>
          </td>
          <td>
              <input name="txtdni" type="text" class="textbox" id="txtdni" value="<?php echo $dniR; ?>">
          </td>

          <td>
              <button type="submit" class="btnnegro" style="border:0px; cursor:pointer;"> <i class="fa fa-save"></i> Guardar </button>
          </td>
        </tr>
				<tr>
					<td class="textoContenidoMenor">Fecha de Nacimiento</td>
					<td class="textoContenidoMenor">Sexo</td>
					<td class="textoContenidoMenor">Nacionalidad</td>
				</tr>
				<tr>
					<td>
                <input name="txtnacimiento" type="text" class="textbox" id="txtnacimiento" placeholder="DD/MM/AAAA" value="<?php echo $nacimientoR; ?>">
                <input name="mando" type="hidden" id="mando" value="si">
          </td>
					<td>
						<select name="sexo" id="sexo">
							<option value="M" <?php echo $sexoR=="M"?"selected":""; ?>>M</option>
							<option value="F" <?php echo $sexoR=="F"?"selected":""; ?>>F</option>
						</select>
          </td>
					<td>
                <input name="txtnacionalidad" type="text" class="textbox" id="txtnacionalidad" 
                value="<?php echo $nacionalidadR; ?>">

          </td>
				</tr>
      </table>
			<br/>
    </form>
    </td>
  </tr>
  <tr>
    <td height="77" valign="top" bgcolor="#FFFFFF">
	<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#F0F0F0">
        <tr class="textoContenidoMenor">
          <td width="5%" height="25" bgcolor="#F4F4F4" ><div align="center"><strong>#</strong></div></td>
            <td width="5%" height="25" bgcolor="#F4F4F4" ><div align="center"><strong>Tipo</strong></div></td>
          <td width="38%" height="25" align="left" valign="middle" bgcolor="#F4F4F4" ><div align="left">Nombres y Apellidos</div></td>
          <td width="20%" align="left" valign="middle" bgcolor="#F4F4F4" ><div align="left">DNI </div></td>
          <td width="11%" align="left" valign="middle" bgcolor="#F4F4F4" ><div align="left">Sexo </div></td>
          <td width="15%" height="25" align="left" valign="middle" bgcolor="#F4F4F4" ><div align="center">Fecha de Nacimiento</div></td>
          <td width="11%" height="25" bgcolor="#F4F4F4" ><div align="center"></div></td>
        </tr>
	  <?php
	$suma =0;
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
          <td height="25" bgcolor="#FFFFFF" class="textoContenidoNegro">
              <div align="center">
                    <a href="#" onclick='entregar(<? echo $aFila['0'];?> , "<?php echo $aFila['1']; ?>")' class="btnestado"> <i class="fa fa-check"></i> </a>
                </div>
          </td>
        </tr>
<?php
}
$sqladicional->free();
$mysqli->close()
?>
    </table></td>
  </tr>
  <tr>
    <td height="19" valign="top" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
</table>
</body>
</html>
