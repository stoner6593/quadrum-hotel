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
  $concatena='  DATE(fecha) between "'.$newfecha1.'" and "'.$newfecha2.'" ';
}else{
  $concatena='  DATE(fecha) between "'.date('Y-m-d').'" and "'.date('Y-m-d').'" ';
}





$sqlalquiler = $mysqli->query("
SELECT id_cb,    correlativo,    cod_estado_envio_sunat,
    mensaje_envio_sunat,    nombre_archivo_zip,
    nombre_archivo,    nombre_documento,
    fecha,    fecha_registro
FROM comunicacion_baja  where ".$concatena." 
order by fecha_registro desc");




?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Listado Comunicación Baja</title>

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
      <td width="25">&nbsp;</td>
      <td width="810" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
       
          
          <tr>
            <td height="30" colspan="2"><table width="100%" border="0" cellpadding="1" cellspacing="1">
             
                <tr>
                  <td height="30"><div class="lineahorizontal" style="background:#BFBFBF;"></div></td>
                </tr>
                <tr>
                  <td height="30" class=" text-success">
                    <h3 style="color:#E1583E;"> <i class="fa fa-users"></i> Listado de comunicación baja SUNAT</h3>
                    
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

                  <form action="listadocomunicacionbaja.php" name="frmlistadocumentos" id="frmlistadocumentos" method="POST">
                    Fecha Inicio:<input name="finicio" value="<?php if ($finicio): echo $finicio; else: echo date("d/m/Y"); endif;?>" type="text" class="form-control" id="datepicker1" placeholder=" dd/mm/YYYY"  >  Fecha Inicio:<input name="ffin" type="text" value="<?php if ($ffin): echo $ffin; else:  echo date("d/m/Y"); endif;?>" class="form-control" id="datepicker2" placeholder=" dd/mm/YYYY" >
                   <button type="button" class="btn btn-primary mb1 bg-blue" onClick="document.frmlistadocumentos.submit();"  style="border:0px; cursor:pointer;">
                       <i class="fas fa-search" style="font-size: 14px;"></i></button>

                   <!--<button id="ProcesaEnvio" class="btnrojo"><i class="fa fa-arrow-up" id="liAnula"></i> Enviar Sunat</button> -->

<!--                   <button id="Consulta" class="btnrojo"><i class="fa fa-arrow-up" id="liAnula"></i>Consultar Ticket</button> -->
                  </form>   
                </td>
                
              </tr> 
              
                <tr>
                  <td height="30">
                    

                    <table id="example" class="display nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>Correlativo</th>
                                <th>Position</th>
                                <th>Office</th>
                                <th>Age</th>
                                <th>Start date</th>
                                <th>Salary</th>
                                <!--  <th>Start date</th>
                                <th>Salary</th>-->
                                

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
                              <td><?php echo $xhFila['1']; ?></td>
                              <td><?php echo $xhFila['3']; ?></td>
                              <td><?php echo ($xhFila['7']); ?></td>
                              <td><?php echo $xhFila['8']; ?></td>
                              <td>
                                  <button type="button" class="btn btn-primary mb1 bg-green tooltip" tooltip="XML C.B."
                                          data-id-xml-cb="<?php echo $xhFila[6].'.xml';?>" id="xml_cb" style="border:0px; cursor:pointer;">
                                      <i class="fas fa-file-code" style="font-size: 14px;"></i></button>
                              </td>
                              <td>
                                  <button type="button" class="btn btn-primary mb1 bg-blue tooltip" tooltip="CDR C.B."
                                          data-id-cdr-cb="<?php echo $xhFila[4];?>" id="cdr_cb" style="border:0px; cursor:pointer;">
                                      <i class="fas fa-file-archive" style="font-size: 14px;"></i></button>
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

  $(function(){

      $( "#datepicker1" ).datepicker();
      $( "#datepicker2" ).datepicker();


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
          
          { "sTitle": "CORRELATIVO"},
          { "sTitle": "Mensaje" },
          { "sTitle": "Fecha"},
          { "sTitle": "Fecha Registro" },
          { "sTitle": "XML" },
          { "sTitle": "CDR","sWidth": "70px" , "sClass": "center"},
          //{ "sTitle": "XML","sWidth": "70px" , "sClass": "center"},
         //  { "sTitle": "CDR","sWidth": "70px" , "sClass": "center"},
         
          ],
         buttons: [
                    {
                        extend: 'print',
                        exportOptions: {
                          columns: [ 0,1,2,3]
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                          columns: [ 0,1,2,3 ]
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                          columns: [ 0,1,2,3 ]
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
                                 
                                  window.location.href='listadoresumendiario.php';
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



