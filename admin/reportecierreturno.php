<?php
include "validar.php";
include "config.php";
include "include/functions.php";
date_default_timezone_set('America/Lima');

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
$concatena='';
if($finicio && $ffin){
  $concatena=' DATE(a.fechaapertura) between "'.$newfecha1.'" and "'.$newfecha2.'" ';
}else{

    $date = new DateTime();
    $finicio= $date->format('d/m/Y');
    $ffin= $date->format('d/m/Y');

    //$finicio=date;
    //$ffin=date;

    $f1=explode("/",$finicio);
    $newfecha1=$f1[2].'-'.$f1[1].'-'.$f1[0];

    $f2=explode("/",$ffin);
    $newfecha2=$f2[2].'-'.$f2[1].'-'.$f2[0];

    $concatena=' DATE(a.fechaapertura) between "'.$newfecha1.'" and "'.$newfecha2.'" ';
}
$sqlalquiler = $mysqli->query("select
	a.idturno,
	a.totalhabitacion,
	a.totaladicional,
	a.totalproducto,	
	a.totalefectivo,
	a.totalvisa,	
	a.idusuario,
	a.estadoturno,
	a.fechaapertura,
	a.fechacierre,
	IFNULL(a.totaldescuento,0),
	IFNULL((select sum(g.monto)
	from gasto g
	where g.idturno = a.idturno and g.usuario = a.idusuario
    and g.tipooperacion = 1),0) as monto_compras,
    IFNULL((select sum(g.monto)
	from gasto g
	where g.idturno = a.idturno and g.usuario = a.idusuario
    and g.tipooperacion = 2),0) as monto_gastos,
    b.user_nombre,
    DATE_FORMAT(a.fechaapertura, '%d/%m/%Y' ) as fechaaperturaformat,
    DATE_FORMAT(a.fechacierre, '%d/%m/%Y' ) as fechacierreformat
	from ingresosturno a 
	inner join usuario b on b.user_id = a.idusuario
	where ".$concatena);

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Reporte Cierre Turno</title>

<?php include "head-include.php"; ?>
<link href="datatable/css/buttons.dataTables.min.css" rel="stylesheet"> 
<link href="datatable/css/jquery.dataTables.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous"> 
<link href="basscss.min.css" rel="stylesheet">

    <style type="text/css">
        div.dataTables_wrapper {
            width: 940px;
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
      <td width="940" valign="top">
          <table width="100%" border="0" cellpadding="0" cellspacing="0">
       
          
          <tr>
            <td height="30" colspan="2">
                <table width="100%" border="0" cellpadding="1" cellspacing="1">
             
                <tr>
                  <td height="30"><div class="lineahorizontal" style="background:#BFBFBF;"></div></td>
                </tr>
                <tr>
                  <td height="30" class=" text-success">
                    <h3 style="color:#E1583E;"> <i class="fa fa-users"></i> Listado de Cierre Turno</h3>
                    
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

                  <form action="reportecierreturno.php" name="frmlistadocumentos" id="frmlistadocumentos" method="POST">
                    Fecha Inicio:<input name="finicio" value="<?php if ($finicio): echo $finicio; else: echo date("d/m/Y"); endif;?>" type="text" class="form-control" id="datepicker1" placeholder=" dd/mm/YYYY"  >
                    Fecha Inicio:<input name="ffin" type="text" value="<?php if ($ffin): echo $ffin; else:  echo date("d/m/Y"); endif;?>" class="form-control" id="datepicker2" placeholder=" dd/mm/YYYY" >
                    <button type="button" class="btn btn-primary mb1 bg-blue" onClick="document.frmlistadocumentos.submit();"  style="border:0px; cursor:pointer;"> <i class="fas fa-search" style="font-size: 14px;"></i></button>

                  </form>   
                </td>
                
              </tr> 
              
                <tr>
                  <td height="10">

                    <table id="example" class="display nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Position</th>
                                <th>Office</th>
                                <th>Age</th>
                                <th>Start date</th>
                                <th>Salary</th>
                                <th>Start date</th>
                                <th>Salary</th>
                                <th>Start date</th>
                                <th>Salary</th>
                                <th>Salary</th>
                                <th>Start date</th>
                                <th>Salary</th>
                                <th>Start date</th>
                                <th>Salary</th>
                                <th>Salary</th>
                                <th>Salary</th>
                            </tr>
                        </thead>
                        <tbody>
                          <?php
                            $item = 0;
                            while ($hFila = $sqlalquiler->fetch_row())
                            {
                                //Habitacion
                                $xhabitacion = number_format($hFila['1'],2);

                                //Producto
                                $xproducto = number_format($hFila['3'],2);

                                //Visa/Efectivo
                                $xefectivo = number_format($hFila['4'],2);
                                $xvisa = number_format($hFila['5'],2);

                                //$xsumatotal = $hFila['6'];
                                $xsumatotal = number_format(($hFila['1'] + $hFila['3']) - $hFila[10],2) ;

                                $xcompra = number_format($hFila['11'],2);
                                $xgasto = number_format($hFila['12'],2);
                                $xsumaegreso = $hFila['11'] + $hFila['12'];

                                $usuario = $hFila['13'];
                                $fapertura = $hFila['14'];
                                $fcierre = $hFila['15'];

                                $totalDescuento =number_format($hFila[10],2);
                                $totalFinal = number_format(($hFila['4']-($hFila['11'] + $hFila['12']) - $hFila[10]),2);

                            $item++;
                          ?>
                            <tr>
                                <td><?php echo $item; ?></td>
                                <td><?php echo $xhabitacion; ?></td>
                                <td><?php echo $xproducto; ?></td>
                                <td><?php echo $xsumatotal; ?></td>
                                <td><?php echo $xvisa; ?></td>
                                <td><?php echo $xefectivo; ?></td>
                                <td><?php echo $xcompra; ?></td>
                                <td><?php echo $xgasto; ?></td>
                                <td><?php echo $xsumaegreso; ?></td>
                                <td><?php echo $totalDescuento; ?></td>
                                <td><?php echo $totalFinal; ?></td>
                                <td><?php echo $usuario; ?></td>
                                <td><?php echo $fapertura; ?></td>
                                <td><?php echo $fcierre; ?></td>
                                <td>
                                    <a href="#" onClick="ImprimirTurno(<?php echo $hFila['0'];?>); return false" class="btn btn-primary mb1 bg-blue">
                                      <i class="fa fa-print"></i> Resumen</a>
                                </td>
                                <td>
                                    <a href="#" onClick="ImprimirProducto(<?php echo $hFila['0'];?>); return false" class="btn btn-primary mb1 bg-green">
                                        <i class="fa fa-print"></i> Productos</a>
                                </td>
                                <td>
                                    <a href="reportePorTurno.php?idturno=<?php echo $hFila['0'];?>" class="btn btn-primary mb1 bg-red">
                                        <i class="fa fa-print"></i> Reporte</a>
                                </td>
                            </tr>
                           <?php
                              }
                          $sqlalquiler->free();
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

  function ImprimirOrden(idhabitacion,idalquiler){ 
        window.open("imprimir/print_alquiler-orden2.php?idhabitacion="+idhabitacion+"&idalquiler="+idalquiler,"modelo","width=1000, height=350, scrollbars=yes" );
  }

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
        "scrollX": true,
        "bDeferRender": true,
        //"sAjaxSource"   : "equipos_malogrados/listar_equipos/"+tipo_e,
        "aaSorting": [[ 0, 'asc' ]],
        "aoColumns": [
          
          { "sTitle": "#"},
          { "sTitle": "Ingreso de Habitaciones" },
          { "sTitle": "Ingreso de Productos/Servicios"},
          { "sTitle": "Total General" },
          { "sTitle": "Total Visa" },
            { "sTitle": "Total Efectivo" },
            { "sTitle": "Total Compras" },
            { "sTitle": "Total Gastos" },
            { "sTitle": "Total Egresos" },
            { "sTitle": "Total Descuento" },
            { "sTitle": "TOTAL EFECTIVO (INGRESO-GASTO-DESCUENTOS)" },
            { "sTitle": "Usuario" },
            { "sTitle": "Fecha Apertura" },
            { "sTitle": "Fecha Cierre" },
          { "sTitle": "Imprimir Resumen","sWidth": "70px" , "sClass": "center"},
          { "sTitle": "Imprimir Productos","sWidth": "70px" , "sClass": "center"},
          { "sTitle": "Reporte","sWidth": "70px" , "sClass": "center"}
          ],
         buttons: [
                    {
                        extend: 'print',
                        exportOptions: {
                          columns: [ 0,1,2,3,4,5,7,8,9,10,11,12,13]
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                          columns: [ 0,1,2,3,4,5,7,8,9,10,11,12,13]
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                          columns: [ 0,1,2,3,4,5,7,8,9,10,11,12,13]
                        }
                    }
          ]
      } );
  })
</script>
<script language="javascript">
    function objAjax(){
        var req = false;
        try{
            req = new XMLHttpRequest(); /* Para Firefox */
        }catch(error1){
            try{
                req = new ActiveXObject("Msxml2.XMLHTTP"); /* Algunas versiones de IE */
            }catch(error2){
                try{
                    req = new ActiveXObject("Microsoft.XMLHTTP"); /* Algunas versiones de IE */
                }catch(error3){
                    req = false;
                }
            }
        }
        return req;
    }

    var req = objAjax();

    function ImprimirTurno(idturno){
        window.open('imprimir/print_turno_rpte.php?idturno='+idturno,'modelo','width=500, height=500, scrollbars=yes' );
    }
    function ImprimirProducto(idturno){
        window.open('imprimir/print_producto.php?idturno='+idturno,'modelo','width=500, height=500, scrollbars=yes' );
    }

</script>



