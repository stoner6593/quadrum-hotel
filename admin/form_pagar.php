<?php
include "config.php";

if(isset($_POST["up_data"])){
	$sql = $mysqli->query("
		UPDATE al_venta_detalle SET 
			cobrado = '1',
			comentarioscredito = '".$_POST["comentarios"]."'
		WHERE idalquilerdetalle=".$_POST["iddet"]);
}

$sqlCli = $mysqli->query("
	select comentarioscredito, cobrado FROM al_venta_detalle WHERE idalquilerdetalle = ".$_GET["iddet"]
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
			
			<?php if($resCli[1] == 1){ ?>
				window.opener.location.reload();
				window.close();
			<?php } ?>
		});
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
		<form action="form_pagar.php?iddet=<?php echo $_GET["iddet"]; ?>" method="post">
			<h3>Realizar pago</h3>

			<input type="hidden" name="up_data" value="1" />
			<input type="hidden" name="iddet" value="<?php echo($_GET["iddet"]); ?>" />
			
			<div class="form-group">
				<label for="comentarios">Comentarios:</label>
				<textarea type="text" name="comentarios" id="comentarios" class="form-control" required><?php echo $resCli[0]; ?></textarea>
			</div>
			<button type="submit" class="btn btn-primary" style="width: 49%;">Marcar como Pagado</button>
			<button type="button" onclick="window.close();" class="btn btn-success" style="width: 49%;">Salir</button>
		</form>
	</div>
</body>
</html>