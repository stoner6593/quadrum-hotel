<?php //ini_set('display_errors', 1);
include "validar.php";
include "config.php";
include "include/functions.php";
date_default_timezone_set('America/Lima');

$xidturno = $_SESSION['idturno'];
$idusuario = $_SESSION['xyzidusuario'];

$sqlusuarioturno = $mysqli->query("select 
	idturno,
	idusuario
	from ingresosturno where idturno = '$xidturno'");
	$xuFila = $sqlusuarioturno->fetch_row();
	
	$xidusuario = @$xuFila["1"]; //Usuario de Turno

//RESUMEN DE PRODUCTOS
//$xidusuario = $_SESSION['xyzidusuario'];


//INGRESO HABITACION
$sqlturno = $mysqli->query("select
	idturno,
	totalhabitacion,
	totaladicional,
	totalproducto,
	
	totalefectivo,
	totalvisa,
	
	idusuario,
	estadoturno,
	fechaapertura,
	fechacierre,
  totaldescuento,
  totalanulado,
  totalefectivoanulado,
  totalvisaanulado,
  totalproductoanulado,

  totalmastercard,
  totalmastercardanulado
	
	from ingresosturno where idturno = '$xidturno' and idusuario ='$idusuario' ");

$hFila = $sqlturno->fetch_row();
	
	//Habitacion
	$xhabitacion = @$hFila['1'];

	//Producto
	$xproducto = @$hFila['3'];
	
	//Visa/Efectivo
	$xefectivo = @$hFila['4'];
	$xvisa = @$hFila['5'];
  $xmastercard = @$hFila['15'];
	
	//$xsumatotal = $hFila['6'];
	
	//TOTAL INGRESOS DE TURNO
	

//EGRESO O GASTOS
$sqlgastos = $mysqli->query("select
	idgasto,
	monto,
	estadoturno,
	usuario,
	tipooperacion
	from gasto 
	where idturno = '$xidturno' and usuario = '$xidusuario'");
	
	$xcompra = 0;
	$xgasto = 0;
	$xsumaegreso = 0;
	while($gFila = $sqlgastos->fetch_row()){
		if ($gFila['4']==1){ //Compras
			$xcompra = $xcompra + $gFila['1'];//total
		}elseif($gFila['4']==2){ //Gastos
			$xgasto = $xgasto + $gFila['1'];//total
		}
		$xsumaegreso = $xsumaegreso + $gFila['1'];
	}

function getTotReserva($mysqli, $xidturno, $xidusuario, $tipo){
  return $mysqli->query("
    SELECT 
      sum(total) as tot
    FROM al_venta_detalle
    WHERE idturno = '$xidturno' and idusuario = '$xidusuario' and tiporeserva = '$tipo'
  ");
}

$totCredito   = getTotReserva($mysqli, $xidturno, $xidusuario, 5)->fetch_row();
$totDeposito  = getTotReserva($mysqli, $xidturno, $xidusuario, 6)->fetch_row();
$totPagoLinea = getTotReserva($mysqli, $xidturno, $xidusuario, 7)->fetch_row();


		
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Administrador</title>
<link href="opera.css" rel="stylesheet" type="text/css">
<script src="chartjs/Chart.js"></script>
<link href="http://fontawesome.io/assets/font-awesome/css/font-awesome.css" rel="stylesheet"> 




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
    function ImprimirTurno(){ 
        window.open('imprimir/print_turno.php?idturno=<?php echo $_SESSION['idturno'];?>','modelo','width=1000, height=350, scrollbars=yes' );
    }
	function ImprimirProducto(){ 
        window.open('imprimir/print_producto.php?idturno=<?php echo $_SESSION['idturno'];?>','modelo','width=1000, height=350, scrollbars=yes' );
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
      <td width="185" height="25" align="left" valign="top"><?php include ("menu_nav.php"); ?></td>
      <td width="25">&nbsp;</td>
      <td width="793" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tbody>
          <tr>
            <td height="30"> <h3 style="color:#E1583E;"> <i class="fa fa-calendar"></i> Reportes </h3> <div class="lineahorizontal" style="background:#EFEFEF;"></div> </td>
            </tr>
          <tr>
            <td height="246" valign="top"><table width="99%" border="0" cellpadding="0" cellspacing="0">
              <tbody>
                <tr>
                  <td height="30" colspan="3" class="textoContenido"><table width="100%" border="0" cellspacing="1" cellpadding="2">
                    <tbody>
                      <tr>
                        <td height="25" colspan="3" class="textoContenido">Antes de Cerrar su Turno debe Imprimir o Guardar toda información resultante para que el Siguiente usuario pueda abrir turno.</td>
                      </tr>
                      <tr>
                        <td height="25" colspan="3" class="textoContenido"><div class="lineahorizontal" style="background:#EFEFEF;"></div></td>
                        </tr>
                      <tr>
                        <td height="25">&nbsp;</td>
                        <td height="25">&nbsp;</td>
                        <td height="25">&nbsp;</td>
                      </tr>
                      <tr>
                        <td width="242" height="25"><strong>Resumen de Ordenes de Turno</strong></td>
                        <td width="410" height="25">Usuario: <?php echo $_SESSION['xyznombre'];?></td>
                        <td width="182" height="25">&nbsp;</td>
                      </tr>
                    </tbody>
                  </table></td>
                  </tr>
                <tr>
                  <td height="19" colspan="3" bgcolor="#FFFFFF"><div class="lineahorizontal" style="background:#EFEFEF;"></div></td>
                  </tr>
                <tr>
                  <td width="358" height="25" valign="top"><table width="89%" border="0" align="center" cellpadding="5" cellspacing="1">
                    <tbody>
                      <tr>
                        <td height="35" colspan="2" align="center" bgcolor="#FFFFFF" class="textoContenido"><strong> INGRESOS DE TURNO</strong></td>
                        </tr>
                      <tr>
                        <td height="25" bgcolor="#FFFFFF"><span class="textoContenido">Ingreso de Habitaciones</span></td>
                        <td bgcolor="#FFFFFF"><span class="textoContenido">S/ <?php echo number_format($xhabitacion,2);?></span></td>
                        </tr>
                      <tr>
                        <td width="198" height="25" bgcolor="#FFFFFF"><span class="textoContenido">Ingreso de Productos/Servicios</span></td>
                        <td width="98" bgcolor="#FFFFFF"><span class="textoContenido">S/ <?php echo number_format($xproducto,2);?></span></td>
                        </tr>
                      <tr>
                        <td height="25" bgcolor="#FFFFFF"><strong><span class="textoContenido">Total General</span></strong></td>
                        <td bgcolor="#FFFFFF"><span class="textoContenido">S/ <?php echo number_format(($xhabitacion+$xproducto) - $hFila[10] ,2);?></span></td>
                        </tr>
                      <tr>
                        <td height="25" bgcolor="#FFFFFF">&nbsp;</td>
                        <td bgcolor="#FFFFFF">&nbsp;</td>
                        </tr>
                      <tr>
                        <td height="25" bgcolor="#FFFFFF"><span class="textoContenido">Total Visa</span></td>
                        <td bgcolor="#FFFFFF"><span class="textoContenido">S/ <?php echo number_format($xvisa,2);?></span></td>
                        </tr>
                      <tr>
                        <td height="25" bgcolor="#FFFFFF"><span class="textoContenido">Total Efectivo</span></td>
                        <td bgcolor="#FFFFFF"><span class="textoContenido">S/ <?php echo number_format($xefectivo - $hFila[10],2);?></span></td>
                        </tr>
                      <!--<tr>
                        <td height="25" bgcolor="#FFFFFF"><span class="textoContenido">Total Master Card</span></td>
                        <td bgcolor="#FFFFFF"><span class="textoContenido">S/ <?php echo number_format($xmastercard,2);?></span></td>
                        </tr> -->
                      </tbody>
                    </table></td>
                  <td width="57" height="25">&nbsp;</td>
                  <td width="638" height="25" align="left" valign="top"><table width="53%" border="0" align="left" cellpadding="5" cellspacing="1">
                    <tbody>
                      <tr>
                        <td height="35" colspan="2" align="center" bgcolor="#FFFFFF" class="textoContenido"><strong>GASTOS Y COMPRAS (EGRESO)</strong></td>
                        </tr>
                      <tr>
                        <td height="25" bgcolor="#FFFFFF"><span class="textoContenido">Compras</span></td>
                        <td bgcolor="#FFFFFF"><span class="textoContenido">S/ <?php echo number_format($xcompra,2);?></span></td>
                        </tr>
                      <tr>
                        <td width="154" height="25" bgcolor="#FFFFFF"><span class="textoContenido">Gastos</span></td>
                        <td width="161" bgcolor="#FFFFFF"><span class="textoContenido">S/ <?php echo number_format($xgasto,2);?></span></td>
                        </tr>
                      <tr>
                        <td height="25" bgcolor="#FFFFFF"><strong><span class="textoContenido">Total Egresos</span></strong></td>
                        <td bgcolor="#FFFFFF"><span class="textoContenido">S/ <?php echo number_format($xsumaegreso,2);?></span></td>
                        </tr>
                      <tr>
                        <td height="25" bgcolor="#FFFFFF">&nbsp;</td>
                        <td bgcolor="#FFFFFF">&nbsp;</td>
                      </tr>
                      <tr>
                        <td height="25" colspan="2" bgcolor="#FFFFFF"><span class="textoContenido"><strong>TOTAL EFECTIVO (INGRESO-GASTO-DESCUENTOS): </strong></span></td>
                      </tr>
                      <tr>
                        <td height="25" colspan="2" bgcolor="#FFFFFF"><span class="textoContenido"><strong>S/ <?php echo number_format($xefectivo-$xsumaegreso - $hFila[10],2);?></strong></span> </td>
                      </tr>
                      </tbody>
                    </table></td>
                    <td width="638" height="25" align="left" valign="top"><table width="53%" border="0" align="left" cellpadding="5" cellspacing="1">
                    <tbody>
                      <tr>
                        <td height="35" colspan="2" align="center" bgcolor="#FFFFFF" class="textoContenido"><strong>Total descuentos</strong></td>
                        </tr>
                      <tr>
                         <td height="25" bgcolor="#FFFFFF">&nbsp;</td>
                        <td bgcolor="#FFFFFF">&nbsp;</td>
                        </tr>
                      <tr>
                         <td height="25" bgcolor="#FFFFFF">&nbsp;</td>
                        <td bgcolor="#FFFFFF">&nbsp;</td>
                        </tr>
                   
                      <tr>
                         <td height="25" bgcolor="#FFFFFF"><strong><span class="textoContenido">Total Egresos</span></strong></td>
                        <td bgcolor="#FFFFFF"><span class="textoContenido">S/ <?php echo number_format($hFila[10],2);?></span></td>
                      </tr>
                     
                      </tbody>
                    </table></td>
                </tr>
                <tr>
                  <td height="25" colspan="3"><div class="lineahorizontal" style="background:#EFEFEF;"></div></td>
                </tr>
                <tr>
                    <td width="638" height="25" align="left" valign="top" style="
    padding-left: 20px;">
                        <table border="0" align="left" cellpadding="5" cellspacing="1">
                            <tbody>
                            <tr>
                                <td height="35" colspan="2" align="center" bgcolor="#FFFFFF" class="textoContenido"><strong>Total Anulado:</strong></td>
                                <td bgcolor="#FFFFFF"><span class="textoContenido">S/ <?php echo number_format($hFila[11],2);?></span></td>
                            </tr>
                            <tr>
                                <td height="35" colspan="2" align="center" bgcolor="#FFFFFF" class="textoContenido"><strong>Total Anulado Efectivo:</strong></td>
                                <td bgcolor="#FFFFFF"><span class="textoContenido">S/ <?php echo number_format($hFila[12],2);?></span></td>
                            </tr>
                            <tr>
                                <td height="35" colspan="2" align="center" bgcolor="#FFFFFF" class="textoContenido"><strong>Total Anulado Visa:</strong></td>
                                <td bgcolor="#FFFFFF"><span class="textoContenido">S/ <?php echo number_format($hFila[13],2);?></span></td>
                            </tr>

                            <tr>
                                <td height="35" colspan="2" align="center" bgcolor="#FFFFFF"><strong><span class="textoContenido">Total Anulado Productos</span></strong></td>
                                <td bgcolor="#FFFFFF"><span class="textoContenido">S/ <?php echo number_format($hFila[14],2);?></span></td>
                            </tr>

                            </tbody>
                        </table>
                    </td>
                    <td>
                      
                    </td>
                    <td>
                      <table width="100%">
                        <tr>
                          <td height="35"><strong><span class="textoContenido">
                            Total en crédito:</span></strong></td>
                          <td>
                            <span class="textoContenido">
                              S/ <?php echo number_format($totCredito[0],2);?>
                            </span>
                          </td>
                        </tr>
                        <tr>
                          <td height="35"><strong><span class="textoContenido">
                            Total en depósito:</span></strong></td>
                          <td>
                            <span class="textoContenido">
                              S/ <?php echo number_format($totDeposito[0],2);?>
                            </span>
                          </td>
                        </tr>
                        <tr>
                          <td height="35"><strong><span class="textoContenido">
                            Total en pago en linea:</span></strong></td>
                          <td>
                            <span class="textoContenido">
                              S/ <?php echo number_format($totPagoLinea[0],2);?>
                            </span>
                          </td>
                        </tr>
                      </table>
                    </td>
                </tr>
                <tr>
                    <td height="25" colspan="3"><div class="lineahorizontal" style="background:#EFEFEF;"></div></td>
                </tr>
                <tr>
                  <td height="25" colspan="3"><table width="100%" border="0" align="center" cellpadding="5" cellspacing="1">
                    <tbody>
                      <tr>
                        <td width="206" height="35" align="center" bgcolor="#FFFFFF" class="textoContenido">
                          <a href="#" onClick="ImprimirTurno(); return false" class="btnrojo"> <i class="fa fa-print"></i> Imprimir Resumen</a> 
                        </td>
                        <td width="178" align="center" bgcolor="#FFFFFF" class="textoContenido"><a href="#" onClick="ImprimirProducto(); return false" class="btnrojo"> <i class="fa fa-print"></i> Imprimir Productos</a></td>
                        <td width="635" align="center" bgcolor="#FFFFFF" class="textoContenido"><a href="include/usuario/prg_cerrar-turno.php?idturno=<?php echo $xidturno.'&idusuario='.$xidusuario ;?>" class="btnnegro" onClick="return confirm('¿Confirma Cerrar el Turno?');"> <i class="fa fa-close"></i> Cerrar Turno </a>&nbsp;</td>
                        </tr>
                      <tr>
                        <td height="35" align="center" bgcolor="#FFFFFF" class="textoContenido">&nbsp;</td>
                        <td align="center" bgcolor="#FFFFFF" class="textoContenido">&nbsp;</td>
                        <td align="center" bgcolor="#FFFFFF" class="textoContenido">&nbsp;</td>
                      </tr>
                      </tbody>
                  </table></td>
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
    <tr>
      <td height="25" colspan="3"></td>
    </tr>
  </tbody>
</table>


</body>
</html>
<?php include ("footer.php") ?>




