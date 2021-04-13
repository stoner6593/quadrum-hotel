<?php
date_default_timezone_set('America/Lima');
session_start();
include "validar.php";
include "config.php";
include "include/functions.php";
date_default_timezone_set('America/Lima');

$xidturno = isset($_GET["idturno"]) ? $_GET["idturno"] : $_SESSION['idturno'];

$sqlTarifas = $mysqli->prepare("SELECT * FROM hab_venta ORDER BY numero");

$sqlTarifas->execute(); 
$result = $sqlTarifas->get_result();
$result = $result->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Ajustar tarifas</title>
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
			
		});

		function cambiandoTarifa(input){
			$("input[type=text]").attr("disabled",true);
			var newVal = $(input).val();
			$.ajax({
				url:"include/tarifas/ajustar.php",
				method:"post",
				data:{
					campo:$(input).attr("name"),
					valor:newVal,
					idHab:$(input).attr("id").split("_")[2]
				},
				dataType:"json",
				success:function(rsp){
					if(!rsp.success){
						$(input).val(actualVal);
					}else{
						$(input).val(Number.parseFloat(newVal).toFixed(2));
					}
					$("input[type=text]").attr("disabled",false);
				}
			});
		}

		var actualVal = 0;

		function actualValor(input){
			actualVal = $(input).val();
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
				<h5 class="tit-pantalla">Ajustar tarifas</h5>
				<form action="include/tarifas/ajustar.php" method="post" name="frmAjustarTarifa" id="frmAjustarTarifa">
					<input type="hidden" name="id-turno" id="id-turno" value="<?php echo $xidturno; ?>">
					<input type="hidden" name="id-usuario" id="id-usuario" value="<?php echo $_SESSION["xyzidusuario"]; ?>">
					<?php foreach($result as $key => $habV){ ?>
					<div class="row" style="margin-top: 20px;">
						<div class="col-md-12" style="text-align: center;">
							<h5 class="text-primary">Habitación <?php echo $habV["numero"]; ?></h5>
						</div>
					</div>
					<div class="row">
						<div class="col-md-2">
							<label for="preciod_d">Dia - Domingo</label>
							<input type="text" value="<?php echo $habV["preciod_d"]; ?>" class="form-control" name="preciod_d" id="preciod_d_<?php echo $habV["idhabitacion"]; ?>" style="text-align: right;" onchange="cambiandoTarifa(this)" onfocus="actualValor(this)"/>	
						</div>
						<div class="col-md-2">
							<label for="preciod_l">Dia - Lunes</label>
							<input type="text" value="<?php echo $habV["preciod_l"]; ?>" class="form-control" name="preciod_l" id="preciod_l_<?php echo $habV["idhabitacion"]; ?>" style="text-align: right;" onchange="cambiandoTarifa(this)" onfocus="actualValor(this)"/>	
						</div>
						<div class="col-md-2">
							<label for="preciod_m">Dia - Martes</label>
							<input type="text" value="<?php echo $habV["preciod_m"]; ?>" class="form-control" name="preciod_m" id="preciod_m_<?php echo $habV["idhabitacion"]; ?>" style="text-align: right;" onchange="cambiandoTarifa(this)" onfocus="actualValor(this)"/>	
						</div>
						<div class="col-md-2">
							<label for="preciod_w">Dia - Miercoles</label>
							<input type="text" value="<?php echo $habV["preciod_w"]; ?>" class="form-control" name="preciod_w" id="preciod_w_<?php echo $habV["idhabitacion"]; ?>" style="text-align: right;" onchange="cambiandoTarifa(this)" onfocus="actualValor(this)"/>	
						</div>
						<div class="col-md-2">
							<label for="preciod_j">Dia - Jueves</label>
							<input type="text" value="<?php echo $habV["preciod_j"]; ?>" class="form-control" name="preciod_j" id="preciod_j_<?php echo $habV["idhabitacion"]; ?>" style="text-align: right;" onchange="cambiandoTarifa(this)" onfocus="actualValor(this)"/>	
						</div>
						<div class="col-md-2">
							<label for="preciod_v">Dia - Viernes</label>
							<input type="text" value="<?php echo $habV["preciod_v"]; ?>" class="form-control" name="preciod_v" id="preciod_v_<?php echo $habV["idhabitacion"]; ?>" style="text-align: right;" onchange="cambiandoTarifa(this)" onfocus="actualValor(this)"/>	
						</div>
					</div>
					<div class="row">
						<div class="col-md-2">
							<label for="preciod_s">Dia - Sabado</label>
							<input type="text" value="<?php echo $habV["preciod_s"]; ?>" class="form-control" name="preciod_s" id="preciod_s_<?php echo $habV["idhabitacion"]; ?>" style="text-align: right;" onchange="cambiandoTarifa(this)" onfocus="actualValor(this)"/>	
						</div>
						<div class="col-md-2">
							<label for="precioh_d">Hora - Domingo</label>
							<input type="text" value="<?php echo $habV["precioh_d"]; ?>" class="form-control" name="precioh_d" id="precioh_d_<?php echo $habV["idhabitacion"]; ?>" style="text-align: right;" onchange="cambiandoTarifa(this)" onfocus="actualValor(this)"/>	
						</div>
						<div class="col-md-2">
							<label for="precioh_l">Hora - Lunes</label>
							<input type="text" value="<?php echo $habV["precioh_l"]; ?>" class="form-control" name="precioh_l" id="precioh_l_<?php echo $habV["idhabitacion"]; ?>" style="text-align: right;" onchange="cambiandoTarifa(this)" onfocus="actualValor(this)"/>	
						</div>
						<div class="col-md-2">
							<label for="precioh_m">Hora - Martes</label>
							<input type="text" value="<?php echo $habV["precioh_m"]; ?>" class="form-control" name="precioh_m" id="precioh_m_<?php echo $habV["idhabitacion"]; ?>" style="text-align: right;" onchange="cambiandoTarifa(this)" onfocus="actualValor(this)"/>	
						</div>
						<div class="col-md-2">
							<label for="precioh_w">Hora - Miercoles</label>
							<input type="text" value="<?php echo $habV["precioh_w"]; ?>" class="form-control" name="precioh_w" id="precioh_w_<?php echo $habV["idhabitacion"]; ?>" style="text-align: right;" onchange="cambiandoTarifa(this)" onfocus="actualValor(this)"/>	
						</div>
						<div class="col-md-2">
							<label for="precioh_j">Hora - Jueves</label>
							<input type="text" value="<?php echo $habV["precioh_j"]; ?>" class="form-control" name="precioh_j" id="precioh_j_<?php echo $habV["idhabitacion"]; ?>" style="text-align: right;" onchange="cambiandoTarifa(this)" onfocus="actualValor(this)"/>	
						</div>
					</div>
					<div class="row">
						<div class="col-md-2">
							<label for="precioh_v">Hora - Viernes</label>
							<input type="text" value="<?php echo $habV["precioh_v"]; ?>" class="form-control" name="precioh_v" id="precioh_v_<?php echo $habV["idhabitacion"]; ?>" style="text-align: right;" onchange="cambiandoTarifa(this)" onfocus="actualValor(this)"/>	
						</div>
						<div class="col-md-2">
							<label for="precioh_s">Hora - Sabado</label>
							<input type="text" value="<?php echo $habV["precioh_s"]; ?>" class="form-control" name="precioh_s" id="precioh_s_<?php echo $habV["idhabitacion"]; ?>" style="text-align: right;" onchange="cambiandoTarifa(this)" onfocus="actualValor(this)"/>	
						</div>
						<div class="col-md-2">
							<label for="precio12_d">12 horas - Domingo</label>
							<input type="text" value="<?php echo $habV["precio12_d"]; ?>" class="form-control" name="precio12_d" id="precio12_d_<?php echo $habV["idhabitacion"]; ?>" style="text-align: right;" onchange="cambiandoTarifa(this)" onfocus="actualValor(this)"/>	
						</div>
						<div class="col-md-2">
							<label for="precio12_l">12 horas - Lunes</label>
							<input type="text" value="<?php echo $habV["precio12_l"]; ?>" class="form-control" name="precio12_l" id="precio12_l_<?php echo $habV["idhabitacion"]; ?>" style="text-align: right;" onchange="cambiandoTarifa(this)" onfocus="actualValor(this)"/>	
						</div>
						<div class="col-md-2">
							<label for="precio12_m">12 horas - Martes</label>
							<input type="text" value="<?php echo $habV["precio12_m"]; ?>" class="form-control" name="precio12_m" id="precio12_m_<?php echo $habV["idhabitacion"]; ?>" style="text-align: right;" onchange="cambiandoTarifa(this)" onfocus="actualValor(this)"/>	
						</div>
						<div class="col-md-2">
							<label for="precio12_w">12 horas - Miercoles</label>
							<input type="text" value="<?php echo $habV["precio12_w"]; ?>" class="form-control" name="precio12_w" id="precio12_w_<?php echo $habV["idhabitacion"]; ?>" style="text-align: right;" onchange="cambiandoTarifa(this)" onfocus="actualValor(this)"/>	
						</div>
					</div>
					<div class="row">
						<div class="col-md-2">
							<label for="precio12_j">12 horas - Jueves</label>
							<input type="text" value="<?php echo $habV["precio12_j"]; ?>" class="form-control" name="precio12_j" id="precio12_j_<?php echo $habV["idhabitacion"]; ?>" style="text-align: right;" onchange="cambiandoTarifa(this)" onfocus="actualValor(this)"/>	
						</div>
						<div class="col-md-2">
							<label for="precio12_v">12 horas - Viernes</label>
							<input type="text" value="<?php echo $habV["precio12_v"]; ?>" class="form-control" name="precio12_v" id="precio12_v_<?php echo $habV["idhabitacion"]; ?>" style="text-align: right;" onchange="cambiandoTarifa(this)" onfocus="actualValor(this)"/>	
						</div>
						<div class="col-md-2">
							<label for="precio12_s">12 horas - Sabado</label>
							<input type="text" value="<?php echo $habV["precio12_s"]; ?>" class="form-control" name="precio12_s" id="precio12_s_<?php echo $habV["idhabitacion"]; ?>" style="text-align: right;" onchange="cambiandoTarifa(this)" onfocus="actualValor(this)"/>	
						</div>
					</div>
					<?php } ?>
					<!--<div class="row" style="margin-top: 20px;">
						<div class="col-md-11 text-right">
							<button type="button" class="btn btn-primary w-25" id="btnRestablecer">Restablecer</button>
							<button type="submit" class="btn btn-success w-25" id="btnAplicar">Aplicar ajuste</button>	
						</div>					
					</div>-->					
				</form>
			</div>			
		</div>
	</div>
</body>
</html>