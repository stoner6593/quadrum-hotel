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
      a.idalquiler,      
      a.nrohabitacion,           
      b.nombre,      
      b.ciudad,      
      (SELECT identificacion FROM tipo_identificacion tid WHERE tid.id = b.tipo_documento) as tipo_documento,
      b.documento,            
      a.comentarios,
      a.nroorden,      
      a.fecharegistro,
      a.documento,      
      a.codigo_respuesta,
      a.mensaje_respuesta,      
      a.nombrezip,
      a.nombre_archivo,      
      a.total,
      a.descuento,
        IFNULL(
        (SELECT sum(det.total) 
          FROM al_venta_detalle det 
          WHERE det.idalquiler=a.idalquiler and det.estadopago<>2),0)+
        IFNULL(
        (SELECT sum(IFNULL(ven.total,0)) 
          from venta ven 
          where ven.idalquiler=a.idalquiler ),0) as tot,
      a.iddocumento, 
      a.idhabitacion,
      a.anulado,
      a.anulado_motivo,
      CASE 
          WHEN a.anulado = 1 THEN 'ANULADO'
        ELSE ''
        END AS anulado_desc,
      'Alquiler' as tipoVentaDes,
      case when a.totalefectivo > 0 and a.totalvisa = 0 then 'EFECTIVO'
			    when a.totalefectivo = 0 and a.totalvisa > 0 then 'TARJETA'
          when a.totalefectivo > 0 and a.totalvisa > 0 then 'EFECTIVO/TARJETA'
          else '' end as tipo_pago,
      a.fechafin,
      a.fecharealsalida,
      b.RUC,
      b.razon_social
      
      from al_venta a
      inner join cliente b on b.idhuesped = a.idhuesped
      where a.estadoalquiler is not null 
      and a.iddocumento is not null and a.enviado is not null 
      ".$concatena."
      
      union
      
select
      a.idalquiler,      a.nrohabitacion,           
      d.nombre,      d.ciudad,      
      (SELECT identificacion FROM tipo_identificacion tid WHERE tid.id = d.tipo_documento) as tipo_documento,
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
      case when a.totalefectivo > 0 and a.totalvisa = 0 then 'EFECTIVO'
			    when a.totalefectivo = 0 and a.totalvisa > 0 then 'TARJETA'
                when a.totalefectivo > 0 and a.totalvisa > 0 then 'EFECTIVO/TARJETA'
                else '' end as tipo_pago,
      a.fechafin,
      a.fecharealsalida,
      d.RUC,
      d.razon_social
      
      from al_venta a
      inner join cliente d on d.idhuesped = a.idhuesped
      where a.codigo_respuesta = -1 and a.estadoalquiler is not null
      and a.iddocumento is null
      ".$concatena."      
  
      union

      select
      venta.idventa,  
      '-',           
      case when cliente.nombre is null then venta.cliente else cliente.nombre end nombre,
      cliente.ciudad,
      (SELECT identificacion FROM tipo_identificacion tid WHERE tid.id = cliente.tipo_documento) as tipo_documento,
      cliente.documento,
      
      venta.anotaciones,
      venta.numero,
      venta.fecha as fecharegistro,

      venta.documento,
      venta.codigo_respuesta,
      venta.mensaje_respuesta,
      venta.nombrezip,
      venta.nombre_archivo,
      venta.total,
      venta.descuento,
      (SELECT sum(det.importe) FROM ventadetalle det 
      WHERE det.idventa=venta.idventa) as tot,
      venta.iddocumento, 0,
      venta.anulado,
      venta.anulado_motivo,
      CASE 
        WHEN venta.anulado = 1 THEN 'ANULADO'
      ELSE ''
      END AS anulado_desc,
      'Venta Productos' as tipoVentaDes,
      case when venta.formapago = 1 then 'EFECTIVO'
			    when venta.formapago = 2 then 'TARJETA'
                else '' end as tipo_pago,
      '' as fechafin,
      '' as fecharealsalida,
      cliente.RUC,
      cliente.razon_social
      
      from venta 
      left join cliente on cliente.idhuesped = venta.idcliente
      where venta.codigo_respuesta = 0 
      and venta.iddocumento is not null ".$concatenaVenta."

order by fecharegistro DESC"); 
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Administrador</title>

