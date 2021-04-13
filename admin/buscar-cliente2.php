<?php
session_start();
//include "validar.php";
include "config.php";
$xidalquiler=$_REQUEST['idalquiler'];
$txtdato = strtoupper($_POST['txtdato']);
$por = $_POST['txtbuscarpor'];

if ($txtdato == ""){
	$sqlhuesped = $mysqli->query("select
		idhuesped,
		nombre,
		documento,
		nograto,
		comentarios
		from cliente limit 0");
} else if($por==1){
	$sqlhuesped = $mysqli->query("select
		idhuesped,
		nombre,
		documento,
		nograto,
		comentarios
		from cliente where nombre regexp '$txtdato|$txtdato.' order by idhuesped asc");
}else if($por==2){
	$sqlhuesped = $mysqli->query("select
		idhuesped,
		nombre,
		documento,
		nograto,
		comentarios
		from cliente where documento = '$txtdato'");
}else {
	$sqlhuesped = $mysqli->query("select
		idhuesped,
		nombre,
		documento,
		nograto,
		comentarios
		from cliente limit 0");
}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Administrador</title>
<?php include "head-include.php"; ?>

<script language="javascript" type="text/javascript">

function entregar(id,cliente,xidalquiler){
   var idalquiler=$("#idalquiler").val();
  
   $.ajax({
        url:'actualiza_cliente_alquiler.php',
        type:'get',
        data:{'idalquiler':idalquiler,'id':id,'cliente': cliente},
   
        success:function(data){
            console.log(data);
           data = eval("("+data+")");  
           //console.log(data);
            if(typeof data.success != "undefined"){                     
                if(data.errors==0){                             
                 
                  swal("Cliente Actualizado!", {
                        icon: "success",
                      });
                  window.close();
                  window.opener.location.reload();

                }else{
                    if(typeof data.errors != "undefined"){
                        if(data.success==0){
                           
                           swal("Error!","Ocurió un Error al Realizar Petición..!", "error");        
                        }
                       
                    }
                }
            }
                          
            
        },
        error:function(rpta){ 
         
         console.log(rpta);
           
            
        }

    });
    //window.opener.document.form1.txtidcliente.value = id;
    //window.opener.document.form1.txtcliente.value = cliente;
    //window.close();
}

</script>
<style type="text/css">
<!--
body {
	margin-top: 10px;
}
-->
</style></head>
<body OnLoad="form1.txtdato.focus()">
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="26" valign="middle" bgcolor="#FFFFFF" class="textoContenido"><strong>Buscar Huésped / Cliente</strong></td>
  </tr>
  <tr>
    <td height="25" valign="middle" bgcolor="#FFFFFF"><form name="form1" method="post" action="buscar-cliente2.php">
      <table width="100%" border="0" cellpadding="1" cellspacing="1">
        <tr>
          <td width="19%"><p>
            <input type="hidden" id="idalquiler" name="idalquiler" value="<?php echo $xidalquiler;?>">
            <label for="rdtipo2" class="textoContenidoMenor"> </label>
            <input name="txtbuscarpor" type="radio" id="rdnumero" value="2" checked="checked">
            <label for="rdnumero" class="textoContenidoMenor"> DNI </label> 
            <input name="txtbuscarpor" type="radio" id="rdtipo" value="1">
            <label for="rdtipo" class="textoContenidoMenor">Nombre</label>
            </p></td>
          <td width="49%"><input name="txtdato" type="text" class="textbox" id="txtdato" placeholder="Ingrese el dato a buscar"></td>
          <td width="32%"><button type="submit" class="btnnegro" style="border:0px; cursor:pointer;"> <i class="fa fa-search-plus"></i> Buscar </button></td>
        </tr>
      </table>
      <label></label>
    </form>    </td>
  </tr>
  <tr>
    <td height="77" valign="top" bgcolor="#FFFFFF">
	<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#F0F0F0">
        <tr class="textoContenidoMenor">
          <td width="3%" height="25" bgcolor="#F4F4F4" ><div align="center"><strong>#</strong></div></td>
          <td width="42%" height="25" align="left" valign="middle" bgcolor="#F4F4F4" ><div align="left">Huésped / Cliente</div></td>
          <td width="16%" align="left" valign="middle" bgcolor="#F4F4F4" ><div align="left">DNI </div></td>
          <td width="25%" align="left" valign="middle" bgcolor="#F4F4F4" ><div align="left">Descripción</div></td>
          <td width="9%" height="25" align="left" valign="middle" bgcolor="#F4F4F4" ><div align="center">No Grato </div></td>
          <td width="5%" height="25" bgcolor="#F4F4F4" ><div align="center"></div></td>
        </tr>
	  <?php
	$suma =0;		
	while($Fila = $sqlhuesped->fetch_row())
	{
		$suma++;
	?>
        <tr class="<?php if($Fila['3']==1){echo 'textoContenidoMenorRojo';}else{echo 'textoContenidoMenor';} ?>">
          <td height="25" bgcolor="#FFFFFF" class="textoContenidoNegro"><div align="center"><? echo $suma; ?></div></td>
          <td height="25" bgcolor="#FFFFFF" class="textoContenidoNegro">
		  <?php echo $Fila["1"];?></td>
          <td bgcolor="#FFFFFF" class="textoContenidoNegro"><? echo $Fila["2"];?></td>
          <td bgcolor="#FFFFFF" class="textoContenidoNegro"><? echo $Fila["4"];?></td>
          <td height="25" bgcolor="#FFFFFF" class="textoContenidoNegro" align="center"><?php if($Fila['3']==1){echo "<img src='imagenesv/desactivo.gif'/>";} ?></td>
          <td height="25" bgcolor="#FFFFFF" class="textoContenidoNegro"><div align="center">
            
            <a href="#" onclick='entregar(<? echo $Fila['0'];?> , "<?php echo $Fila['1']; ?>","<?php echo $xidalquiler;?>")' class="btnestado"> <i class="fa fa-check"></i> </a>
            
          </div></td>
        </tr>	
<?php
}
$sqlhuesped->free();
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