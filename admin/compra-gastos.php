<?php
include "validar.php";
include "config.php";
include "include/functions.php";
date_default_timezone_set('America/Lima');

$xidturno = $_SESSION['idturno'];

/*$txtdato = $_POST['txtdato'];

if($txtdato==""){
	$sqlproducto = $mysqli->query("select
	idgasto,
	nombre,
	cantidad,
	monto,
	descripcion,
	fechayhora,
	estadoturno,
	usuario,
	descripcion,
	idturno
	
	from gasto where estadoturno = 1 and idturno = '$xidturno' order by idgasto asc");
}else{
	$sqlproducto = $mysqli->query("select
	idgasto,
	nombre,
	cantidad,
	monto,
	descripcion,
	fechayhora,
	estadoturno,
	usuario,
	descripcion,
	idturno
	
	from gasto 
	where estadoturno = 1 and nombre regexp '$txtdato|$txtdato.' 
	and idturno = '$xidturno' order by idgasto asc");
}*/
$concatena='';
$finicio=@($_POST['finicio']);
$ffin=@($_POST['ffin']);

if($finicio){
  $f1=explode("/",$finicio);
  $newfecha1=$f1[2].'-'.$f1[1].'-'.$f1[0];
}
if($ffin){
  $f2=explode("/",$ffin);
  $newfecha2=$f2[2].'-'.$f2[1].'-'.$f2[0];
}
if($finicio && $ffin){
  $concatena=' and DATE(fechayhora) between "'.$newfecha1.'" and "'.$newfecha2.'" ';
}


$sqlproducto = $mysqli->query("select
  idgasto,
  nombre,
  cantidad,
  monto,
  descripcion,
  fechayhora,
  estadoturno,
  usuario,
  descripcion,
  idturno
  
  from gasto 
  where estadoturno = 1 ".$concatena
  ." and idturno = '$xidturno' order by idgasto asc");

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Reporte Compra/Gastos</title>

<?php include "head-include.php"; ?>
<link href="datatable/css/buttons.dataTables.min.css" rel="stylesheet"> 
<link href="datatable/css/jquery.dataTables.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous"> 
<link href="basscss.min.css" rel="stylesheet">