<?php include "head-include.php"; ?>
<link href="datatable/css/buttons.dataTables.min.css" rel="stylesheet"> 
<link href="datatable/css/jquery.dataTables.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous"> 
<link href="basscss.min.css" rel="stylesheet">
</head>
<body>

<table width="100%" border="0" cellpadding="0" cellspacing="0" id="example1" class=" table table-bordered table-hover">

    <tr>
      <td height="25" colspan="3"><?php include ("head.php"); ?></td>
    </tr>
    <tr>
      <td width="185" height="25" align="left" valign="top"><?php include ("menu_nav.php"); ?></td>
      <td width="1">&nbsp;</td>
      <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
       
          
          <tr>
            <td height="30" colspan="2"><table width="100%" border="0" cellpadding="1" cellspacing="1">
             
                <tr>
                  <td height="30"><div class="lineahorizontal" style="background:#BFBFBF;"></div></td>
                </tr>
                <tr>
                  <td height="30" class=" text-success">
                    <h3 style="color:#E1583E;"> <i class="fa fa-list"></i> Listado de documentos histórico</h3>
                    
                  </td>
                </tr>
                <tr>
                  <td height="20">
                      <?php
                        if (isset($_SESSION['msgerror'])){ ?>
                          <div class="alert alert-success alert-dismissable textoContenidoMenor">
                            <?php
                            echo $_SESSION['msgerror'];
                            unset($_SESSION['msgerror']);
                            ?>
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                          </div>
                    <?php } ?>
                  </td>
              </tr>
              <tr>
                <td width="1%">
                    <hr/>
                  <form action="resumendiariohistorico.php" name="frmlistadocumentos" id="frmlistadocumentos" method="POST">
                    Fecha Inicio:<input name="finicio" value="<?php if ($finicio): echo $finicio; else: echo date("d/m/Y"); endif;?>" type="text" class="form-control" id="datepicker1" placeholder=" dd/mm/YYYY"  >
                    Fecha Fin:<input name="ffin" type="text" value="<?php if ($ffin): echo $ffin; else:  echo date("d/m/Y"); endif;?>" class="form-control" id="datepicker2" placeholder=" dd/mm/YYYY" >
                   <button type="button" class="btn btn-primary mb1 bg-blue" onClick="document.frmlistadocumentos.submit();"  style="border:0px; cursor:pointer;"> <i class="fas fa-search" style="font-size: 14px;"></i></button> 

                   <!-- <button id="ProcesaEnvio" class="btnrojo"><i class="fa fa-arrow-up" id="liAnula"></i> Enviar Sunat</button> 

                   <button id="Consulta" class="btnrojo"><i class="fa fa-arrow-up" id="liAnula"></i>Consultar Ticket</button>  -->
                  </form>   
                </td>
                
              </tr>
                    <tr>
                        <td width="1%">
                            <hr/>
                            <h4 class="panel-title">Descargar en Lote los archivos</h4>
                            <form class="form-horizontal" id="descargaFileZip" name="descargaFileZip" action="FE/generateFileZip.php" method="POST" role="form">
                                <center>
                                <div class="modal-body">

                                    <div class="form-group">
                                        <label for="inputUsername" class="col-sm-5 control-label">Estado Comprobantes:</label>
                                        <div class="col-sm-2">
                                            <div class="input-group">
                                                <select name="estadoCP" id="estadoCP" class="form-control">
                                                    <option value="TD">Todos</option>
                                                    <option value="ES">Solo enviados a Sunat</option>
                                                </select>
                                                <span class="input-group-addon"><i class="oi oi-file"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputUsername" class="col-sm-5 control-label">Tipo Archivo:</label>
                                        <div class="col-sm-2">
                                            <div class="input-group">
                                                <select name="typeFile" id="typeFile" class="form-control">
                                                    <option value="PDF">PDF</option>
                                                    <option value="XML">XML</option>
                                                    <option value="CDR">CDR</option>
                                                </select>
                                                <span class="input-group-addon"><i class="oi oi-file"></i></span>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="finiciodown"  id="finiciodown" value="<?php if ($finicio): echo $finicio; else: echo date("d/m/Y"); endif;?>">
                                    <input type="hidden" name="ffindown"  id="ffindown" value="<?php if ($ffin): echo $ffin; else:  echo date("d/m/Y"); endif;?>">

                                </div>
                                    <br/>
                                <div class="modal-footer">

                                        <?php
                                        if($sqlalquiler->num_rows>0) {
                                            ?>
                                            <button type="submit" class="btn btn-primary" id="submitBuscar"
                                                    name="submitBuscar">Descargar
                                            </button>
                                            <?php
                                        }else{
                                            ?>
                                            <button type="submit" class="btn btn-primary" id="submitBuscar"
                                                    name="submitBuscar" disabled>Descargar
                                            </button>
                                            <?php
                                        }
                                        ?>

                                </div>
                                </center>
                            </form>
                            <hr/>
                        </td>
                    </tr>
              
                <tr>
                  <td height="30">
                    

                    <table id="example" class="display wrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Position</th>
                                <th>Office</th>
                                <th>Office</th>
                                <th>Age</th>
                                <th>Start date</th>
                                <th>Start date</th>
                                <th>Salary</th>
                                <th>Start date</th>
                                <th>Salary</th>
                                <th>Tipo</th>
                                <th>Tipo</th>
                                <th>Tipo</th>
                                <th>Tipo</th>
                                <th>Tipo</th>
                                <th>Tipo</th>
                                <th>Tipo</th>
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
                              <td><?php echo $xhFila['4']; ?></td>
                              <td><?php echo $xhFila['5']; ?></td>
                                <td><?php echo $xhFila['1']; ?></td>
                                <td><?php echo $xhFila['0']+1000; ?></td>
                              <td><?php echo ($xhFila['9']); ?></td>
                              <td><?php echo $xhFila['26']; ?></td>
                              <td><?php echo $xhFila['27']; ?></td>
                              <td><?php echo $xhFila['8']; ?></td>
                              <td><?php echo $xhFila['24']; ?></td>
                              <td><?php echo $xhFila['25']; ?></td>
                              <td>S/. <?php echo $xhFila['16'] - $xhFila['15']; ?></td>
                                <td><?php echo $xhFila['23']; ?></td>
                              <td><?php echo $xhFila['21']; ?></td>
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
                              <td>
                                <?php
                                    if($xhFila['17'] === NULL){
                                ?>
                                      <img src="imagenesv/sign-ban-icon.png" width="25px" />
                                <?php 
                                    }else{
                                ?>
                                    <button type="button" class="btn btn-primary mb1 bg-green tooltip" tooltip="XML" data-id-xml="<?php echo $nombreXml;?>" id="xml" style="border:0px; cursor:pointer;"> <i class="fas fa-file-code" style="font-size: 14px;"></i></button>
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

      $( "#descargaFileZip" ).submit(function( event ) {
          //alert( "Handler for .submit() called." );
          $( "#div_loading" ).remove();
          //event.preventDefault();
          return true;
      });

    $("#ProcesaEnvio").on('click',function(e){
      e.preventDefault();
 
      swal({
        title: "Enviar Documento a SUNAT?",
        text: "Una vez enviado no se puede revertir el proceso..!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
        closeOnClickOutside: false,
        closeOnEsc: false
      })
      .then((willDelete) => {

        if (willDelete) {
          //Enviar proceso por ajax
           $.ajax({
                url:'FE/Generaxml.php',
                type:'post',
                data:{'idalquiler':0,'tipo_documento':0,'finicio': $("#datepicker1").val() , 'ffin': $("#datepicker1").val()},
                //dataType:'json',
                beforeSend:function(){
                   $('#ProcesaEnvio').attr('disabled','disabled');   
                   $("#liAnula").removeClass();
                   $("#liAnula").addClass('fa fa-spinner fa-spin');
                    swal('Enviando...!',{
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
                            nombre_archivo= data.success['nombre_archivo'] ;                                               
                            $("#liAnula").removeClass();
                            $("#liAnula").addClass('fa fa-arrow-up');
                            $('#ProcesaEnvio').removeAttr('disabled');    
                                            
                                        
                        }else{
                            if(typeof data.errors != "undefined"){
                                if(data.success==0){
                                    swal("Error!",data.errors['getCode']+' '+data.errors['getMessage'], "error");
                                    $("#liAnula").removeClass();
                                    $("#liAnula").addClass('fa fa-file-code-o');
                                    $('#ProcesaEnvio').removeAttr('disabled');     
                                    
                                    //window.location.href='control-habitaciones.php';
                                  
                                }else{
                                     if(data.success==2){

                                        swal("Error!",data.errors, "error");
                                        $("#liAnula").removeClass();
                                        $("#liAnula").addClass('fa fa-arrow-up');
                                        $('#ProcesaEnvio').removeAttr('disabled'); 
                                      
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
                $('#ProcesaEnvio').removeAttr('disabled');   
                 console.log(rpta);
                   
                    
                }

            });
          
        } else {
          swal("El proceso de envío fue cancelado..!!");
        }
      });
    })

      $( "#datepicker1" ).datepicker();
      $( "#datepicker2" ).datepicker();
      $('#example tbody ').on('click','#pdf',function(e){

          e.preventDefault();
          
          var cod=$(this).attr('data-id-pdf'); 
          swal("Buscando Archivo..! Por favor espere...", {
            buttons: false,
            closeOnEsc: false,
            timer: 2000,
            closeOnClickOutside: false
          });
          setTimeout(function(){
            $.get("FE/PDF/"+cod)
            .done(function() { 
                swal({
                 
                  text: "Documento encontrado!",
                  icon: "success",
                  buttons: {
                  cancel: false,
                  confirm: true,
                },
                  dangerMode: true

                })
                .then((willDelete) => {
                  if (willDelete) {
                                         
                      window.open("FE/PDF/"+cod);
                  }
                });

                $( "#div_loading" ).remove();
            }).fail(function(data) { 
               swal("Error!","Documento no se encuentra en nuestro servidor..!", "error");      
                console.log(data);
                // not exists code
                $( "#div_loading" ).remove();
            }) 
          }, 2000);  
          //swal("Error!",cod, "error");  
      })

     
        $('#example tbody ').on('click','#xml',function(e){

          e.preventDefault();
          
          var cod=$(this).attr('data-id-xml'); 
          swal("Buscando Archivo..! Por favor espere...", {
            buttons: false,
            closeOnEsc: false,
            timer: 2000,
            closeOnClickOutside: false
          });
          setTimeout(function(){
              console.log("FE/XMLFIRMADOS/"+cod);
            $.get("FE/XMLFIRMADOS/"+cod)
            .done(function() { 
                swal({
                 
                  text: "Documento encontrado!",
                  icon: "success",
                  buttons: {
                  cancel: false,
                  confirm: true,
                },
                  dangerMode: true

                })
                .then((willDelete) => {
                  if (willDelete) {
                                         
                      window.open("FE/XMLFIRMADOS/"+cod);
                  }
                });

                $( "#div_loading" ).remove();
            }).fail(function(data) { 
               swal("Error!","Documento no se encuentra en nuestro servidor..!", "error");      
                console.log(data);
                // not exists code
                $( "#div_loading" ).remove();
            }) 
          }, 2000);  
          //swal("Error!",cod, "error");  
      })



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
        "aaSorting": [[ 0, 'asc' ]],
        "aoColumns": [
          
          { "sTitle": "ID"},
          { "sTitle": "Cliente" },
          { "sTitle": "Tipo documento" },
          { "sTitle": "Documento" },
          { "sTitle": "Habitación" },
          { "sTitle": "No. Orden"},  
          { "sTitle": "Comprobante"},
          { "sTitle": "RUC"},
          { "sTitle": "Razón Social"},
          { "sTitle": "Fecha Inicio" },
          { "sTitle": "Fecha Fin" },
          { "sTitle": "Fecha Salida" },
          { "sTitle": "Monto" },
            { "sTitle": "Forma Pago" },
          { "sTitle": "Anulado","sWidth": "70px" , "sClass": "center"},
          { "sTitle": "PDF","sWidth": "70px" , "sClass": "center"},
          { "sTitle": "XML","sWidth": "70px" , "sClass": "center"},
         //  { "sTitle": "CDR","sWidth": "70px" , "sClass": "center"},
         
          ],
         buttons: [
                    {
                        extend: 'print',
                        exportOptions: {
                          columns: [ 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14]
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                          columns: [ 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14]
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                          columns: [ 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14]
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



