<?php
include "validar.php";
include "config.php";
include "include/functions.php";
date_default_timezone_set('America/Lima');

$txtbuscarpor = @$_POST['txtbuscarpor'];
$txtdato = @$_POST['txtdato'];

$concatena='';

$finicio=@($_POST['finicio']);
$ffin=@($_POST['finicio']);
if($finicio){
$f1=explode("/",$finicio);
$newfecha1=$f1[2].'-'.$f1[1].'-'.$f1[0];
}

if($ffin){
$f2=explode("/",$ffin);
$newfecha2=$f2[2].'-'.$f2[1].'-'.$f2[0];
}


if($finicio && $ffin){
  $concatena=' and DATE(a.fecharegistro) between "'.$newfecha1.'" and "'.$newfecha2.'" ';
}else{

    $date = new DateTime();
    $finicio= $date->format('d/m/Y');
    $ffin= $date->format('d/m/Y');

    $f1=explode("/",$finicio);
    $newfecha1=$f1[2].'-'.$f1[1].'-'.$f1[0];

    $f2=explode("/",$ffin);
    $newfecha2=$f2[2].'-'.$f2[1].'-'.$f2[0];

    $concatena=' and DATE(a.fecharegistro) between "'.$newfecha1.'" and "'.$newfecha2.'" ';
}


if($finicio && $ffin){
  $concatenaVenta=' and DATE(venta.fecha) between "'.$newfecha1.'" and "'.$newfecha2.'" ';
}else{

    $date = new DateTime();
    $finicio= $date->format('d/m/Y');
    $ffin= $date->format('d/m/Y');

    $f1=explode("/",$finicio);
    $newfecha1=$f1[2].'-'.$f1[1].'-'.$f1[0];

    $f2=explode("/",$ffin);
    $newfecha2=$f2[2].'-'.$f2[1].'-'.$f2[0];

    $concatena=' and DATE(venta.fecha) between "'.$newfecha1.'" and "'.$newfecha2.'" ';
}


