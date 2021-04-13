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
  $concatenaVenta=' and DATE(venta.fecha) between "'.$newfecha1.'" and "'.$newfecha2.'" ';

  $sqlalquiler = $mysqli->query("call getRpte_resumen_cpcontabilidad('".$newfecha1."', '".$newfecha2."');");
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
}


//$sqlalquiler = $mysqli->query("call getRpte_resumen_cpcontabilidad('".$newfecha1."', '".$newfecha2."');");


?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Resumen Comprobante Contabilidad</title>

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
                    <h3 style="color:#E1583E;"> <i class="fa fa-users"></i> Resumen Contable</h3>

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

                  <form action="resumencontable.php" name="frmlistadocumentos" id="frmlistadocumentos" method="POST">
                    Fecha Inicio:<input name="finicio" value="<?php if ($finicio): echo $finicio; else: echo date("d/m/Y"); endif;?>" type="text" class="form-control" id="datepicker1" placeholder=" dd/mm/YYYY" readonly="true"/>
                    Fecha Fin:<input name="ffin" type="text" value="<?php if ($ffin): echo $ffin; else:  echo date("d/m/Y"); endif;?>" class="form-control" id="datepicker2" placeholder=" dd/mm/YYYY" readonly="true"/>
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
                                <th>Salary</th>
                                <th>Salary</th>
                            </tr>
                        </thead>
                        <tbody>
                          <?php if(isset($_POST['finicio'])){ ?>
                          <?php
                            $item = 0;
                            $nombreXml="";
                            while ($xhFila = $sqlalquiler->fetch_row())
                            {
                              $nombreXml=substr($xhFila['13'], 0,-4).'.xml';
                            $item++;
                          ?>
                            <tr>
                              <td><?php echo $xhFila['10']; ?></td>
                              <td><?php echo ($xhFila['25']); ?></td>
                              <td><?php echo $xhFila['9']; ?></td>
                              <td><?php echo $xhFila['12'] ?></td>
                              <td><?php echo $xhFila['5']; ?></td>
                              <td><?php echo $xhFila['2']; ?></td>
                              <td><?php echo $xhFila['26']; ?></td>
                              <td><?php echo $xhFila['27']; ?></td>
                              <td><?php echo $xhFila['28']; ?></td>
                              <td><?php echo $xhFila['17']; ?></td>
                              <td>
                                <?php
                                    if($xhFila['19'] === NULL){
                                ?>
                                    <a href="#" onClick="ImprimirOrden(<?php echo $xhFila['18'];?>,<?php echo $xhFila['0'];?>); return false" class="btnrojo">
                                      <i class="fa fa-print"></i> </a>
                                <?php
                                    }else{
                                ?>
                                  <button type="button" class="btn btn-primary mb1 bg-blue tooltip" tooltip="PDF" data-id-pdf="<?php echo $xhFila['14'].'.pdf';?>" id="pdf" style="border:0px; cursor:pointer;"> <i class="fa fa-file-pdf" style="font-size: 14px;"></i></button>
                                <?php
                                    }
                                ?>
                              </td>
                              <td>
                                <?php
                                    if($xhFila['19'] === NULL){
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
                          <?php }else{ ?>
                            <tr>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                            </tr>
                          <?php } ?>
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
        "scrollX": true,
        "bDeferRender": true,
        "pagingType": "full_numbers",
        //"sAjaxSource"   : "equipos_malogrados/listar_equipos/"+tipo_e,
        "aaSorting": [[ 0, 'asc' ]],
        "aoColumns": [

          { "sTitle": "Id"},
          { "sTitle": "Tipo Doc." },
          { "sTitle": "F. Emision"},
          { "sTitle": "Estado" },
          { "sTitle": "Num. Cliente" },
          { "sTitle": "Cliente" },
          { "sTitle": "Moneda" },
          { "sTitle": "Op. Gravadas" },
          { "sTitle": "IGV" },
          { "sTitle": "Total" },
          { "sTitle": "PDF","sWidth": "70px" , "sClass": "center"},
          { "sTitle": "XML","sWidth": "70px" , "sClass": "center"},
         //  { "sTitle": "CDR","sWidth": "70px" , "sClass": "center"},

          ],
         buttons: [
                    {
                        extend: 'print',
                        title: 'resumencontable_<?php echo $finicio; ?>-<?php echo $ffin; ?>',
                        exportOptions: {
                          columns: [ 0,1,2,3,4,5,6,7,8,9]
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'resumencontable_<?php echo $finicio; ?>-<?php echo $ffin; ?>',
                        exportOptions: {
                          columns: [ 0,1,2,3,4,5,6,7,8,9]
                        }
                    },
                    {
                        extend: 'pdf',
                        title: 'resumencontable_<?php echo $finicio; ?>-<?php echo $ffin; ?>',
                        exportOptions: {
                          columns: [ 0,1,2,3,4,5,6,7,8,9]
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
