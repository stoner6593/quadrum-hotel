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


if($finicio && $ffin){
  $concatena=' and DATE(a.fechaemision) between "'.$newfecha1.'" and "'.$newfecha2.'" ';
}else{

    $date = new DateTime();
    $finicio= $date->format('d/m/Y');
    $ffin= $date->format('d/m/Y');

    $f1=explode("/",$finicio);
    $newfecha1=$f1[2].'-'.$f1[1].'-'.$f1[0];

    $f2=explode("/",$ffin);
    $newfecha2=$f2[2].'-'.$f2[1].'-'.$f2[0];

    $concatena=' and DATE(a.fechaemision) between "'.$newfecha1.'" and "'.$newfecha2.'" ';
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
      a.nroorden,      a.fechaemision,
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
      'Venta' as tipoVentaDes,
      '1' as tipo,
      a.id_cb,
      cb.cod_estado_envio_sunat,
      cb.nombre_archivo_zip,
      cb.nombre_documento
      
      from al_venta a
      inner join cliente b on b.idhuesped = a.idhuesped
      left join comunicacion_baja cb on cb.id_cb = a.id_cb
      where a.codigo_respuesta = 0
      and a.iddocumento is not null and a.enviado is not null 
      ".$concatena."
        
      union

      select
      venta.idventa,  
      '-',           
      case when cliente.nombre is null then venta.cliente else cliente.nombre end nombre,
      cliente.ciudad,
      cliente.tipo_documento,
      cliente.documento,
      
      venta.anotaciones,
      venta.numero,
      venta.fecha as fechaemision,

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
      '2' as tipo,
      venta.id_cb,
      cb.cod_estado_envio_sunat,
      cb.nombre_archivo_zip,
      cb.nombre_documento
      
      from venta 
      left join cliente on cliente.idhuesped = venta.idcliente
      left join comunicacion_baja cb on cb.id_cb = venta.id_cb
      where venta.codigo_respuesta = 0 
      and venta.iddocumento is not null ".$concatenaVenta."

order by fechaemision DESC");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Administrador</title>

<?php include "head-include.php"; ?>
<link href="datatable/css/buttons.dataTables.min.css" rel="stylesheet">
<link href="datatable/css/jquery.dataTables.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css"
      integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
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
      <td width="1">&nbsp;</td>
      <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
       
          
          <tr>
            <td height="30" colspan="2"><table width="100%" border="0" cellpadding="1" cellspacing="1">
             
                <tr>
                  <td height="30"><div class="lineahorizontal" style="background:#BFBFBF;"></div></td>
                </tr>
                <tr>
                  <td height="30" class=" text-success">
                    <h3 style="color:#1894ff;"> <i class="fa fa-list"></i> Listado de documentos enviados a sunat - Comunicación de Baja</h3>
                    
                  </td>
                </tr>
                <tr>
                  <td height="20">
                      <?php
                        if (isset($_SESSION['msgerror'])){ ?>
                          <div class="alert alert-danger alert-dismissable textoContenidoMenor">
                            <?php
                            echo $_SESSION['msgerror'];
                            unset($_SESSION['msgerror']);
                            ?>
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                          </div>
                    <?php }else{

                          if (isset($_SESSION['msgsuccess'])){?>
                                <div class="alert alert-success alert-dismissable textoContenidoMenor">
                                    <?php
                                    echo $_SESSION['msgsuccess'];
                                    unset($_SESSION['msgsuccess']);
                                    ?>
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                  </div>
                              <?php
                          }

                        } ?>
                  </td>
              </tr>
              <tr>
                <td width="1%">
                    <hr/>
                  <form action="comunicacionBaja.php" name="frmlistadocumentos" id="frmlistadocumentos" method="POST">
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
                    

                    <table id="example" class="display wrap" style="width:100%">
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
                                <th>Tipo</th>
                                <th>Baja</th>
                                <th>Motivo</th>
                                <th>XML C.B.</th>
                                <th>CDR C.B.</th>
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
                              <td><?php echo $xhFila['21']; ?></td>
                              <td><?php echo $xhFila['22']; ?></td>
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
                                <td>
                                    <?php
                                    if($xhFila['19'] == 1){
                                        ?>
                                        <img src="imagenesv/sign-ban-icon.png" width="25px" />
                                        <?php
                                    }else{
                                        if($xhFila['17'] == '2') {
                                            if($xhFila['24'] == '0') {
                                                ?>
                                                <button type="button" class="btn btn-primary mb1 bg-red tooltip"
                                                        tooltip="Comunicacion Baja"
                                                        data-id-cb="<?php echo $xhFila['0']; ?>"
                                                        data-id-cb-doc="<?php echo $xhFila['9']; ?>"
                                                        data-id-cb-cliente="<?php echo $xhFila['2']; ?>"
                                                        data-id-cb-tipo="<?php echo $xhFila['23']; ?>"
                                                        id="comuBaja" style="border:0px; cursor:pointer;">
                                                    <i class="fas fa-level-down-alt" style="font-size: 14px;"></i>
                                                </button>
                                                <?php
                                            }else{
                                                ?>
                                                <a type="button" class="btn btn-primary mb1 bg-yellow"
                                                        style="border:0px; cursor:pointer;"
                                                        href="#" title="Comunicacion Baja Registrado">
                                                    <i class="fas fa-check-circle" style="font-size: 14px;"></i>
                                                </a>
                                                <?php
                                            }
                                        }else{
                                            ?>
                                            <img src="imagenesv/sign-ban-icon.png" width="25px" />
                                            <?php
                                        }
                                    }
                                    ?>
                                </td>
                                <td><?php echo $xhFila['20']; ?></td>
                                <td>
                                    <?php
                                    if($xhFila['24'] == 0 || $xhFila['24'] == -1){
                                        ?>
                                        <img src="imagenesv/sign-ban-icon.png" width="25px" />
                                        <?php
                                    }else{
                                        ?>
                                        <button type="button" class="btn btn-primary mb1 bg-green tooltip" tooltip="XML C.B."
                                                data-id-xml-cb="<?php echo $xhFila[27].'.xml';?>" id="xml_cb" style="border:0px; cursor:pointer;">
                                            <i class="fas fa-file-code" style="font-size: 14px;"></i></button>
                                        <?php
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if($xhFila['24'] == 0 || $xhFila['24'] == -1){
                                        ?>
                                        <img src="imagenesv/sign-ban-icon.png" width="25px" />
                                        <?php
                                    }else{
                                        ?>
                                        <button type="button" class="btn btn-primary mb1 bg-blue tooltip" tooltip="CDR C.B."
                                                data-id-cdr-cb="<?php echo $xhFila[26];?>" id="cdr_cb" style="border:0px; cursor:pointer;">
                                            <i class="fas fa-file-archive" style="font-size: 14px;"></i></button>
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

<!-- Button HTML (to Trigger Modal) -->
<!--<a href="#myModal" class="btn btn-lg btn-primary" data-toggle="modal">Launch Demo Modal</a>-->

<!-- Modal HTML -->
<div id="myModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="modal-header-h3">Comunicación de Baja</h4>
            </div>
            <form action="FE/ComunicacionBaja.php" name="frmcb" id="frmcb" method="POST">
            <div class="modal-body">
                <input type="hidden" name="idVenta"  id="idVenta" value="">
                <input type="hidden" name="tipoVenta"  id="tipoVenta" value="">
                <div class="form-group">
                    <label for="inputUsername" class="col-sm-4 control-label bold">Documento:</label>
                    <div class="col-sm-2">
                        <div class="input-group">
                            <input name="txtDocumento" autocomplete="false" readonly type="text" class="form-control" id="txtDocumento">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputUsername" class="col-sm-4 control-label bold">Cliente:</label>
                    <div class="col-sm-2">
                        <div class="input-group">
                            <input name="txtCliente" autocomplete="false" readonly type="text" class="form-control" id="txtCliente" style="width: 400px;"/>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputUsername" class="col-sm-4 control-label bold">Motivo:</label>
                    <div class="col-sm-2">
                        <div class="input-group">
                            <textarea name="Motivo" class="form-control" id="Motivo" style="width:90%;text-transform: uppercase;"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default custom-close" data-dismiss="modal">Cerrar</button>
<!--                <button type="button" class="btn btn-primary">Enviar</button>-->
                <button type="submit" form="frmcb" value="Submit" class="btn btn-primary">Enviar</button>
            </div>
            </form>
        </div>
    </div>
</div>


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
      });

     
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
      });

      $('#example tbody ').on('click','#xml_cb',function(e){

          e.preventDefault();

          var cod=$(this).attr('data-id-xml-cb');
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
      });

      $('#example tbody ').on('click','#cdr_cb',function(e){

          e.preventDefault();

          var cod=$(this).attr('data-id-cdr-cb');
          swal("Buscando Archivo..! Por favor espere...", {
              buttons: false,
              closeOnEsc: false,
              timer: 2000,
              closeOnClickOutside: false
          });
          setTimeout(function(){
              $.get("FE/CDR/"+cod)
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

                                  window.open("FE/cdr/"+cod);
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
      });

      $('#example tbody ').on('click','#comuBaja',function(e){

          e.preventDefault();

          $( "#div_loading" ).remove();

          var idVenta=$(this).attr('data-id-cb');
          var documento=$(this).attr('data-id-cb-doc');
          var cliente=$(this).attr('data-id-cb-cliente');
          var tipo=$(this).attr('data-id-cb-tipo');

          $('#idVenta').val(idVenta);
          $('#tipoVenta').val(tipo);
          $('#txtDocumento').val(documento);
          $('#txtCliente').val(cliente);

          $('#myModal').modal('show');

      });

      $(".custom-close").on('click', function() {
          $( "#div_loading" ).remove();
          $( "#div_loading" ).remove();
      });

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
        //"sAjaxSource"   : "equipos_malogrados/listar_equipos/"+tipo_e,
        "aaSorting": [[ 0, 'asc' ]],
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
          { "sTitle": "Baja","sWidth": "70px" , "sClass": "center"},
            { "sTitle": "Motivo","sWidth": "70px" , "sClass": "center"},
            { "sTitle": "XML C.B.","sWidth": "70px" , "sClass": "center"},
            { "sTitle": "CDR C.B.","sWidth": "70px" , "sClass": "center"},
          ],
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
                          columns: [ 0,1,2,3,4,5,6]
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



