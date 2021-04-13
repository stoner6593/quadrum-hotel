<?php
date_default_timezone_set('America/Lima');
session_start();
include "validar.php";
include "config.php";
include "include/functions.php";
date_default_timezone_set('America/Lima');

$xidturno = isset($_GET["idturno"]) ? $_GET["idturno"] : $_SESSION['idturno'];

$sqlusuarioturno = $mysqli->prepare("select 
  *
  from ingresosturno where idturno = '$xidturno'");

$sqlusuarioturno->execute(); 
$result = $sqlusuarioturno->get_result();
$result = $result->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Ajustar caja</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

	<!-- Data Picker-->
	<link rel="stylesheet" type="text/css" href="jqueryui/jquery-ui.css">
	<script src="jqueryui/jquery-ui.js"></script>
	<script src="jqueryui/jquery-1.12.4.js"></script>
	<script src="jqueryui/jquery-ui.js"></script>

	<link rel="stylesheet" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

	<!--<script type="text/javascript" src="../admin/datatable/jquery-1.12.4.js"></script>-->
<script type="text/javascript" src="../admin/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="../admin/datatable/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="../admin/datatable/buttons.flash.min.js"></script>
<script type="text/javascript" src="../admin/datatable/jszip.min.js"></script>
<script type="text/javascript" src="../admin/datatable/pdfmake.min.js"></script>
<script type="text/javascript" src="../admin/datatable/vfs_fonts.js"></script>
<script type="text/javascript" src="../admin/datatable/buttons.html5.min.js"></script>
<script type="text/javascript" src="../admin/datatable/buttons.print.min.js"></script>


	<style type="text/css">
		.nav-item a:hover{
			background-color: #E1583E;
		}

		.tit-pantalla{
			color:#E1583E;
		}

		.bg-dark{
			background-color: #515151 !important;
		}
	</style>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#btnRestablecer").click(function(){
				restablecer();
			});

			$("#totalhabitacion, #totalproducto").change(function(){
				$("#totalingreso").val((
					parseFloat($("#totalhabitacion").val()) + 
					parseFloat($("#totalproducto").val())
				).toFixed(2));

				$("#totalhabitacion").val(parseFloat($("#totalhabitacion").val()).toFixed(2));
				$("#totalproducto").val(parseFloat($("#totalproducto").val()).toFixed(2));

				verificarTotales();
			});

			$("#totalefectivo, #totalvisa").change(function(){
				$("#totalforma").val((
					parseFloat($("#totalefectivo").val()) + 
					parseFloat($("#totalvisa").val())
				).toFixed(2));

				$("#totalefectivo").val(parseFloat($("#totalefectivo").val()).toFixed(2));
				$("#totalvisa").val(parseFloat($("#totalvisa").val()).toFixed(2));

				verificarTotales();
			});

			<?php if(isset($_GET["exito"]) && $_GET["exito"] == 1){ ?>
				alert("¡Se realizó el ajuste de la caja con éxito!");
			<?php } ?>
		});

		function restablecer(){
			$("#totalhabitacion")	.val($("#totalhabitacion-h").val());
			$("#totalproducto")		.val($("#totalproducto-h").val());
			$("#totalingreso")		.val($("#totalingreso-h").val());
			$("#totalefectivo")		.val($("#totalefectivo-h").val());
			$("#totalvisa")			.val($("#totalvisa-h").val());
			$("#totalforma")		.val($("#totalforma-h").val());

			verificarTotales();
		}

		function verificarTotales(){
			if($("#totalingreso").val() == $("#totalforma").val()){
				$("#btnAplicar").attr("disabled", false);
			}else{
				$("#btnAplicar").attr("disabled", true);
			}
		}

	</script>
