<?php
include "validar.php";
include "config.php";
include "include/functions.php";
date_default_timezone_set('America/Lima');

$xidventa = $_GET['xidventa'];
$xguardado = $_GET['guardado'];

$detalle = @$_GET['detalle'];

//Recuperar Datos para Mostrar
$sqlventa = $mysqli->query("select
	idventa,
	numero,
	cliente,
	fecha,
	hora,
	total,
	operacion,
	formapago,
	estado,
	estadoturno,
	idusuario,
	anotaciones
	
	from venta where idventa = '$xidventa'");
	$vFila = $sqlventa->fetch_row();
	
	$sqldetalle = $mysqli->query("select idventadetalle, idventa, idproducto, nombre, cantidad, precio, importe from ventadetalle where idventa = '$xidventa' order by idventadetalle asc");

  $serie=$mysqli->query("SELECT * FROM series WHERE estado=1 and iddocumento in(1,2)");
	
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Administrador</title>
<link href="opera.css" rel="stylesheet" type="text/css">

<?php include "head-include.php"; ?>

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
    function ImprimirVentaOrden(){
        window.open('imprimir/print_venta-orden.php?xidventa=<?php echo $xidventa;?>','modelo','width=350, height=500, scrollbars=yes' );
    } 
</script>

</head>
<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
      <td height="25" colspan="3"><?php include ("head.php"); ?></td>
    </tr>
    <tr>
      <td width="230" height="25" align="left" valign="top"><?php include ("menu_nav.php"); ?></td>
      <td width="21">&nbsp;</td>
      <td width="1125" valign="top"><table width="904" border="0" cellpadding="0" cellspacing="0">
        <tbody>
          <tr>
            <td width="729" height="30"><h3 style="color:#E1583E;"> <i class="fa fa-shopping-basket"></i> Venta  Productos</h3></td>
            <td width="175" align="right" valign="middle">
			      <?php 
              if($detalle==1){
            ?>            
                <button type="button" onclick="window.location.href='venta-listado.php';" class="btngris" style="border:0px; cursor:pointer;"> <i class="fa fa-arrow-left"></i> Volver </button>
            <?php 
              } 
            ?>
            </td>
          </tr>
          <tr>
            <td height="30" colspan="2">  <div class="lineahorizontal" style="background:#EFEFEF;"></div> </td>
          </tr>
          <tr>
            <td height="30" colspan="2"><table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
              <tbody>
                <tr>
                  <td height="25" colspan="2"> <h2> <span style="color:#00A230;"> 
                    <?php 
                        if($xguardado==1){ 
                          echo 'Venta Completada!'; 
                        }else{
                          echo 'Detalle de Ticket!';
                        }
                    ?></span> </h2> </td>
                  <td height="25" colspan="2" align="center"><h3>Número: <strong><?php echo $vFila['1'];?></strong></h3></td>
                  </tr>
                <tr>
                  <td height="25" colspan="4"><table width="600" border="0" cellpadding="4" cellspacing="1" bgcolor="#E0E0E0">
                    <tr class="textoContenidoMenor">
                      <td width="329" height="25" bgcolor="#F4F4F4">Producto</td>
                      <td width="49" height="25" bgcolor="#F4F4F4">Cantidad</td>
                      <td width="85" height="25" align="right" bgcolor="#F4F4F4">P. Unitario (S/)</td>
                      <td width="100" height="25" align="right" bgcolor="#F4F4F4">Importe (S/)</td>
                      </tr>
                    <?php $xtotal = 0; while($tmpFila = $sqldetalle->fetch_row()){?>
                    <tr class="textoContenidoMenor">
                      <td height="25" bgcolor="#FFFFFF"><?php echo $tmpFila['3']; ?></td>
                      <td height="25" align="center" bgcolor="#FFFFFF"><?php echo $tmpFila['4']; ?></td>
                      <td height="25" align="right" bgcolor="#FFFFFF"><?php echo $tmpFila['5']; ?></td>
                      <td height="25" align="right" bgcolor="#FFFFFF"><?php echo number_format($tmpFila['6'],2); ?></td>
                      </tr>
                    <?php $xtotal = $xtotal + $tmpFila['5']; } ?>
                    </table></td>
                </tr>
                <tr>
                  <td width="224" height="25">&nbsp;</td>
                  <td width="188" height="25">&nbsp;</td>
                  <td width="193" height="25">&nbsp;</td>
                  <td width="193" height="25" class="textoContenido">&nbsp;</td>
                  </tr>
                <tr>
                  <td height="25" class="textoContenido">Tipo de Operación</td>
                  <td height="25" class="textoContenido">Forma de Pago</td>
                  <td height="25"><span class="textoContenido">Estado </span></td>
                  <td height="25" class="textoContenido">Total Pagado </td>
                  </tr>
                <tr>
                  <td height="25" class="textoContenido"><strong><?php echo tipoOperacion($vFila['6']);?></strong></td>
                  <td height="25" class="textoContenido"><strong><?php echo formaPago($vFila['7']);?></strong></td>
                  <td height="25" align="right" class="textoContenido"><strong><?php echo estadoPago($vFila['8']);?></strong></td>
                  <td height="25" class="textoContenido"><strong>S/ <?php echo number_format($vFila['5'],2);?></strong></td>
                  </tr>
                <tr>
                  <td height="25" colspan="4"><div class="lineahorizontal" style="background:#EFEFEF;"></div></td>
                  </tr>
                <tr>
                  <td height="30" class="textoContenido">Cliente</td>
                  <td height="30">&nbsp;</td>
                  <td height="30"><span class="textoContenido">Fecha / Hora</span></td>
                  <td>&nbsp;</td>
                  </tr>
                <tr>
                  <td height="30" colspan="2"><strong><span class="textoContenido"><?php echo $vFila['2'];?></span></strong></td>
                  <td height="30" class="textoContenido"><strong><?php echo Cfecha($vFila['3']);?></strong>  <br></td>
                  <td class="textoContenido"><?php echo $vFila['4'];?></td>
                  </tr>
                <tr>
                  <td height="25" colspan="4">
                  <?php if($vFila['8']==0){?>
                  <p class="textoContenido"> <span style="color:#E1583E;"> Motivo de Anulación: </span> <?php echo $vFila['11'];?> </p>
                  <?php } ?>
                  <div class="lineahorizontal" style="background:#EFEFEF;"></div>
                  </td>
                  </tr>
                <tr>
                  <td height="25" colspan="2"> 

                    <a href="#" onClick="return ImprimirVentaOrden();" class="btnrojo"> <i class="fa fa-print"></i> Imprimir </a>
                                       
                    <?php 
                        if($detalle != 1){
                    ?>
                    	<?php 
              						$xidalquiler = $_GET['idalquiler'];
              						$xidhabitacion = $_GET['idhabitacion'];	
              						if($xidalquiler != "" && $xidalquiler != 0){ 
                      ?>
						                    <button type="button" onclick="window.location.href='alquilar-detalle.php?idalquiler=<?php echo $xidalquiler.'&idhabitacion='.$xidhabitacion; ?>';" class="btnnegro" style="border:0px; cursor:pointer;"> <i class="fa fa-remove"></i> Salir </button>                        
                    	<?php 
                          }else{ 
                      ?>    
                              	<button type="button" onclick="window.location.href='venta.php';" class="btnnegro" style="border:0px; cursor:pointer;"> <i class="fa fa-remove"></i> Salir </button>
                              
                      <?php 
          						    }
						            } 
					           ?>
                    
                    </td>
                    <td>
                        <select name="serie" id="series" class="select">
                          <option value=""></option>
                            <?php 
                              while ($tmpSeries = $serie->fetch_row()){ $num++; 
                            ?>                                    
                                <option value="<?php echo $tmpSeries[1];?>"><strong><?php echo $tmpSeries[2];?></strong></option>
                            <?php 
                              }
                            ?>
                        </select>
                    </td>
                    <td>                                  
                        <button id="ProcesaEnvio" class="btnrojo"><i class="fa fa-arrow-up" id="liAnula"></i> Enviar Sunat</button> 
                    </td>
                  <!-- <td height="25">&nbsp;</td>
                  <td height="25">&nbsp;</td> -->
                </tr>
                </tbody>
              </table></td>
          </tr>
        </tbody>
      </table></td>
    </tr>
    <tr>
      <td height="25" colspan="3"></td>
    </tr>
  </tbody>
</table>

</body>
</html>
<?php include ("footer.php") ?>

<script type="text/javascript">
  
  $(function(){

    

    $("#ProcesaEnvio").on('click',function(e){
      e.preventDefault();
      if($("#series").val()==""){
        swal("Advertencia!",'Seleccione Tipo de Documento a Enviar..!', "warning");
        $("#series").focus();
        return;
      }
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

            $url = "";

            if($("#series").val() == "01"){
                $url = "FE/EnviarDocumento_2_1.php";
            }else{
                $url = "FE/Generaxml.php";
            }

          //Enviar proceso por ajax
          $.ajax({
                url: $url,
                type:'post',
                data:{'idalquiler':<?php echo $xidventa; ?>,'tipo_documento':$("#series").val(),'tipo_servicio':'VE'},
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
                   //console.log(data);
                    if(typeof data.success != "undefined"){                     
                        if(data.errors==0){                             
                          
                          window.open('FE/PDF/'+data.success['nombre_archivo']);

                            if($("#series").val() == "01"){

                                $.ajax({
                                    url:'include/backupNube/registro_data_nube_por_doc.php',
                                    type:'post',
                                    data:{'idalquiler':<?php echo $xidventa; ?>,'tipo_documento':$("#series").val(),'tipoVenta':'02'},
                                    success:function(data){
                                        console.log(data);
                                    },
                                    error:function(data){
                                        console.log(data);
                                    }
                                });
                            }

                           swal({
                              title: "Enviado...!",
                              text: data.success['Description'],
                              icon: "success",
                              buttons: true,
                              dangerMode: true

                            })
                            .then((willDelete) => {
                              if (willDelete) {
                                  swal("Transacción Finalizada!", {
                                    icon: "success",
                                  });

                                  window.location.href='control-habitaciones.php';
                              } /*else {
                                swal("Transacción Finalizada!");
                              }*/
                            });
                                                                               
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
                                    window.open('FE/PDF/'+data.nombre_archivo);
                                    //window.location.href='control-habitaciones.php';
                                  
                                }else{
                                     if(data.success==2){

                                        swal("Error!",data.errors, "error");
                                        $("#liAnula").removeClass();
                                        $("#liAnula").addClass('fa fa-arrow-up');
                                        $('#ProcesaEnvio').removeAttr('disabled'); 
                                        window.open('FE/PDF/'+data.nombre_archivo);
                                        //window.location.href='control-habitaciones.php';
                                    }
                                }
                                $( "#div_loading" ).remove();
                               
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
          /*swal("algo", {
            icon: "success",
          });*/
        } else {
          swal("El proceso de envío fue cancelado..!!");
        }
      });
    });

  });
  </script>




