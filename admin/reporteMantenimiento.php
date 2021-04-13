<?php
include "validar.php";
include "config.php";
include "include/functions.php";
date_default_timezone_set('America/Lima');

$txtbuscarpor = @$_POST['txtbuscarpor'];
$txtdato = @$_POST['txtdato'];

$concatena='';

$finicio=@($_POST['finicio']);
$ffin=@($_POST['ffin']);
$newfecha1='';
$newfecha2='';
if($finicio){ 
$f1=explode("/",$finicio);
$newfecha1=$f1[2].'-'.$f1[1].'-'.$f1[0];
}
if($ffin){
$f2=explode("/",$ffin);
$newfecha2=$f2[2].'-'.$f2[1].'-'.$f2[0];
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Reporte de mantenimiento</title>

<?php include "head-include.php"; ?>
<link href="datatable/css/buttons.dataTables.min.css" rel="stylesheet"> 
<link href="datatable/css/jquery.dataTables.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous"> 
<link href="basscss.min.css" rel="stylesheet">

<style type="text/css">
  div.dataTables_wrapper {
        width: 940px;
        margin: 0 auto;
    }
</style>

</head>
<body>

<table width="100%" border="0" cellpadding="0" cellspacing="0" id="example1" class=" table table-bordered table-hover">

    <tr>
      <td height="25" colspan="3"><?php include ("head.php"); ?></td>
    </tr>
    <tr>
      <td width="185" height="25" align="left" valign="top"><?php include ("menu_nav.php"); ?></td>
      <td width="25">&nbsp;</td>
      <td width="810" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
       
          
          <tr>
            <td height="30" colspan="2"><table width="100%" border="0" cellpadding="1" cellspacing="1">
             
                <tr>
                  <td height="30"><div class="lineahorizontal" style="background:#BFBFBF;"></div></td>
                </tr>
                <tr>
                  <td height="30" class=" text-success">
                    <h3 style="color:#E1583E;"> <i class="fa fa-book"></i> Reporte de mantenimiento</h3>
                    
                  </td>
                </tr>
                <tr>
                  <td height="20">
                  <?php if (isset($_SESSION['msgerror'])){ ?>
                  <div class="alert alert-success alert-dismissable textoContenidoMenor">
                    <?php echo $_SESSION['msgerror'];$_SESSION['msgerror']="";?> 
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                  </div>
                  <?php } ?></td>
              </tr>
              <tr>
                <td width="1%">

                  <form action="reporteMantenimiento.php" name="frmlistadocumentos" id="frmlistadocumentos" method="POST">
                    Fecha Inicio:<input name="finicio" value="<?php if ($finicio): echo $finicio; else: echo date("d/m/Y"); endif;?>" type="text" class="form-control" id="datepicker1" placeholder=" dd/mm/YYYY"  >  
                    Fecha Fin:<input name="ffin" type="text" value="<?php if ($ffin): echo $ffin; else:  echo date("d/m/Y"); endif;?>" class="form-control" id="datepicker2" placeholder=" dd/mm/YYYY" >
                   <button type="button" class="btn btn-primary mb1 bg-blue" onClick="document.frmlistadocumentos.submit();"  style="border:0px; cursor:pointer;"> <i class="fas fa-search" style="font-size: 14px;"></i></button> 

                   <!-- <button id="ProcesaEnvio" class="btnrojo"><i class="fa fa-arrow-up" id="liAnula"></i> Enviar Sunat</button> 

                   <button id="Consulta" class="btnrojo"><i class="fa fa-arrow-up" id="liAnula"></i>Consultar Ticket</button>  -->
                  </form>   
                </td>
                
              </tr> 
              
                <tr>
                  <td height="30">
                    

                    <table id="example" class="display nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID MANT</th>
                                <th>FECHA REGISTRO</th>
                                <th>HABITACIÓN</th>
                                <th>EMPLEADO</th>
                                <th>FECHA INICIO</th>
                                <th>FECHA FIN</th>
                                <th>TIPO</th>
                                <th>OBSERVACIÓN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!--<tr>
                              <td><?php /*echo $xhFila['1']; */?></td>
                              <td><?php /*echo ($xhFila['3']); */?></td>
                              <td><?php /*echo $xhFila['2']; */?></td>
                              <td><?php /*echo $xhFila['4']; */?></td>
                              <td><?php /*echo $xhFila['5']; */?></td>
                              <td><?php /*echo $xhFila['6']; */?></td>
                              <td><?php /*echo $xhFila['8']; */?></td>
                              <td><?php /*echo $xhFila['15']; */?></td>
                              <td><?php /*echo $xhFila['9']; */?> <?php /*echo $xhFila['10']; */?></td>
                              <td><?php /*echo $xhFila['11']; */?></td>
                              <td><?php /*echo $xhFila['12']; */?></td>
                              <td><?php /*echo $xhFila['13']; */?></td>
                              <td><?php /*echo $xhFila['14']; */?></td>

                              <td><?php /*echo $xhFila['16']; */?></td>
                              <td><?php /*echo $xhFila['17']; */?></td>
                              <td><?php /*echo $xhFila['18']; */?></td>
                              <td><?php /*echo $xhFila['19']; */?></td>
                            </tr>-->
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
<!--<script type="text/javascript" src="../admin/datatable/jquery-1.12.4.js"></script>-->
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
  
  var nombre_archivo="";

  $(function(){

      $( "#datepicker1" ).datepicker();
      $( "#datepicker2" ).datepicker();

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
        "pagingType": "full_numbers",
        "scrollX": true,
        //"sAjaxSource"   : "include/huesped/view_libro_huesped.php?finicio=<?php if ($newfecha1): echo $newfecha1;else: echo ''; endif;?>&ffin=<?php if ($newfecha2): echo $newfecha2;else: echo ''; endif;?>",
        "aaSorting": [[ 2, 'asc' ]],
        "aoColumns": [          
          { "sTitle": "ID MANT", mData: 'hman_id'},
          { "sTitle": "FECHA REGISTRO", mData: 'fecha_registro' },
          { "sTitle": "HABITACIÓN", mData: 'idhabitacion'},
          { "sTitle": "EMPLEADO", mData: 'idempleado' },
          { "sTitle": "FECHA INICIO", mData: 'fecha_inicio' },
          { "sTitle": "FECHA FIN", mData: 'fecha_fin' },
          { "sTitle": "TIPO", mData: 'idtipo' },
          { "sTitle": "OBSERVACIÓN", mData: 'observacion' },
         ],
         buttons: [
                    {
                        extend: 'print',
                        exportOptions: {
                          columns: [ 0,1,2,3,4,5,6,7]
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                          columns: [ 0,1,2,3,4,5,6,7]
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                          columns: [ 0,1,2,3,4,5,6,7]
                        }
                    }
          ],
          "ajax": {
              "method": "POST",
              "url": "include/mantenimiento/view_reporte_mantenimiento.php",
              "data": function (d) {
                  return $.extend({}, d, {
                      "finicio": <?php if ($finicio): echo "'".$finicio."'";else: echo "''"; endif;?>,
                      "ffin": <?php if ($ffin): echo "'".$ffin."'";else: echo "''"; endif;?>
                  });
              }
          }
      } );
   
  });
</script>



