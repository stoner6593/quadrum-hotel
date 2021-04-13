<?php
//require_once "../init.php"; echo APP_RUCEMISOR; exit;
session_start();
include "validar.php";
include "config.php";
include "include/functions.php";
date_default_timezone_set('America/Lima');
$xusuarioingresado = $_SESSION['xyzidusuario'];
$nombreusuario = $_SESSION['xyznombre'].' ('.$_SESSION['xyzusuario'].') ';

 $sqlusuario = $mysqli->query("select
 	user_id,
	user_user,
	user_nombre,
	user_categoria
	from usuario where user_id = '$xusuarioingresado'
 	");
	$uFila = $sqlusuario->fetch_row();

//echo $xusuarioingresado ;
$sqlconsulta = $mysqli->query("select
	ingresosturno.idturno,
	ingresosturno.idusuario,
	ingresosturno.estadoturno,

	usuario.user_id,
	usuario.user_user,
	usuario.user_nombre,
	usuario.user_categoria

	from ingresosturno inner join usuario on usuario.user_id = ingresosturno.idusuario
	where ingresosturno.estadoturno = 1");

	$numero = $sqlconsulta->num_rows;
	//echo $numero."<br><br>";

	$xuFila = $sqlconsulta->fetch_row();



	$xidusuario = $xuFila['3'];
	$xusuario = $xuFila['4'];
	$xusuarionombre = $xuFila['5'];


	$xidturno = $xuFila['0'];
	$_SESSION['idturno'] = $xuFila['0'];

	$abrirturno = 0;

	if ($numero==1){

		if($xidusuario <> $xusuarioingresado){
			$msg = "Hay un turno abierto con el usuario <br> <strong>".$xusuarionombre." (".$xusuario.")"." </strong> <br> Si requiere cambiar de usuario, antes debe cerrar el turno. <br><br>"."<a href='salir.php' class='btnrojo' style='width:100%; color:#FFFFFF; padding:20px; font-size:18px;'> Salir  </a>";
			$_SESSION['estadomenu'] = 0;
		}else{
			$msg = "Hay un turno abierto con el usuario <br> <strong>".$xusuarionombre." (".$xusuario.")"." </strong> <br> Si requiere cambiar de usuario, antes debe cerrar el turno. <br><br>"."<a href='reporte.php?xidturno=$xidturno' class='btnrojo' style='width:100%; color:#FFFFFF; padding:10px; font-size:18px;'> Cerrar Turno  </a>";
			$_SESSION['estadomenu'] = 1;
			$_SESSION['estadoturno'] = 1;
		}
	} else {
		$msg = "Para iniciar el trabajo debe abrir un Turno con el Usuario: <br> <strong>".$nombreusuario;

		$link = "</strong> <a href='include/usuario/prg_abrir-turno.php?idusuario=$xusuarioingresado' class='btnrojo' style='width:100%; color:#FFFFFF; padding:10px; font-size:18px;'> Abrir Turno </a>";

		$_SESSION['estadomenu'] = 0;
		$abrirturno = 1;
	}


	$xuscategoria = $uFila["3"];
	if($xuscategoria == 1) {
		$msg = "Ha ingresado como Administrador. <br><br>"."Hay un turno abierto con el usuario <br> <strong>".$xusuarionombre." (".$xusuario.")"." </strong> <br><br> Le recomendamos no generar ordenes de Producto ni alquiler de habitaciones cuando está trabajando un Turno.";
		$_SESSION['estadomenu'] = 1;
	}


?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Administrador</title>

<?php include "head-include.php"; ?>

<script>

	function validarDatos() {
		var Lstturno = parseInt(document.form1.txtturno.value);

		if (Lstturno == 0 ) {
		alert("Seleccione el Turno a trabajar.");
		document.form1.txtturno.focus();
		return false
		}

		return true;
	}

</script>

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
      <td width="1125" valign="top">
        <h3 style="color:#E1583E;"> <i class="fa fa-list"></i> Series </h3>
        <hr>
        <table class="table table-hover" width="100%" style="border:1px solid #000000; padding:5px;">
          <tr style="border:1px solid #eaeaea;">
            <th>ID</th>
            <th>Cod Sunat</th>
            <th>Descripción</th>
            <th>Serie</th>
            <th>Numeración</th>
            <th>Estado</th>
            <th>Guardar</th>
          </tr>
        <?php
        if(isset($_GET["numeracion"])){
          if($mysqli->query("
            UPDATE series SET numeracion = '".$_GET["numeracion"]."' WHERE iddocumento = ".$_GET["iddocumento"]."
          ")){
            echo "<p style='color:#00FF00;'>Actualización exitosa</p>";
          }else{
            echo "<p style='color:#FF0000;'>Error</p>";            
          }
        }

        $sql = $mysqli->query("Select * from series");
        while ($row = $sql->fetch_assoc()) { ?>
          <tr style="border:1px solid #000000; padding:5px; text-align:center;">
            <td><?php echo $row["iddocumento"]; ?></td>
            <td><?php echo $row["codsunat"]; ?></td>
            <td><?php echo $row["descripcion"]; ?></td>
            <td><?php echo $row["serie"]; ?></td>
            <td>
              <input type="number" name="numeracion<?php echo $row["iddocumento"]; ?>" id="numeracion<?php echo $row["iddocumento"]; ?>" value="<?php echo $row["numeracion"]; ?>" style="text-align:right;" />
            </td>
            <td><?php echo $row["estado"]; ?></td>
            <td><button onclick="location.href='series.php?numeracion='+$('#numeracion<?php echo $row["iddocumento"]; ?>').val()+'&iddocumento='+<?php echo $row["iddocumento"]; ?>;" type="button" class="btn btn-primary"><span class="fa fa-save"></span></button></td>
          </tr>
        <?php } ?>
        </table>
      </td>
    </tr>
    <tr>
      <td height="25" colspan="3">&nbsp;</td>
    </tr>
  </tbody>
</table>
<?php include "footer.php"; ?>
<script>
    $(function(){
        $('#enviar').click(function (e) {

            var Lstturno = parseInt(document.form1.txtturno.value);

            if (Lstturno == 0 ) {
                alert("Seleccione el Turno a trabajar.");
                document.form1.txtturno.focus();
                return false
            }else{
                //$(this).prop('disabled', true);
                $("#loading").show();
                $(this).hide();
                return true;
            }
        });
    });
</script>
</body>
</html>