</head>
<body>
	<nav class="navbar navbar-expand-md bg-dark navbar-dark fixed-top">
		<a class="navbar-brand" href="#">
			<img src="../imagenesv/logohotelpalacemovil.png" alt="">
		</a>
	    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
	        <span class="navbar-toggler-icon"></span>
	    </button>
	    <div class="collapse navbar-collapse" id="collapsibleNavbar">
	        <ul class="navbar-nav w-75">
	            <li class="nav-item">
	                <a class="nav-link" href="#">
	                    Administración del Hotel
	                </a>
	            </li>
	        </ul>
	        <ul class="navbar-nav flex-row-reverse">
	        	<li class="nav-item">
	                <a class="nav-link" href="#">
	                	<span class="fa-stack" style="color:#E1583E;">
	                        <i class="fa fa-circle fa-stack-2x"></i>
	                        <i class="fa fa-user fa-stack-1x" style="color:#FFFFFF;"></i>
	                    </span>
	                    <?php echo $_SESSION['xyznombre'].' ('.$_SESSION['xyzusuario'].')'; ?> 
	                </a>
	            </li>
	        </ul>
	    </div> 
	</nav>
	<div class="row mt-5">
		<div class="col-md-2 mt-3">
			<?php include ("menu_nav.php"); ?>
		</div>
		<div class="col-md-10 mt-3">
			<div class="container mt-3">
				<h5 class="tit-pantalla">Ajustar caja</h5>
				<form action="include/caja/ajustar.php" method="post" name="frmAjustarCaja" id="frmAjustarCaja">
					<input type="hidden" name="id-turno" id="id-turno" value="<?php echo $xidturno; ?>">
					<input type="hidden" name="id-usuario" id="id-usuario" value="<?php echo $_SESSION["xyzidusuario"]; ?>">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label for="totalhabitacion">Total habitación:</label>
								<input type="hidden" name="totalhabitacion-h" id="totalhabitacion-h" value="<?php echo $result[0]["totalhabitacion"]; ?>" />
								<input class="form-control text-right" type="number" name="totalhabitacion" id="totalhabitacion" value="<?php echo $result[0]["totalhabitacion"]; ?>" />
							</div>
						</div>
						<div class="col-md-1 text-center"><br/><h1>+</h1></div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="totalproducto">Total producto:</label>
								<input type="hidden" name="totalproducto-h" id="totalproducto-h" value="<?php echo $result[0]["totalproducto"]; ?>" />
								<input class="form-control text-right" type="number" name="totalproducto" id="totalproducto" value="<?php echo $result[0]["totalproducto"]; ?>" />
							</div>
						</div>
						<div class="col-md-1 text-center"><br/><h1>=</h1></div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="totalingreso">Total:</label>
								<input type="hidden" name="totalingreso-h" id="totalingreso-h" value="<?php echo number_format($result[0]["totalhabitacion"]+$result[0]["totalproducto"],2); ?>" />
								<input class="form-control text-right" type="number" name="totalingreso" id="totalingreso" value="<?php echo number_format($result[0]["totalhabitacion"]+$result[0]["totalproducto"],2); ?>" readOnly />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label for="totalefectivo">Total efectivo:</label>
								<input type="hidden" name="totalefectivo-h" id="totalefectivo-h" value="<?php echo $result[0]["totalefectivo"]; ?>" />
								<input class="form-control text-right" type="number" name="totalefectivo" id="totalefectivo" value="<?php echo $result[0]["totalefectivo"]; ?>" />
							</div>
						</div>
						<div class="col-md-1 text-center"><br/><h1>+</h1></div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="totalvisa">Total visa:</label>
								<input type="hidden" name="totalvisa-h" id="totalvisa-h" value="<?php echo $result[0]["totalvisa"]; ?>" />
								<input class="form-control text-right" type="number" name="totalvisa" id="totalvisa" value="<?php echo $result[0]["totalvisa"]; ?>" />
							</div>
						</div>
						<div class="col-md-1 text-center"><br/><h1>=</h1></div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="totalforma">Total:</label>
								<input type="hidden" name="totalforma-h" id="totalforma-h" value="<?php echo number_format($result[0]["totalefectivo"]+$result[0]["totalvisa"],2); ?>" />
								<input class="form-control text-right" type="number" name="totalforma" id="totalforma"  value="<?php echo number_format($result[0]["totalefectivo"]+$result[0]["totalvisa"],2); ?>" readOnly />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-11 text-right">
							<button type="button" class="btn btn-primary w-25" id="btnRestablecer">Restablecer</button>
							<button type="submit" class="btn btn-success w-25" id="btnAplicar">Aplicar ajuste</button>	
						</div>					
					</div>					
				</form>
			</div>			
		</div>
	</div>
</body>
</html>