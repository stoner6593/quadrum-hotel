<?php
include "validar.php";
include "config.php";
include "include/functions.php";
date_default_timezone_set('America/Lima');

$xidusuario = @$_GET['xidusuario'];
$xestado = @$_GET['estado'];

$sqlusuario = $mysqli->query("SELECT a.idempleado,
        a.emp_apaterno,
        a.emp_amaterno,
        a.emp_nombres,
        a.id_doc_identidad,
        a.nro_doc_identidad,
        a.emp_sexo,
        a.emp_email,
        a.cargo_id,
        a.emp_estado,
        c.cargo_descrip
    FROM empleado a
    inner join empleado_cargo c on c.cargo_id = a.cargo_id
    order by a.emp_apaterno");

if($xestado=='modifica'){
	$sqlusuariomod = $mysqli->query("SELECT a.idempleado,
        a.emp_apaterno,
        a.emp_amaterno,
        a.emp_nombres,
        a.id_doc_identidad,
        a.nro_doc_identidad,
        a.emp_sexo,
        a.emp_email,
        a.cargo_id,
        a.emp_estado,
        c.cargo_descrip
    FROM empleado a
    inner join empleado_cargo c on c.cargo_id = a.cargo_id where idempleado = '$xidusuario'");
	$usFila = $sqlusuariomod->fetch_row();
}

$sqlCargo = $mysqli->query("SELECT cargo_id,
    cargo_descrip,
    cargo_estado
FROM empleado_cargo where cargo_estado = 1 order by cargo_descrip");

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Empleado</title>

<?php include "head-include.php"; ?>

</head>
<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
      <td height="25" colspan="3"><?php include ("head.php"); ?></td>
    </tr>
    <tr>
      <td width="230" height="25" align="left" valign="top"><?php include ("menu_nav.php"); ?></td>
      <td width="21">&nbsp;</td>
      <td width="1125" valign="top"><table width="850" border="0" cellpadding="0" cellspacing="0">
        <tbody>
          <tr>
            <td width="610" height="30"> <h3> <i class="fa fa-user"></i> Empleados </h3>
                <div class="lineahorizontal" style="background:#EFEFEF;"></div> </td>
            <td width="240">&nbsp;</td>
          </tr>
          <tr>
            <td height="30" colspan="2"><table width="850" border="0" cellpadding="0" cellspacing="0">
              <tbody>
                <tr>
                  <td height="10" colspan="4">
                  <form id="form1" name="form1" method="post" action="<?php if($xestado=='modifica'){echo 'include/empleado/prg_empleado-modifica.php';}else{echo 'include/empleado/prg_usuario-nuevo.php';}?>">
                    <table width="850" border="0" cellpadding="0" cellspacing="0">
                      <tbody>
                        <tr>
                            <td width="221" height="30">
                                <span class="textoContenido">Tipo de Documento:  </span>
                                <input name="txtidusuario" type="hidden" id="txtidusuario" value="<?php echo $usFila['0'];?>">
                            </td>
                            <td>
                                <select name="tipo_documento" id="tipo_documento" required class="form-control">
                                    <option value="0">DOC.TRIB.NO.DOM.SIN.RUC</option>
                                    <option value="1" selected>DOC. NACIONAL DE IDENTIDAD</option>
                                    <option value="4">CARNET DE EXTRANJERIA</option>
                                    <option value="6">REG. UNICO DE CONTRIBUYENTES</option>
                                    <option value="7">PASAPORTE</option>
                                    <option value="A">CED. DIPLOMATICA DE IDENTIDAD</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td height="30"><span class="textoContenido">Documento (*)</span></td>
                            <td height="30">
                                <input name="txtdocumento" autocomplete="false" type="text" class="form-control" id="txtdocumento" required value="<?php echo @$usFila['5']?>">
                                <i class="" id="espera"></i>
                            </td>
                        </tr>
                        <tr>
                            <td height="30"><span class="textoContenido">Ap. Paterno (*)</span></td>
                            <td height="30">
                                <input name="txtApPaterno" autocomplete="false" type="text" class="form-control" id="txtApPaterno" required value="<?php echo @$usFila['1']?>">
                                <i class="" id="espera"></i>
                            </td>
                        </tr>
                        <tr>
                            <td height="30"><span class="textoContenido">Ap. Materno (*)</span></td>
                            <td height="30">
                                <input name="txtApMaterno" autocomplete="false" type="text" class="form-control" id="txtApMaterno" required value="<?php echo @$usFila['2']?>">
                                <i class="" id="espera"></i>
                            </td>
                        </tr>
                        <tr>
                            <td height="30"><span class="textoContenido">Nombres (*)</span></td>
                            <td height="30">
                                <input name="txtNombres" autocomplete="false" type="text" class="form-control" id="txtNombres" required value="<?php echo @$usFila['3']?>">
                                <i class="" id="espera"></i>
                            </td>
                        </tr>
                        <tr>
                            <td height="30"><span class="textoContenido">Sexo (*)</span></td>
                            <td height="30">
                                <select name="txtSexo" id="txtSexo">
                                        <option value="M" selected>Masculino</option>
                                        <option value="F">Femenino</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td height="30"><span class="textoContenido">Cargo (*)</span></td>
                            <td height="30">
                                <select name="txtCargo" id="txtCargo">
                                    <option value="">Seleccione el cargo</option>
                                    <?php while ($tmpSeries = $sqlCargo->fetch_row()){
                                        ?>
                                        <option value="<?php echo $tmpSeries[0];?>"><strong><?php echo $tmpSeries[1];?></strong></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td height="30"><span class="textoContenido">Email</span></td>
                            <td height="30">
                                <input name="txtEmail" autocomplete="false" type="text" class="form-control" id="txtEmail" required value="<?php echo @$usFila['7']?>">
                                <i class="" id="espera"></i>
                            </td>
                        </tr>
                        <tr>
                          <td height="30"></td>
                          <td height="30"></td>
                          <td height="30" align="center">
                              <?php if($xestado=='modifica'){ ?>
                              <button type="submit" class="btnrojo" style="border:0px; cursor:pointer;"> <i class="fa fa-save"></i> Actualizar </button>
                              <?php }else{ ?>
                              <button type="submit" class="btnrojo" style="border:0px; cursor:pointer;"> <i class="fa fa-save"></i> Guardar </button>
                              <?php } ?>

                              <a href="empleado.php" class="btnnegro" style="color:#FFFFFF;"> Cancelar </a>
                          </td>
                        </tr>
                        <tr>
                          <td height="10">&nbsp;</td>
                          <td height="10" class="textoContenidoMenor"> <?php if($xestado=='modifica'){ ?> Modificar datos del empleado. <?php } ?></td>
                          <td height="10">&nbsp;</td>
                        </tr>
                      </tbody>
                    </table>
                  </form>
                  </td>
                </tr>
                <tr>
                  <td height="10" colspan="4"><?php if (isset($_SESSION['msgerror'])){ ?>
                    <div class="alert alert-success alert-dismissable textoContenidoMenor"> <?php echo $_SESSION['msgerror'];$_SESSION['msgerror']="";?> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> </div>
                    <?php } ?></td>
                  </tr>
                <tr>
                  <td height="30" colspan="4">&nbsp;</td>
                </tr>
                <tr>
                  <td height="30" colspan="4"><table width="850" border="0" cellpadding="4" cellspacing="1" bgcolor="#E0E0E0">
                      <tr class="textoContenidoMenor">
                        <td width="51" height="25" bgcolor="#F4F4F4">&nbsp;</td>
                        <td width="239" height="25" bgcolor="#F4F4F4">Nombre Completo</td>
                        <td width="105" height="25" bgcolor="#F4F4F4">Doc. Identidad</td>
                        <td width="156" height="25" bgcolor="#F4F4F4">Email</td>
                        <td width="147" height="25" bgcolor="#F4F4F4">Cargo</td>
                        <td width="33" align="center" bgcolor="#F4F4F4">Estado</td>
                        <td width="45" align="center" bgcolor="#F4F4F4">Editar</td>
                        </tr>
                      <?php 
					  $num= 0; 
					  while($uFila = $sqlusuario->fetch_row()) {
						  $num++;
						  $nombreCompleto = $uFila['1']." ".$uFila['2'].", ".$uFila['3'];
					  ?>
                      <tr class="textoContenidoMenor">
                        <td height="25" align="center" bgcolor="#FFFFFF"><?php echo $num;?></td>
                        <td height="25" bgcolor="#FFFFFF"><?php echo $nombreCompleto;?></td>
                        <td height="25" bgcolor="#FFFFFF"><?php echo $uFila['5'];?></td>
                        <td height="25" bgcolor="#FFFFFF"><?php echo $uFila['7'];?></td>
                        <td height="25" bgcolor="#FFFFFF">
                            <?php echo $uFila['10'];?>
                        </td>
                        <td height="35" align="center" bgcolor="#FFFFFF">
                        <button type="button" class="btnestado" style="border:0px; cursor:pointer; background:#<?php echo $xhFila['13']; ?>"> <i class="fa fa-angle-up"></i></button>
                        </td>
                        <td height="35" align="center" bgcolor="#FFFFFF">
                        <?php if($_SESSION['xyztipo']=='1'):?>
                          <button type="button" onclick="window.location.href='empleado.php?xidusuario=<?php echo $uFila['0'].'&estado=modifica';?>';"
                                  class="btnmodificar" style="border:0px; cursor:pointer;"> <i class="fa fa-edit"></i></button>
                         <?php endif;?> 
                        </td>
                        </tr>
					  <?php } ?>
                  </table></td>
                  </tr>
                <tr>
                  <td width="224" height="30">&nbsp;</td>
                  <td width="188" height="30">&nbsp;</td>
                  <td width="195" height="30">&nbsp;</td>
                  <td width="195" height="30">&nbsp;</td>
                </tr>
              </tbody>
            </table></td>
            </tr>
        </tbody>
      </table></td>
    </tr>
    <tr>
      <td height="25" colspan="3"></td>
    </tr>
  </tbody>
</table>


</body>
</html>
<?php include ("footer.php") ?>




