<?php
session_start();
include "validar.php";
include "include/functions.php";

include "config.php";
$nomAdicional = $_GET['nom'];

$sqladicional = $mysqli->query("
	SELECT DISTINCT nombre, dni 
	FROM al_venta_personaadicional 
	where nombre like '%".$nomAdicional."%' order by nombre;");

$arr = array();

while($row = $sqladicional->fetch_assoc()){
	array_push($arr, $row);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<?php include "head-include.php"; ?>
	<script type="text/javascript">
		$(document).ready(function(){
			
		});

		function seleccionarPersona(nombre,dni){
			var urlAnterior = document.referrer;
			var finUrl = urlAnterior.indexOf("&nombreAd");
			var url = urlAnterior;
			if(finUrl !== -1){
				url = urlAnterior.substring(0,finUrl);
			}
			location.href=url+"&nombreAd="+nombre+"&dniAd="+dni;
		}
	</script>
</head>
<body>
	<button type="button" class="btnnegro" id="btnBuscar" onclick="window.history.back()" style="border:0px; cursor:pointer;">
		<i class="fa fa-arrow-left"></i> Regresar 
	</button>
	<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#F0F0F0">
		<tr class="textoContenidoMenor">
			<th bgcolor="#F4F4F4">Nombre</th>
			<th bgcolor="#F4F4F4">Documento</th>
			<th bgcolor="#F4F4F4"></th>
		</tr>
		<?php foreach ($arr as $key => $row) { ?>
			<tr class="textoContenidoMenor">
				<td bgcolor="#FFFFFF" class="textoContenidoNegro"><?php echo $row["nombre"]; ?></td>
				<td bgcolor="#FFFFFF" class="textoContenidoNegro"><?php echo $row["dni"]; ?></td>
				<td bgcolor="#FFFFFF" class="textoContenidoNegro">
					<button type="button" onclick="seleccionarPersona(
						'<?php echo $row["nombre"]; ?>','<?php echo $row["dni"]; ?>'
					);">Seleccionar</button>
				</td>
			</tr>
		<?php } ?>
	</table>


</body>
</html>