$sqlalquiler = $mysqli->query("select
      a.idalquiler,      a.nrohabitacion,           
      b.nombre,      b.ciudad,      b.tipo_documento,
      b.documento,            a.comentarios,
      a.nroorden,      a.fecharegistro,
      a.documento,      a.codigo_respuesta,
      a.mensaje_respuesta,      a.nombrezip,
      a.nombre_archivo,      a.total,
      a.descuento,
      IFNULL((SELECT sum(det.total) 
      FROM al_venta_detalle det 
      WHERE det.idalquiler=a.idalquiler and det.estadopago<>2),0)+
      IFNULL((SELECT sum(IFNULL(ven.total,0)) from venta ven where ven.idalquiler=a.idalquiler ),0) as tot,
      a.iddocumento, a.idhabitacion,
      a.anulado,
      a.anulado_motivo,
      CASE 
        WHEN a.anulado = 1 THEN 'ANULADO'
      ELSE ''
      END AS anulado_desc,
      'Alquiler' as tipoVentaDes,
      IFNULL((SELECT sum(det.total) 
      FROM al_venta_detalle det 
      WHERE det.idalquiler=a.idalquiler and det.estadopago=2),0) as total_anulado,
      (SELECT GROUP_CONCAT(concat('- ',
      case when det.tipoalquiler = 3 then 'Hora Adicional'
            when det.tipoalquiler = 4 then 'Huesped Adicional'
            when det.tipoalquiler = 5 then 'Ingreso Anticipado'
            else 'Alquiler' end
      ,' ','(',det.cantidad,') ',DATE_FORMAT(det.fechadesde, '%d/%m/%Y' ),' - ',
      DATE_FORMAT(det.fechahasta, '%d/%m/%Y' ),'  S/ ',det.total) SEPARATOR '\n<br/>') 
      FROM al_venta_detalle det 
      WHERE det.idalquiler=a.idalquiler and det.estadopago<>2) as group_det_anulado,
      motivoanulacion,
      c.user_nombre,
      case when d.turno = '1' then 'DIA' else 'NOCHE' end as turno
      from al_venta a
      inner join cliente b on b.idhuesped = a.idhuesped
      inner join usuario c on c.user_id = a.idusuario
      left join ingresosturno d on d.idturno = a.idturno
      where a.estadoalquiler is not null 
      and a.iddocumento is not null and a.enviado is not null 
      and a.anulaporusuario=1
      ".$concatena."
      
      
      union
      
select
      a.idalquiler,      a.nrohabitacion,           
      d.nombre,      d.ciudad,      d.tipo_documento,
      d.documento,            a.comentarios,
      a.nroorden,      a.fecharegistro,
      CONCAT('TK-','',a.nroorden),      a.codigo_respuesta,
      a.mensaje_respuesta,      a.nombrezip,
      a.nombre_archivo,      a.total,
      a.descuento,
      IFNULL((SELECT sum(det.total) 
      FROM al_venta_detalle det 
      WHERE det.idalquiler=a.idalquiler and det.estadopago<>2),0)+
      IFNULL((SELECT sum(IFNULL(ven.total,0)) from venta ven where ven.idalquiler=a.idalquiler ),0) as tot,
      a.iddocumento, a.idhabitacion,
      a.anulado,
      a.anulado_motivo,
      CASE 
        WHEN a.anulado = 1 THEN 'ANULADO'
      ELSE ''
      END AS anulado_desc,
      'Alquiler' as tipoVentaDes,
      IFNULL((SELECT sum(det.total) 
      FROM al_venta_detalle det 
      WHERE det.idalquiler=a.idalquiler and det.estadopago=2),0) as total_anulado,
      (SELECT GROUP_CONCAT(concat('- ',
      case when det.tipoalquiler = 3 then 'Hora Adicional'
            when det.tipoalquiler = 4 then 'Huesped Adicional'
            when det.tipoalquiler = 5 then 'Ingreso Anticipado'
            else 'Alquiler' end
      ,' ','(',det.cantidad,') ',DATE_FORMAT(det.fechadesde, '%d/%m/%Y' ),' - ',
      DATE_FORMAT(det.fechahasta, '%d/%m/%Y' ),'  S/ ',det.total) SEPARATOR '\n<br/>') 
      FROM al_venta_detalle det 
      WHERE det.idalquiler=a.idalquiler and det.estadopago<>2) as group_det_anulado,
      motivoanulacion,
      c.user_nombre,
      case when x.turno = '1' then 'DIA' else 'NOCHE' end as turno
      from al_venta a
      inner join cliente d on d.idhuesped = a.idhuesped
      inner join usuario c on c.user_id = a.idusuario
      left join ingresosturno x on x.idturno = a.idturno
      where a.codigo_respuesta = -1 and a.estadoalquiler is not null
      and a.iddocumento is null
      and a.anulaporusuario=1
      ".$concatena."      
  
order by fecharegistro DESC"); 
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Listado de alquileres con detalle de Anulados</title>

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
      <td width="810" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
       
          
          <tr>
            <td height="30" colspan="2"><table width="100%" border="0" cellpadding="1" cellspacing="1">
             
                <tr>
                  <td height="30"><div class="lineahorizontal" style="background:#BFBFBF;"></div></td>
                </tr>
                <tr>
                  <td height="30" class=" text-success">
                    <h3 style="color:#E1583E;"> <i class="fa fa-users"></i> Listado de alquileres Anulados</h3>
                    
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

                  <form action="resumenAlquileresAnuladosPrincipal.php" name="frmlistadocumentos" id="frmlistadocumentos" method="POST">
                    Fecha Inicio:<input name="finicio" value="<?php if ($finicio): echo $finicio; else: echo date("d/m/Y"); endif;?>" type="text" class="form-control" id="datepicker1" placeholder=" dd/mm/YYYY"  >  Fecha Inicio:<input name="ffin" type="text" value="<?php if ($ffin): echo $ffin; else:  echo date("d/m/Y"); endif;?>" class="form-control" id="datepicker2" placeholder=" dd/mm/YYYY" >
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
                                <th>#</th>
                                <th>Position</th>
                                <th>Office</th>
                                <th>Age</th>
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
                            $nombreXml="";
                            while ($xhFila = $sqlalquiler->fetch_row())
                            {   
                              $nombreXml=substr($xhFila['12'], 0,-4).'.xml';
                            $item++;
                          ?>
                            <tr>
                              <td><?php echo $item; ?></td>
                              <td><?php echo $xhFila['2']; ?></td>
                              <td><?php echo ($xhFila['9']); ?></td>
                              <td><?php echo $xhFila['8']; ?></td>
                              <td>S/. <?php echo $xhFila['16'] - $xhFila['15']; ?></td>                              
                              <td><?php echo $xhFila['24']; ?></td>
                              <td><?php echo $xhFila['25']; ?></td>
                              <td><?php echo $xhFila['26']; ?></td>
                              <td><?php echo $xhFila['27']; ?></td>
                              <td>
                                <?php
                                    if($xhFila['17'] === NULL){
                                ?>
                                    <a href="#" onClick="ImprimirOrden(<?php echo $xhFila['18'];?>,<?php echo $xhFila['0'];?>); return false" class="btnrojo">
                                      <i class="fa fa-print"></i> </a> 
                                <?php 
                                    }else{
                                ?>
                                  <button type="button" class="btn btn-primary mb1 bg-blue tooltip" tooltip="PDF" data-id-pdf="<?php echo $xhFila['13'].'.pdf';?>" id="pdf" style="border:0px; cursor:pointer;"> <i class="fa fa-file-pdf" style="font-size: 14px;"></i></button>
                                <?php 
                                    }
                                ?>
                              </td>
                              <!--<td><button type="button" class="btn btn-primary mb1 bg-maroon tooltip" tooltip="CDR" data-id-cdr="<?php echo "R-".$xhFila['12'];?>" id="cdr" style="border:0px; cursor:pointer;"> <i class="fas fa-file-archive" style="font-size: 14px;"></i></button></td>-->
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
        "bDeferRender": true,
        "scrollX": true,
        //"sAjaxSource"   : "equipos_malogrados/listar_equipos/"+tipo_e,
        "aaSorting": [[ 0, 'asc' ]],
        "aoColumns": [
          
          { "sTitle": "ID"},
          { "sTitle": "Cliente" },
          { "sTitle": "Documento"},
          { "sTitle": "Fecha" },
          { "sTitle": "Monto" },
          { "sTitle": "Detalle Anulado" },
          { "sTitle": "Motivo" },
          { "sTitle": "Usuario" },
          { "sTitle": "Turno" },
          { "sTitle": "Orden","sWidth": "70px" , "sClass": "center"}
         //  { "sTitle": "CDR","sWidth": "70px" , "sClass": "center"},
         
          ],
         buttons: [
                    {
                        extend: 'print',
                        exportOptions: {
                          columns: [ 0,1,2,3,4,5,6,7,8]
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                          columns: [ 0,1,2,3,4,5,6,7,8]
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                          columns: [ 0,1,2,3,4,5,8]
                        }
                    }
          ]
      } );


      $("#Consulta").on("click",function(e){
        e.preventDefault();

        /*if(nombre_archivo==""){
          swal("Error!","No existe archivo para consultar");
          return;
        }*/

        swal("Pegar el Nº de Ticket aquí:", {
          content: "input",
        })
        .then((value) => {
          //swal(`You typed: ${value}`);
          var dato=`${value}`;
          $.ajax({
                url:'FE/ConsultaResumen.php',
                type:'post',
                data:{'ticket':dato},
                //dataType:'json',
                beforeSend:function(){
                   $('#Consulta').attr('disabled','disabled');   
                   $("#liAnula").removeClass();
                   $("#liAnula").addClass('fa fa-spinner fa-spin');
                    swal('Ticket Consultado...!',{
                      closeOnClickOutside: false,
                      closeOnEsc: false,
                       buttons: false
                  });
                  
                },  
                success:function(data){
                    
                   data = eval("("+data+")");  
                   console.log(data);
                    if(typeof data.success != "undefined"){                     
                        if(data.errors==0){                             
                          
                         
                           swal({
                              title: "Enviado...!",
                              text: data.success['Description'] +": "+data.success['codRespuesta'],
                              icon: "success",
                              buttons: true,
                              dangerMode: true

                            })
                            .then((willDelete) => {
                              if (willDelete) {
                                  swal("Transacción Finalizada!", {
                                    icon: "success",
                                  });
                                 
                                  window.location.href='resumendiario.php';
                              } 
                            });
                                                                               
                            $("#liAnula").removeClass();
                            $("#liAnula").addClass('fa fa-arrow-up');
                            $('#Consulta').removeAttr('disabled');    
                                            
                                        
                        }else{
                            if(typeof data.errors != "undefined"){
                                if(data.success==0){
                                    swal("Error!",data.errors['getCode']+' '+data.errors['getMessage'], "error");
                                    $("#liAnula").removeClass();
                                    $("#liAnula").addClass('fa fa-file-code-o');
                                    $('#Consulta').removeAttr('disabled');     
                                    
                                    //window.location.href='control-habitaciones.php';
                                  
                                }else{
                                     if(data.success==2){

                                        swal("Error!",data.errors, "error");
                                        $("#liAnula").removeClass();
                                        $("#liAnula").addClass('fa fa-arrow-up');
                                        $('#Consulta').removeAttr('disabled'); 
                                      
                                        //window.location.href='control-habitaciones.php';
                                    }
                                }
                               
                            }
                        }
                    }
                                   
                    
                },
                error:function(rpta){ 
                 swal("Error!","Ocurió un Error al Realizar Petición..!", "error");                  
                 $("#liAnula").removeClass();
                 $("#liAnula").addClass('fa fa-arrow-up');
                $('#Consulta').removeAttr('disabled');   
                 console.log(rpta);
                   
                    
                }

            });

        });
      })
   
  })
</script>