</head>
<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0">

    <tr>
      <td height="25" colspan="3"><?php include ("head.php"); ?></td>
    </tr>
    <tr>
      <td width="185" height="25" align="left" valign="top"><?php include ("menu_nav.php"); ?></td>
      <td width="25">&nbsp;</td>
      <td width="810" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
       
          <tr>
            <td width="525" height="30"> <h3 style="color:#E1583E;"> <i class="fa fa-shopping-basket"></i> Registro de Compras Gastos / Stock</h3> </td>
            <td width="285" align="center"> 

            <button onclick="window.location.href = 'compra-gastos-editor.php';" class="btngris" style="border:0px; cursor:pointer;"> <i class="fa fa-plus-circle"></i> Registrar Compra / Gasto </button> 
            
            </td>
          </tr>
          <tr>
            <td height="30" colspan="2"><table width="100%" border="0" cellpadding="1" cellspacing="1">
             
                <tr>
                  <td height="30"><div class="lineahorizontal" style="background:#EFEFEF;"></div></td>
                </tr>
                <!-- <tr>
                  <td height="30">
                    <form id="form1" name="form1" method="post">
                    <table width="100%" border="0" cellpadding="1" cellspacing="1">

                        <tr>
                          <td width="65%"><input name="txtdato" type="text" class="textbox" id="txtdato" placeholder="Ingrese el nombre del producto o servicio"></td>
                          <td width="35%">
                          <button type="submit" class="btnnegro" style="border:0px; cursor:pointer;"> <i class="fa fa-search-plus"></i> Buscar </button> 
                          </td>
                        </tr>

                    </table>
                    </form>
                  </td>
                </tr> -->
                <tr>
                  <td height="30">
                    <form name="frmlistadocumentos" id="frmlistadocumentos" method="POST">
                      Fecha Inicio:<input name="finicio" value="<?php if ($finicio): echo $finicio; else: echo date("d/m/Y"); endif;?>" type="text" class="form-control" id="datepicker1" placeholder=" dd/mm/YYYY"  >  Fecha Inicio:<input name="ffin" type="text" value="<?php if ($ffin): echo $ffin; else:  echo date("d/m/Y"); endif;?>" class="form-control" id="datepicker2" placeholder=" dd/mm/YYYY" >
                      <button type="button" class="btn btn-primary mb1 bg-blue" onClick="document.frmlistadocumentos.submit();"  style="border:0px; cursor:pointer;"> <i class="fas fa-search" style="font-size: 14px;"></i></button>
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
                  <td height="30">
                    <table id="example" width="100%" class="display nowrap" style="width:100%">
                    <thead>
                      <tr class="textoContenidoMenor">
                        <td width="45" height="25" align="center" bgcolor="#F4F4F4">#</td>
                        <td width="272" height="25" bgcolor="#F4F4F4">Nombre</td>
                        <td width="58" height="25" align="center" bgcolor="#F4F4F4">Cantidad </td>
                        <td width="92" height="25" align="right" bgcolor="#F4F4F4">Monto (S/)</td>
                        <td width="80" align="center" bgcolor="#F4F4F4">Fecha</td>
                        <td width="81" align="center" bgcolor="#F4F4F4">Usuario </td>
                        <td width="208" align="left" bgcolor="#F4F4F4">Anotaciones</td>
                      </tr>
                      </thead>
                      <tbody>
                      <?php 
          					  $num = 0;
          					  while($xpFila = $sqlproducto->fetch_row()) { 
          					  $num++;
          					  ?>
                                <tr class="textoContenidoMenor">
                                  <td height="25" align="center" bgcolor="#FFFFFF"><?php echo $num;?></td>
                                  <td height="25" bgcolor="#FFFFFF"><?php echo $xpFila['1'];?></td>
                                  <td height="25" align="center" bgcolor="#FFFFFF"><?php echo $xpFila['2'];?></td>
                                  <td height="25" align="right" bgcolor="#FFFFFF"><?php echo number_format($xpFila['3'],2);?></td>
                                  <td align="center" bgcolor="#FFFFFF"><?php echo Cfecha($xpFila['5']);?></td>
                                  <td height="25" align="center" bgcolor="#FFFFFF"><?php echo $xpFila['7'];?></td>
                                  <td align="left" bgcolor="#FFFFFF"><?php echo $xpFila['8'];?></td>
                                </tr>
                              
                              <?php
          					  }
          					  $sqlproducto->free();
          					?>  
                    </tbody>
                  </table>
                </td>
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

</body>
</html>

<script type="text/javascript" src="../admin/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="../admin/datatable/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="../admin/datatable/buttons.flash.min.js"></script>
<script type="text/javascript" src="../admin/datatable/jszip.min.js"></script>
<script type="text/javascript" src="../admin/datatable/pdfmake.min.js"></script>
<script type="text/javascript" src="../admin/datatable/vfs_fonts.js"></script>
<script type="text/javascript" src="../admin/datatable/buttons.html5.min.js"></script>
<script type="text/javascript" src="../admin/datatable/buttons.print.min.js"></script>


<?php include ("footer.php") ?>

<script type="text/javascript">  

  $(function(){

    var table;

      table = $('#example').DataTable( {
        responsive: true,
        "bProcessing" : true,     
        "bScrollInfinite": true,
        "bScrollCollapse": true,
         dom: 'Bfrtip',     
        "BAutoWidth"  : true,
        "bJQueryUI"   : true,     
        "paging": true,
        "bDestroy": true,
        "bDeferRender": true,
        //"sAjaxSource"   : "equipos_malogrados/listar_equipos/"+tipo_e,
        /*"aaSorting": [[ 0, 'asc' ]],
        "aoColumns": [
          
          { "sTitle": "ID"},
          { "sTitle": "Cliente" },
          { "sTitle": "Documento"},
          { "sTitle": "Fecha" },
          { "sTitle": "Monto" },
          { "sTitle": "Anulado","sWidth": "70px" , "sClass": "center"},
          { "sTitle": "Tipo","sWidth": "70px" , "sClass": "center"},
          { "sTitle": "PDF","sWidth": "70px" , "sClass": "center"},
          { "sTitle": "XML","sWidth": "70px" , "sClass": "center"},
          { "sTitle": "ANULAR","sWidth": "70px" , "sClass": "center"}          
         
          ],*/
         buttons: [
                    {
                        extend: 'print',
                        exportOptions: {
                          columns: [ 0,1,2,3,4,5,6]
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                          columns: [ 0,1,2,3,4 ,5,6]
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                          columns: [ 0,1,2,3,4,5,6]
                        }
                    }
          ]
      } );
  })
</script>