<?php
include "config.php";
//ini_set('display_errors', 1);

if(isset($_POST["up_data"])){
	$sql = $mysqli->query("
		UPDATE cliente SET 
			RUC = '".$_POST["ruc"]."',
			razon_social = '".$_POST["razon"]."',
			direccion = '".$_POST["direccion"]."'
		WHERE idhuesped=".$_POST["idcliente"]);
}

$sqlCli = $mysqli->query("
	select RUC, razon_social, direccion FROM cliente WHERE idhuesped = ".$_GET["idcliente"]
);

$resCli = $sqlCli->fetch_row();

?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title></title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#loader").hide();

			var loading = window.opener.document.getElementById('div_loading');
			if (loading){
				padre = loading.parentNode;
				padre.removeChild(loading);
			}

			<?php if(isset($sql)){ ?>
			<?php if($sql){ ?>
				window.opener.location.reload();
			<?php }} ?>

			$("#findRUC").click(function(){
				buscarRUC($("#ruc").val());
			});
		});

		function buscarRUC(documento){
			$("#loader").show();
			$.ajax({
	            data:  {
	            	'documento': documento,
	            	'tipo_documento':6
	            },
	            url:'../vendor/jossmp/sunatphp/testx.php',
	            type:  'get',
	            success:  function (response) {
	            	$("#loader").hide();
	            	data = eval("("+response+")");

		          
		            if(typeof data.success != "undefined"){           
						if(data.success==true){ 
						  var vnombre = data.data.nombre_o_razon_social.replace("'", " ");
						  var vdireccion = data.data.direccion_completa ? data.data.direccion_completa.replace("Ñ", "N") : '-';
						  var vcondicion = data.data.condicion;
						  //var vnombre = data.result.RazonSocial;
						  vdireccion = vdireccion.replace("ñ", "n");

						  $("#razon").val(vnombre);
						  $("#direccion").val(vdireccion);
						  $("#condicion").text(vcondicion);
						}else{
							alert(data.msg);
						  $("#razon").val('');
						  $("#direccion").val('');   
						  $("#condicion").text('');
						}
		            }
	            },
	            errors: function(response){
	            	$("#loader").hide();
                    console.log(response);
                    alert("Error de servidor");
                }
			});
		}
	</script>
	<style type="text/css">
		#loader {
		    width: 100%;
		    height: 100%;
		    background: rgba(0, 0, 0, 0.5);
		    text-align: center;
		    position: fixed;
		    top: 0;
		    z-index: 900;
		    padding-top:200px;
		    color:#FFFFFF;
		    font-size:30px;
		    z-index: 10;
		}
	</style>
</head>
<body>
	<div id="loader">
		<img src="imagenesv/loader.gif" alt="Cargando">
		<p>Cargando</p>
	</div>
	<div class="container">		
		<form action="datos-fiscales.php?idcliente=<?php echo($_GET["idcliente"]); ?>" method="post">
			<h3>Datos fiscales</h3>
			<?php if(isset($sql)){ ?>
			<?php if($sql){ ?>
			<div class="alert alert-success">
				<strong>Éxito!</strong> Se guardarón los datos facturación.
			</div>
			<?php }else{ ?>
			<div class="alert alert-warning">
				<strong>Error!</strong> Verifique.
			</div>
			<?php }} ?>
			<input type="hidden" name="up_data" value="1" />
			<input type="hidden" name="idcliente" value="<?php echo($_GET["idcliente"]); ?>" />
			
			<div class="form-group">
				<label for="ruc">RUC:</label>
				<div class="row">
					<div class="col-xs-9">
						<input type="text" name="ruc" id="ruc" class="form-control" value="<?php echo $resCli[0]; ?>" required />
					</div>
					<div class="col-xs-3">
						<button type="button" class="btn btn-info" id="findRUC">Buscar</button>
					</div>
				</div>
			</div>				
			
			<div class="form-group">
				<label for="razon">Razón social:</label>
				<input type="text" name="razon" id="razon" class="form-control" value="<?php echo $resCli[1]; ?>" required />
			</div>
			<div class="form-group">
				<label for="Dirección">Dirección:</label>
				<input type="text" name="direccion" id="direccion" class="form-control" value="<?php echo $resCli[2]; ?>" required />
			</div>
			<div class="form-group">
				<label for="Dirección">Condición:</label>
				<strong name="condicion" id="condicion"></strong>
			
			</div>
			<button type="submit" class="btn btn-primary" style="width: 49%;">Guardar</button>
			<button type="button" onclick="window.close();" class="btn btn-success" style="width: 49%;">Salir</button>
		</form>
	</div>
</body>
</html>