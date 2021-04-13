<?php
require_once "../../init.php";

	include("Invoice.php");
	include("generales/convertir.php");
	//include "../validar.php";
	include "../config.php";
	include "../include/functions.php";
	include "Firmado.php";
	include "CustomHeaders.php";
	include "Pdf.php";
	include "Barcode.php";
	

	class TimeoutException extends RuntimeException {}
	class Generaxml extends Invoice
	{
		
		//private $sqlalquiler=null; 
		public $idalquiler;
		public $tipo_documento;
		public $finicio;
		public $ffin;
		function __construct($idalquiler,$tipo_documento,$finicio=null,$ffin)
		{
			$this->idalquiler=$idalquiler;
			$this->tipo_documento=$tipo_documento;
			$this->finicio=$finicio;
			$this->ffin=$ffin;
			$this->crear_directorio();
			
		}

		public function crear_directorio(){
			
			if(!is_dir('XMLFIRMADOS/')){
				@mkdir('XMLFIRMADOS/', 0700);
			}
			
			if(!is_dir('XML/')){
				@mkdir('XML/', 0700);
			}
			if(!is_dir('CDR/')){
				@mkdir('CDR/', 0700);
			}
			
			if(!is_dir('XMLENVIAR/')){
				@mkdir('XMLENVIAR/', 0700);
			}
			if(!is_dir('PDF/')){
				@mkdir('PDF/', 0700);
			}
		}

		
		
		//DATOS DE LA EMPRESA
		public function generar_xml(){ 

			$db = new conexion();
			$link = $db->conexion();

				
			$sqlalquiler = $link->query("select
			al_venta.idalquiler,
			al_venta.idhuesped,
			al_venta.idhabitacion,
			al_venta.nrohabitacion,
			al_venta.tipooperacion,
			al_venta.total,
			
			cliente.idhuesped,
			cliente.nombre,
			cliente.ciudad,
			cliente.tipo_documento,
			cliente.documento,
			
			al_venta.comentarios,
			al_venta.nroorden,
			al_venta.fechafin,
			al_venta.totalefectivo,
			al_venta.totalvisa,
			al_venta.descuento,
			al_venta.fecharegistro
			
			from al_venta inner join cliente on cliente.idhuesped = al_venta.idhuesped
			where al_venta.idalquiler = '$this->idalquiler' 
			and al_venta.anulado = 0
			");		
			$xaFila = $sqlalquiler->fetch_row();
			
			
			//
			if($xaFila[9]==''){
				$RUCReceptor='00000000';
				$RznSoc= $xaFila[7];
				$Direccion=$xaFila[8];
				$TipoDocumento='1';
			}else{

				$RUCReceptor=$xaFila[10];
				$RznSoc= $xaFila[7];
				$Direccion=$xaFila[8];
				$TipoDocumento=$xaFila[9];

			}

			$array = [
							'Encabezado' => [
								
								/*'Emisor' => [
									'RUCEmisor' => '20545756022',
									'RznSoc' => 'INVERSIONES INKA´S PALACE S.A.C.',
									'NomComercial' => '',
									'Ubigeo' => 150101,
									'Direccion'=>"CAL.MANUEL DEL PINO NRO. 116 URB. SANTA BEATRIZ (ALT.CDRA.16 AV.ARENALES) LIMA - LIMA - LIMA",
									'Urbanizacion' => '',
									'Departamento' => 'LIMA',
									'Provincia' => 'LIMA',
									'Distrito' => 'LIMA',
								],*/
								'Emisor' => [
									'RUCEmisor'		=> APP_EMISOR_RUC,
									'RznSoc'		=> APP_EMISOR_RAZONSOCIAL,
									'NomComercial'	=> APP_EMISOR_NOMBRE,
									'Ubigeo'		=> APP_EMISOR_UBIGUEO,
									'Direccion'		=> APP_EMISOR_DIRECCION,
									'Urbanizacion' 	=> APP_EMISOR_URBANIZACION,
									'Departamento' 	=> APP_EMISOR_DEPARTAMENTO,
									'Provincia' 	=> APP_EMISOR_PROVINCIA,
									'Distrito' 		=> APP_EMISOR_DISTRITO,
								],
								'Receptor' => [
									'RUCReceptor' =>$RUCReceptor,
									'RznSoc' => $RznSoc,											
									'Direccion'=> $Direccion,
									'TipoDocumento'=>$TipoDocumento,
								],
							],
					];

			

			//Detalle Aquiler
			$sqldetalle = $link->query("select
				idalquilerdetalle,
				idalquiler,
				tipoalquiler,	
				fechadesde,	
				fechahasta,	
				nrohoras,	
				nrodias,	
				costohora,	
				costodia,	
				formapago,	
				totalefectivo,	
				totalvisa,	
				estadopago,	
				costoingresoanticipado,	
				horaadicional,
				costohoraadicional,	
				huespedadicional,	
				costohuespedadicional,	
				preciounitario,	
				cantidad,	
				total,	
				idturno,	
				idusuario
				
				from al_venta_detalle 
				where idalquiler = '$this->idalquiler'   and estadopago!=2 order by idalquilerdetalle asc
				");
			$descripcion="";
			$items=array();
			$globalIGV=0; $globalTotalVenta=0; $globalGrabadas=0;$Descuento=0; 
			$num=0;
			while ($tmpFila = $sqldetalle->fetch_row()){ $num++; 
                
                //Validación para que no muestre toda la descripción del alquiler
                //Según reglamento no se debe de alguilar por horas
                
			    if($tmpFila['2']==1){
				    
				    	$descripcion="HOSPEDAJE";
				    
			    }else{
			        
			    
    				$descripcion=tipoAlquiler($tmpFila['2']).' ('.$tmpFila['19'].')';
    				if($tmpFila['2'] != 4 &&  $tmpFila['2'] != 5){
    					$descripcion.= fechadesdehasta($tmpFila['3'],$tmpFila['4']);
    				}
    				
    			}
				//$pu=number_format($tmpFila[18] / 1.18,2);								
				/*$pu=number_format($tmpFila[20] / 1.18,2);								
				
				$t=number_format(str_replace(",", "", $tmpFila[20]),2); 
				$st=number_format($t / (1.18 ),2);
				$igv=number_format($t - $st,2);*/


				$pu1=number_format($tmpFila[20] / 1.18,2);								
				$pu= str_replace(",", "", $pu1);								
				//$t=($pu * 1);
				$t=(str_replace(",", "", $tmpFila[20])); 
				$stt=number_format($t / (1.18 ),2);
				$st=str_replace(",", "", $stt);
				$igvv=number_format($t - $st,2);
				$igv=str_replace(",", "", $igvv);
				
				
				$item ["pro_id"]     = $num;
	            $item ["pro_desc"]   =str_replace('<br>', '', $descripcion) ;
	            $item ['pro_cantidad']   = 1;
	            $item ["pro_unimedida"]  = 'NIU';
	            $item ["pro_preunitario"]    =str_replace(",", "", number_format($pu,2));
	            $item ['pro_preref']     =str_replace(",", "", number_format($pu,2));
	            $item ["pro_tipoprecio"]     = "01"; //Precio Incluye IGV
	            $item ["pro_igv"]    = str_replace(',', '', $igv); 
	            $item ['pro_tipoimpuesto']   = "10"; //Gravado - Operación Onerosa
	            $item ["pro_isc"]    = number_format(0.00,2);
	            $item ["pro_otroimpuesto"]   = number_format(0.00,2);
	            $item ['pro_subtotal']   =str_replace(',', '', $st);
	            $item ['pro_total']  = str_replace(",", "", number_format($t,2)); //number_format(str_replace(",", "", $t),2);
				//echo $igv."-".$t."-".$st;

	            $globalIGV+=$igv;
	            $globalTotalVenta+= $t;//$tmpFila[18];
	            $globalGrabadas+=$st;
	           
	            array_push($items,$item);

	            //Actualiza estado de envío de alquiler a SUNAT / Esto para cuando se agreguen nuevos productos solo se envíen los NO FACTURADOS
	            $sqlactualiza = $link->query("UPDATE  al_venta_detalle SET procesado=1 where idalquilerdetalle = '$tmpFila[0]'");
			}
			//FIN DETALLE ALQUILER
			//echo $globalIGV."-".$globalTotalVenta."-".$globalGrabadas;
			//INICIO DETALLE VENTAS
			$sqlventa = $link->query("select
			venta.idventa,
			venta.idalquiler,
			ventadetalle.idventadetalle,
			ventadetalle.idventa,
			ventadetalle.nombre,
			ventadetalle.cantidad,
			ventadetalle.precio,
			ventadetalle.importe
			
			from venta left join ventadetalle on ventadetalle.idventa = venta.idventa
			where venta.idalquiler = '$this->idalquiler'  order by ventadetalle.idventadetalle asc");

			$detTotventa=0;
			while($vFila = $sqlventa->fetch_row()){

				$pu2=number_format($vFila[6] / 1.18,2);	

				$ttt=($vFila[6] * $vFila[5]);
				$t2=str_replace(",", "",$ttt);

			
				$st2=str_replace(',', '', number_format($t2 / (1.18 ),2)) ;
				$igv2=number_format($t2 - $st2,2);


				//$st2=number_format($t2 * (18 / 100 ),2);
				//$igv2=number_format($vFila[7] - $t2,2);

				$item2 ["pro_id"]     = rand();
	            $item2 ["pro_desc"]   =$vFila['4'] ;
	            $item2 ['pro_cantidad']   = $vFila['5'];
	            $item2 ["pro_unimedida"]  = 'NIU';
	            $item2 ["pro_preunitario"]    =$pu2;
	            $item2 ['pro_preref']     =str_replace(",", "", $pu2);
	            $item2 ["pro_tipoprecio"]     = "01"; //Precio Incluye IGV
	            $item2 ["pro_igv"]    = $igv2;
	            $item2 ['pro_tipoimpuesto']   = "10"; //Gravado - Operación Onerosa
	            $item2 ["pro_isc"]    = number_format(0.00,2);
	            $item2 ["pro_otroimpuesto"]   = number_format(0.00,2);
	            $item2 ['pro_subtotal']   =str_replace(",", "", $st2) ;
	            $item2 ['pro_total']  =str_replace(",", "", number_format($t2,2)) ;
	            
	            $globalIGV+= str_replace(",", "", $igv2); 
	            $globalTotalVenta+=str_replace(',', '', number_format($t2,2));
	            $globalGrabadas+= str_replace(",", "", $st2);
	           
	            if(count($item2)>0){

	            	array_push($items,$item2);

	            	 //Actualiza estado de envío de producto a SUNAT / Esto para cuando se agreguen nuevos productos solo se envíen los NO FACTURADOS
	            	$sqlDetalleVenta = $link->query("UPDATE ventadetalle SET procesado=1 where idventadetalle='$vFila[2]'");
	            }

			}



			//Descuento Global
			$Descuento= $xaFila[16];
			
			//CORRELATIVO PARA LOS DOCUMENTOS

			$correlativo=$link->query("SELECT * FROM series WHERE codsunat='$this->tipo_documento' and estado=1")->fetch_row();

			
			//$date = new DateTime($xaFila[17]);
            $date = new DateTime();
			//Validar si es boleta o factura
			$corre=$correlativo[3].'-'.str_pad($correlativo[4], 8, "0", STR_PAD_LEFT);
			
			$this->setEncabezado($array);			
			$this->setId($corre);
			if($this->tipo_documento=="03"){
				$this->InvoiceTypeCode="03"; //01 factura - 03 boleta	
			}else if($this->tipo_documento=="01"){
				$this->InvoiceTypeCode="01"; //01 factura - 03 boleta
			}
			
			$this->setIssueDate($date->format('Y-m-d')); //Fecha registro

			$this->DocumentCurrencyCode="PEN"; //PEN SOLES - USD DOLARES			
			$this->setInvoiceLine($items);
			//$this->ci->invoice->setDiscrepancia($discrepancia);
			//$this->ci->invoice->setDocRelacionado($relacionado);
			$this->setTotalIgv(number_format($globalIGV,2));
			$this->setTotalIsc(number_format(0.00,2));
			$this->setTotalOtrosTributos(number_format(0.00,2));
			$this->setTotalVenta(number_format($globalTotalVenta,2));
			$this->setGravadas(number_format($globalGrabadas,2));//Venta Grabada
			$this->setGratuitas(number_format(0.00,2));//Venta Gratuitas
			$this->setInafectas(number_format(0.00,2));//Venta Inafectas
			$this->setExoneradas(number_format(0.00,2));//Venta Exoneradas
			$this->setDescuentoGlobal(number_format($Descuento,2));//DescuentoGlobal
			$this->setMontoPercepcion(number_format(0.00,2));//MontoPercepcion
			$this->setTipoOperacion("01");//TipoOperacion 01 Venta Interna
			$MontoLetras=num_to_letras(($globalTotalVenta - $Descuento),"PEN");
			$this->setMontoEnLetras($MontoLetras);//Total Venta Letras
			
			//General XML	
			switch ($correlativo[1])
	           {
	              
	                case "07":
	                   // $this->ci->invoice->_xmlCreditNote();
	                    break;
	                case "08":
	                    //$this->ci->invoice->_xmlDebitNote();
	                    break;
	                default: 
	                	$this->_xml();
	                	break;                       
	               
	            }
				
			
			//FIN GENERA XML
			//Arreglo listo para enviar al firmado del XML
	        $dato['TotIgv']=str_replace(',', '', $globalIGV) ;
	        $dato['TotVenta']=str_replace(',', '', $globalTotalVenta) ;
	        $dato['TotGravada']= str_replace(',', '', $globalGrabadas) ;
	        $dato['TotGratuitas']=0.00;
	        $dato['TotInafectas']=0.00;
	        $dato['TotExoneradas']=0.00;
	        $dato['DescuentoGlobal']=$Descuento;
	        $dato['Moneda']='PEN'; 
	        $dato['tipo_documento']=trim($correlativo[1]); 
	        $dato['fecharegistro']=$date->format('Y-m-d');

	        if($xaFila['14'] > 0){ 
	        	$dato['formapago']="Efectivo";
	        }elseif ($xaFila['15'] > 0) {
	        	$dato['formapago']="Visa";
	        }
	       

			$arrayFirmado=array('NomDocXML'=>$this->getId(),
				'TipoDocumento'=>$this->InvoiceTypeCode,
				'RUCEmisor'=>$array['Encabezado']['Emisor']['RUCEmisor']);
			
			$firmado= new Firmado();			
			
			//Firma XML - Devuelve Nombre de XML
			$result=$firmado->Firmar_xml($arrayFirmado); //Validar luego			
			
			//Comprime XML .zip	
			$zip = new ZipArchive();

			$nombreZip=substr($result, 0,-4).'.zip';
			$filename = './XMLENVIAR/'.$nombreZip;			
 
			if($zip->open($filename,ZIPARCHIVE::CREATE)===true) {
			        $zip->addFile('./XMLFIRMADOS/'.$result,$result);			       
			        $zip->close();
			       // echo 'Creado '.$filename;
			}
			else {
			        //echo 'Error creando '.$filename;
			}						
		
			@$cargaZip='./XMLENVIAR/'.$nombreZip;
			$zipEnviar=(file_get_contents($cargaZip));

			//Nombre para el archivo PDF ***** mejorar
			$nombre_archivo = utf8_decode($array['Encabezado']['Emisor']['RUCEmisor'].'-'.$date->format('Y-m-d').'-'.$corre);

			$this->generapdfinvoice($array,$corre,$dato,$items,$MontoLetras,$nombre_archivo,$firmado->firma,$xaFila[3]); //Genera PDF
			
			//Agregado para enviar solo facturas, las boletas se almacenan para enviar por resumen diario
			if($this->tipo_documento=="03"){

				
				$ArrayMessage=array('success'=> array('ReferenceID' => $corre,'codRespuesta' =>0,
							'Description' => "Documento pendiente de envio a SUNAT",'nombre_archivo'=>$nombre_archivo.'.pdf' ),'errors'=>0);

				$this->ActualizaCorrelativo($correlativo);
				$this->ActualizaVenta($correlativo,0,"Pendiente de envio",$nombreZip,$nombre_archivo,$corre,1);


				echo json_encode($ArrayMessage);

			}else if($this->tipo_documento=="01"){

				$Respuesta=$this->enviar_sunat($zipEnviar,$nombreZip,$nombre_archivo);	

				$res=json_decode($Respuesta,TRUE);
				echo $Respuesta; //Muestra Respuesta
				
				if($res['errors']=="0"):					
					//if($res['success']['codRespuesta'][0]==0):
						//Aquí trabajar para guardar en BD
						$codigoRespuesta=$res['success']['codRespuesta'];
						$msgRespuesta=$res['success']['Description'];
						
																		
					//endif;	
				else:
						$codigoRespuesta=$res['errors']['getCode'];
						$msgRespuesta=$res['errors']['getMessage'];		
				endif;
				
				
				$this->ActualizaCorrelativo($correlativo);
				$mensajeSunat=str_replace("'","",$msgRespuesta);
				if(trim($codigoRespuesta)=='WSDL'): $codigoRespuesta=-1; endif;
				$this->ActualizaVenta($correlativo,$codigoRespuesta,$mensajeSunat,$nombreZip,$nombre_archivo,$corre,2);
			

			}

			
			
		}

		function _xmlResumen(){

			$db = new conexion();
			$link = $db->conexion();

			$sqlalquiler = $link->query("select
			al_venta.idalquiler,
			al_venta.idhuesped,
			al_venta.idhabitacion,
			al_venta.nrohabitacion,
			al_venta.tipooperacion,
			al_venta.total,
			
			cliente.idhuesped,
			cliente.nombre,
			cliente.ciudad,
			cliente.tipo_documento,
			cliente.documento,
			
			al_venta.comentarios,
			al_venta.nroorden,
			al_venta.fechafin,
			al_venta.totalefectivo,
			al_venta.totalvisa,
			al_venta.descuento,
			al_venta.fecharegistro
			
			from al_venta inner join cliente on cliente.idhuesped = al_venta.idhuesped
			where al_venta.idalquiler = '$this->idalquiler' 
			and al_venta.anulado = 0
			");		
			$xaFila = $sqlalquiler->fetch_row();
			
			
			//
			if($xaFila[9]==''){
				$RUCReceptor='00000000';
				$RznSoc= $xaFila[7];
				$Direccion=$xaFila[8];
				$TipoDocumento='1';
			}else{

				$RUCReceptor=$xaFila[10];
				$RznSoc= $xaFila[7];
				$Direccion=$xaFila[8];
				$TipoDocumento=$xaFila[9];

			}


			
			$array = [
						'Encabezado' => [
							
							/*'Emisor' => [
									'RUCEmisor' => '20545756022',
									'RznSoc' => 'INVERSIONES INKA´S PALACE S.A.C.',
									'NomComercial' => '',
									'Ubigeo' => 150101,
									'Direccion'=>"CAL.MANUEL DEL PINO NRO. 116 URB. SANTA BEATRIZ (ALT.CDRA.16 AV.ARENALES) LIMA - LIMA - LIMA",
									'Urbanizacion' => '',
									'Departamento' => 'LIMA',
									'Provincia' => 'LIMA',
									'Distrito' => 'LIMA',
								],*/
								'Emisor' => [
									'RUCEmisor'		=> APP_EMISOR_RUC,
									'RznSoc'		=> APP_EMISOR_RAZONSOCIAL,
									'NomComercial'	=> APP_EMISOR_NOMBRE,
									'Ubigeo'		=> APP_EMISOR_UBIGUEO,
									'Direccion'		=> APP_EMISOR_DIRECCION,
									'Urbanizacion' 	=> APP_EMISOR_URBANIZACION,
									'Departamento' 	=> APP_EMISOR_DEPARTAMENTO,
									'Provincia' 	=> APP_EMISOR_PROVINCIA,
									'Distrito' 		=> APP_EMISOR_DISTRITO,
								],
							'Receptor' => [
								'RUCReceptor' =>$RUCReceptor,
								'RznSoc' => $RznSoc,											
								'Direccion'=> $Direccion,
								'TipoDocumento'=>$TipoDocumento,
							],
						],
					];

			$concatena='';

			
			$f1=explode("/",$this->finicio);
			$newfecha1=$f1[2].'-'.$f1[1].'-'.$f1[0];

			$f2=explode("/",$this->ffin);
			$newfecha2=$f2[2].'-'.$f2[1].'-'.$f2[0];



			if($newfecha1 && $newfecha2){
			  //$concatena=" and DATE(al_venta.fecharegistro) between '".$newfecha1."' and '".$newfecha2."'" ;
			  //$concatena=' and DATE(al_venta.fecharegistro) between "'.$newfecha1.'" and "'.$newfecha2.'" ';
			  $concatena= ' and case when al_venta.fechaemision is null then
              DATE(al_venta.fecharegistro) between "'.$newfecha1.'" and "'.$newfecha2.'"
              else DATE(al_venta.fechaemision) between "'.$newfecha1.'" and "'.$newfecha2.'"
              end ';
			}

			if($newfecha1 && $newfecha2){
			  $concatenaVenta=' and DATE(venta.fecha) between "'.$newfecha1.'" and "'.$newfecha2.'" ';
			}

			$sqlalquiler =$link->query("select
			al_venta.idalquiler,
			al_venta.idhuesped,
			al_venta.idhabitacion,
			al_venta.nrohabitacion,
			al_venta.tipooperacion,
			al_venta.total,
			
			cliente.idhuesped,
			cliente.nombre,
			cliente.ciudad,
			cliente.tipo_documento,
			cliente.documento,
			
			al_venta.comentarios,
			al_venta.nroorden,
			case when al_venta.fechaemision is null then al_venta.fecharegistro else al_venta.fechaemision end as fecharegistro,
			al_venta.documento,
			'1' as tipoVenta
			
			from al_venta inner join cliente on cliente.idhuesped = al_venta.idhuesped
			where al_venta.enviado = 1 and al_venta.ticket is null and al_venta.iddocumento=1 
			and al_venta.codigo_respuesta = 0 and al_venta.anulado = 0 ".$concatena."

			union

			select
			venta.idventa,
			venta.idcliente,
			0,
			'',
			0,
			venta.total,
			
			cliente.idhuesped,
			cliente.nombre,
			cliente.ciudad,
			cliente.tipo_documento,
			cliente.documento,
			
			venta.anotaciones,
			venta.numero,
			venta.fecha as fecharegistro,
			venta.documento,
			'2' as tipoVenta
			
			from venta left join cliente on cliente.idhuesped = venta.idcliente
			where venta.enviado = 1 and venta.ticket is null and venta.iddocumento=1 
			and venta.codigo_respuesta = 0 and venta.anulado = 0 ".$concatenaVenta."

			order by fecharegistro desc
			");		
			
			
			if(($sqlalquiler->num_rows==0)){
				
				echo json_encode(array('success'=>0,'errors'=>array('getMessage' =>'No hay registros, Verifique que las fechas sean iguales' ,'getCode'=>0)));
				
				exit();
			}
			$i=0;
			while($xaFila = $sqlalquiler->fetch_row()){

				//Para el cliente
				if($xaFila[9]==''){
					$RUCReceptor='00000000';				
					$TipoDocumento='1';
				}else{

					$RUCReceptor=$xaFila[10];					
					$TipoDocumento=$xaFila[9];

				}

				

				//Detalle Aquiler
				$sqldetalle = $link->query("select
					idalquilerdetalle,
					idalquiler,
					tipoalquiler,	
					fechadesde,	
					fechahasta,	
					nrohoras,	
					nrodias,	
					costohora,	
					costodia,	
					formapago,	
					totalefectivo,	
					totalvisa,	
					estadopago,	
					costoingresoanticipado,	
					horaadicional,
					costohoraadicional,	
					huespedadicional,	
					costohuespedadicional,	
					preciounitario,	
					cantidad,	
					total,	
					idturno,	
					idusuario
					
					from al_venta_detalle 
					where idalquiler = '$xaFila[0]'  and '$xaFila[15]'='1'  order by idalquilerdetalle asc
					");
				$descripcion="";
				$items=array();
				$globalIGV=0; $globalTotalVenta=0; $globalGrabadas=0; 
				$num=0;
				while ($tmpFila = $sqldetalle->fetch_row()){ $num++; 
						
					$pu=number_format($tmpFila[20] / 1.18,2);
					$t=number_format($tmpFila[20],2);
					$st=number_format($t / (1.18 ),2);
					$igv=number_format($t - $st,2);
					
					
		            $globalIGV+=$igv;
		            $globalTotalVenta+= $t;//$tmpFila[18];
		            $globalGrabadas+=$st;
		           

		          
				}
				

				//INICIO DETALLE VENTAS
				$sqlventa = $link->query("select
				venta.idventa,
				venta.idalquiler,
				ventadetalle.idventadetalle,
				ventadetalle.idventa,
				ventadetalle.nombre,
				ventadetalle.cantidad,
				ventadetalle.precio,
				ventadetalle.importe
				
				from venta left join ventadetalle on ventadetalle.idventa = venta.idventa
				where venta.idalquiler = '$xaFila[0]' and '$xaFila[15]'='1' order by ventadetalle.idventadetalle asc");

				$detTotventa=0;
				while($vFila = $sqlventa->fetch_row()){

					$pu2=number_format($vFila[6] / 1.18,2);								
					$t2=number_format($vFila[6] * $vFila[5],2);
					$st2=number_format($t2 / (1.18 ),2);
					$igv2=number_format($t2 - $st2,2);

		            $globalIGV+=$igv2;
		            $globalTotalVenta+=number_format($t2,2);
		            $globalGrabadas+=$st2;
		           
		           $detTotventa++;
		        }

		        $idTipoVenta = 0;
		        if($detTotventa>0){
					$idTipoVenta = '1';
		        }
		        //if($detTotventa>0){
	            // $datos[]= array('RUCReceptor' => $RUCReceptor,
	            // 	'TipoDocumento'=>$TipoDocumento,
	            // 	'globalIGV'=> str_replace(",","",$globalIGV), 
	            // 	'globalTotalVenta'=>number_format(str_replace(",","",$globalTotalVenta),2),
	            // 	'globalGrabadas'=>str_replace(",","",$globalGrabadas),
	            // 	'correlativo'=>$xaFila[14],
	            // 	'id'=>$xaFila[0],
	            // 	'tipoVenta'=>'1');		       
				//}
				
	            //INICIO DETALLE VENTAS PRODUCTOS
				$sqlventaDetalle = $link->query("select
				venta.idventa,
				venta.idalquiler,
				ventadetalle.idventadetalle,
				ventadetalle.idventa,
				ventadetalle.nombre,
				ventadetalle.cantidad,
				ventadetalle.precio,
				ventadetalle.importe
				
				from venta left join ventadetalle on ventadetalle.idventa = venta.idventa
				where venta.idventa = '$xaFila[0]' and '$xaFila[15]'='2' order by ventadetalle.idventadetalle asc");

				$detTotventa=0;
				while($vFila = $sqlventaDetalle->fetch_row()){

					$pu2=number_format($vFila[6] / 1.18,2);								
					$t2=number_format($vFila[6] * $vFila[5],2);
					$st2=number_format($t2 / (1.18 ),2);
					$igv2=number_format($t2 - $st2,2);

		            $globalIGV+=$igv2;
		            $globalTotalVenta+=number_format($t2,2);
		            $globalGrabadas+=$st2;
		           
		           $detTotventa++;
		        }

		        if($detTotventa>0){
					$idTipoVenta = '2';
		        }

                $idTipoVenta = $xaFila[15];

		        //if($detTotventa>0){
	            $datos[]= array('RUCReceptor' => $RUCReceptor,
	            	'TipoDocumento'=>$TipoDocumento,
	            	'globalIGV'=> str_replace(",","",$globalIGV), 
	            	'globalTotalVenta'=>number_format(str_replace(",","",$globalTotalVenta),2),
	            	'globalGrabadas'=>str_replace(",","",$globalGrabadas),
	            	'correlativo'=>$xaFila[14],
	            	'id'=>$xaFila[0],
	            	'tipoVenta'=>$idTipoVenta);	
				//}

	            $i++;
			}
			
			//var_dump($datos);

			$this->setResumenDiario($datos);

			
			//CORRELATIVO PARA LOS DOCUMENTOS

			$correlativo=$link->query("SELECT * FROM series WHERE codsunat='RC' and estado=1")->fetch_row();


			//$time = strtotime($newfecha1);
			$date = new DateTime($newfecha1);
			//$date =date('Y-m-d');//$newfecha1
			//$dateEnvio = date('Y-m-d',$time);
			//Validar si es boleta o factura
			//$corre=$correlativo[3].'-'.str_pad($correlativo[4],8,"0",STR_PAD_LEFT);
			$corre=$correlativo[3].'-'.$date->format('Ymd').'-'.($correlativo[4]);

			$this->setEncabezado($array);			
			$this->setId($corre);			
			
			$this->InvoiceTypeCode="03"; //01 factura - 03 boleta	
			//$this->setIssueDate(date('Y-m-d')); //Fecha registro
			$this->setIssueDate($date->format('Y-m-d')); //Fecha registro
			$this->DocumentCurrencyCode="PEN"; //PEN SOLES - USD DOLARES		
			

			$this->_xmlResumenDiario();
			

			$arrayFirmado=array('NomDocXML'=>($this->getId()),
				'TipoDocumento'=>'RC',
				'RUCEmisor'=>$array['Encabezado']['Emisor']['RUCEmisor']);
			
			$firmado= new Firmado();			
		
			//Firma XML - Devuelve Nombre de XML
			$result=$firmado->Firmar_xml($arrayFirmado); //Validar luego			
			
			//Comprime XML .zip	
			$zip = new ZipArchive();

			$nombreZip=substr($result, 0,-4).'.zip';
			$filename = './XMLENVIAR/'.$nombreZip;			
 
			if($zip->open($filename,ZIPARCHIVE::CREATE)===true) {
			        $zip->addFile('./XMLFIRMADOS/'.$result,$result);			       
			        $zip->close();
			       // echo 'Creado '.$filename;
			}
			else {
			        //echo 'Error creando '.$filename;
			}						
		
			@$cargaZip='./XMLENVIAR/'.$nombreZip;
			$zipEnviar=(file_get_contents($cargaZip));

		
			$nombre_archivo = utf8_decode($array['Encabezado']['Emisor']['RUCEmisor'].'-'.$corre);
			$Respuesta=$this->enviar_sunat($zipEnviar,$nombreZip,$nombre_archivo);	
			

			$res=json_decode($Respuesta,TRUE);
			
			
			if($res['errors']=="0"):					
				//if($res['success']['codRespuesta'][0]==0):
					//Aquí trabajar para guardar en BD
					$codigoRespuesta=$res['success']['codRespuesta'];
					$msgRespuesta=$res['success']['Description'];
					foreach ($datos as $key) {
						$this->ActualizaResumenDocumentos($key['id'],$key['tipoVenta'],$codigoRespuesta);
					}
					
																	
				//endif;	
			else:
					$codigoRespuesta=$res['errors']['getCode'];
					$msgRespuesta=$res['errors']['getMessage'];		
			endif;
			
			
			$this->ActualizaCorrelativo($correlativo);
			$mensajeSunat=str_replace("'","",$msgRespuesta);
			if(trim($codigoRespuesta)=='WSDL'): $codigoRespuesta=-1; endif;
			$this->ActualizaResumen($correlativo,$codigoRespuesta,$mensajeSunat,$nombreZip,$nombre_archivo,$corre,2);
		
			echo $Respuesta; //Muestra Respuesta
			//}
			

		}
		//Actualizar Correlativo
		function ActualizaCorrelativo($numeracion=array()){
			$db = new conexion();
			$link = $db->conexion();
			return $link->query("UPDATE series SET numeracion = numeracion + 1 WHERE codsunat='$numeracion[1]' and iddocumento='$numeracion[0]'");

		}
		//Actualiza datos de venta
		function ActualizaVenta($numeracion=array(),$codigoRespuesta,$msgRespuesta,$nombreZip,$nombre_archivo,$corre,$enviado){
			$db = new conexion();
			$link = $db->conexion();
			return $link->query("UPDATE al_venta SET iddocumento ='$numeracion[0]',correlativo='$numeracion[4]',codigo_respuesta='$codigoRespuesta',
				mensaje_respuesta='$msgRespuesta',nombrezip='$nombreZip',nombre_archivo='$nombre_archivo',documento='$corre', enviado='$enviado', fechaemision = CURRENT_TIMESTAMP
			WHERE idalquiler ='$this->idalquiler'");
		}
		//Ingreso datos de resumen
		function ActualizaResumen($numeracion=array(),$codigoRespuesta,$msgRespuesta,$nombreZip,$nombre_archivo,$corre,$enviado){
			$db = new conexion();
			$link = $db->conexion();
			return $link->query("INSERT INTO resumen (iddocumento,correlativo,codigo_respuesta,mensaje_respuesta,nombrezip,nombre_archivo,documento,fecha)
				values($numeracion[0],'$numeracion[4]','$codigoRespuesta','$msgRespuesta','$nombreZip','$nombre_archivo','$corre',NOW())");
		}
		//Actualizar documentos enviados por resumen
		function ActualizaResumenDocumentos($id,$tipoVenta,$codigoRespuesta=""){
			$db = new conexion();
			$link = $db->conexion();
			if($tipoVenta=='1'){
				return $link->query("UPDATE al_venta SET enviado ='2',mensaje_respuesta='Documento enviado por resumen diario',ticket='$codigoRespuesta' WHERE idalquiler ='$id'");
			}
			if($tipoVenta=='2'){
				return $link->query("UPDATE venta SET enviado ='2',mensaje_respuesta='Documento enviado por resumen diario',ticket='$codigoRespuesta' WHERE idventa ='$id'");
			}
		}
		//Eniar a SUNAT
		function enviar_sunat($zipEnviar,$nombreZip,$nombre_archivo){
	   		global $wsdl, $client;
	   		$wsdl = APP_SUNAT_WSDL;
	      	//$wsdl ='https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService?wsdl';
	      	//https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService?wsdl
	      	//https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService?wsdl
		
			/*$params=array('user'=>'20545756022MODDATOS',
				'pass'=>'moddatos');*/
				$params=array('user'=>APP_SUNAT_USER,
				'pass'=>APP_SUNAT_PASS);

		
			try {


				$cabecera= new CustomHeaders($params);
			   
					
				$client = new SoapClient($wsdl, [ 'cache_wsdl' => WSDL_CACHE_NONE, 'trace' =>TRUE , 'soap_version' => SOAP_1_1 ] ); 
				$client->__setSoapHeaders([$cabecera]); 
				$client->__getFunctions();
				
				$contentFile = new SoapVar($zipEnviar, XSD_BYTE);

				

				try {

					if($this->tipo_documento==0){
						$respuesta=$client->sendSummary((array('fileName'=>$nombreZip,'contentFile'=>$zipEnviar,'partyType'=>"")));
						
						$leer=$client->__getLastResponse();	
						$ArrayMessage=array('success'=> array('ReferenceID' => 0,'codRespuesta' =>$respuesta->ticket,
								'Description' => "Por favor, Copie y  consulte el Nº de Ticket a SUNAT =>",'nombre_archivo'=>$nombreZip ),'errors'=>0);
							return json_encode($ArrayMessage);
							//print_r($leer);
					
					}else{
						$respuesta=$client->sendBill(array('fileName'=>$nombreZip,'contentFile'=>$zipEnviar));

						if($respuesta):

							
							$leer=$client->__getLastResponse();					
							//Guarda CDR en carpeta destino
							file_put_contents('CDR/R-'.$nombreZip, $respuesta->applicationResponse);			
							
							$res=$this->getDataCDR('CDR/R-'.$nombreZip,1);						

							$ArrayMessage=array('success'=> array('ReferenceID' => $res['ID_DOCUMENTO_ENVIADO'],'codRespuesta' =>$res['CODIGO'],
								'Description' => $res['DESCRIPCION'],'nombre_archivo'=>$nombre_archivo.'.pdf' ),'errors'=>0);
							return json_encode($ArrayMessage);
							
						else:
							$error=json_encode(array('success'=>0,'errors'=>array('getMessage' =>'ERROR SUNAT' ,'getCode'=>0)));
		                	 throw new \Exception($error);
						endif;
					}

					
					
				 } catch(TimeoutException $e) {
	            	$error=json_encode(array('success'=>0,'errors'=>array('getMessage' =>'ERROR SUNAT' ,'getCode'=>0)));
	                return json_encode($ArrayMessage);
	            
	            } catch (Exception $e) {
	                // relanzarla
	                $ArrayMessage=array('success'=>0,'nombre_archivo'=>$nombre_archivo.'.pdf','errors'=>array('getMessage' =>$e->getMessage() ,'getCode'=>$e->getCode()));
	                return json_encode($ArrayMessage);
	            }	
				
			 } catch(TimeoutException $e) {
	            	$error=json_encode(array('success'=>0,'errors'=>array('getMessage' =>'ERROR SUNAT' ,'getCode'=>0)));
	                return json_encode($ArrayMessage);	

			} catch (SoapFault $fault) {
				$filtrar=str_replace("soap-env:Client."," ", $fault->faultcode);
			    $ArrayMessage=array('success'=>0,'errors'=>array('getCode'=>$filtrar,'getMessage'=>$fault->faultstring),'nombre_archivo'=>$nombre_archivo.'.pdf');
			  
			    
			} catch (Exception $e) { 
			    
			    $ArrayMessage=array('success'=>0,'nombre_archivo'=>$nombre_archivo.'.pdf','errors'=>array('getMessage' =>$e->getMessage() ,'getCode'=>$e->getCode()));
			}

			return json_encode($ArrayMessage);
			
	   	}

	   		/**
	     * Se debe enviar el el nombre del archivo RUC-TIPO-SERIE-NUMERO <br>
	     * Si devuelve el Objeto WebService esta correcto la lectura de la respuesta de SUNAT <br>
	     * Type 1: Lee las respuestas de SendBill <br>
	     * Type 2: Lee la respuesta de un getStatus
	     * @param string $archivo
	     * @param string $type
	     * @return array|string
	     */
	    public function getDataCDR($archivo, $type = '1')
	    {
	        try {

	            switch ($type) {
	                default:
	                case "1":
	                    $archivoZip = $archivo;
	                    break;
	                case "2":
	                    $archivoZip = $archivo;
	                    break;
	            }

	            // Se valida que el .zip exista
	            if (!file_exists($archivoZip)) {
	            	$error=json_encode(array('success'=>0,'errors'=>array('getMessage' =>'El Archivo de respuesta SUNAT no fue encontrado' ,'getCode'=>0)));
	                throw new \Exception($error);
	            	
	            }

	            $zip = new ZipArchive();

	            // Se lee el archivo .zip
	            $readFile = $zip->open($archivoZip);

	            // Se extrae el archivo XML
	            $zip->extractTo('CDR/');

	            // Se valida que se haya extraido correctamente.
	            if ($readFile !== TRUE) {
	                
	                $error=json_encode(array('success'=>0,'errors'=>array('getMessage' =>'Al extraer la respuesta SUNAT ocurrio un error.' ,'getCode'=>0)));
	                throw new \Exception($error);
	            }

	            // archivo XML
	            $archivoXML =  substr($archivo, 0,-4) . '.xml';

	            // Se valida que exista el archivo XML
	            if (!file_exists($archivoXML)) {
	                //throw new \Exception('No se pudo leer el XML de respuesta de SUNAT.');
	                $error=json_encode(array('success'=>0,'errors'=>array('getMessage' =>'No se pudo leer el XML de respuesta de SUNAT.' ,'getCode'=>0)));
	                throw new \Exception($error);
	            }

	            $xml = new DOMDocument('1.0', 'ISO-8859-1');

	            // Se desactivan los errores al leer el Documento XML
	            $xml->load($archivoXML, LIBXML_NOWARNING | LIBXML_NOERROR);

	            /**
	             *  Se capturan los TAGS que almacenan la respuesta SUNAT
	             */

	            // DOCUMENTO DE RESPUESTA SUNAT
	            $DocumentResponse = $xml->getElementsByTagName('DocumentResponse');
	            $itemsDocumentResponse = $DocumentResponse->item(0)->childNodes;
	            $ResponseValue = null;
	            if ($itemsDocumentResponse->length > 0) {
	                for ($DocRes = 0; $DocRes < $itemsDocumentResponse->length; $DocRes++) {
	                    $Response = $itemsDocumentResponse->item($DocRes);
	                    if (isset($Response->tagName)) {
	                        $valtag = $Response->tagName;
	                        if ($valtag == 'cac:Response') {
	                            $ResponseValue = $itemsDocumentResponse->item($DocRes);
	                            break;
	                        }
	                    }
	                }
	            }

	            if (!empty($ResponseValue) && $ResponseValue->childNodes->length > 0) {
	                $ResNodes = $ResponseValue->childNodes;
	                for ($res = 0; $res < $ResNodes->length; $res++) {
	                    if (isset($ResNodes->item($res)->tagName)) {
	                        $tagResnNodes = $ResNodes->item($res)->tagName;
	                        if ($tagResnNodes == 'cbc:ReferenceID') {
	                            $this->SUNAT_ID_DOCUMENTO_ENVIADO = $ResNodes->item($res)->nodeValue;
	                        }
	                        if ($tagResnNodes == 'cbc:ResponseCode') {
	                            $this->SUNAT_CODIGO = $ResNodes->item($res)->nodeValue;
	                        }
	                        if ($tagResnNodes == 'cbc:Description') {
	                            $this->SUNAT_DESCRIPCION = $ResNodes->item($res)->nodeValue;
	                        }
	                    }
	                }
	            }

	            // FECHAS Y HORAS
	            $this->SUNAT_ID = $xml->getElementsByTagName('ID')->item(0)->nodeValue;
	            $this->SUNAT_FECHA_RECEPCION = $xml->getElementsByTagName('IssueDate')->item(0)->nodeValue;
	            $this->SUNAT_HORA_RECEPCION = $xml->getElementsByTagName('IssueTime')->item(0)->nodeValue;
	            $this->SUNAT_FECHA_GENERACION = $xml->getElementsByTagName('ResponseDate')->item(0)->nodeValue;
	            $this->SUNAT_HORA_GENERACION = $xml->getElementsByTagName('ResponseTime')->item(0)->nodeValue;


	            // NOTAS
	            $Notas = $xml->getElementsByTagName('Note');
	            foreach ($Notas as $nota) {
	                $desNota = $nota->nodeValue;
	                $this->SUNAT_NOTE .= $desNota . '|';
	            }

	            // RUC DEL RECEPCION
	            $SenderParty = $xml->getElementsByTagName('SenderParty');
	            $PartyIdentification = $SenderParty->item(0)->childNodes;
	            $this->SUNAT_RUC_RECEPCION = $PartyIdentification->item(0)->nodeValue;

	            // RUC DEL PROCESADO
	            $ReceiverParty = $xml->getElementsByTagName('ReceiverParty');
	            $PartyIdentification = $ReceiverParty->item(0)->childNodes;
	            $this->SUNAT_RUC_PROCESADO = $PartyIdentification->item(0)->nodeValue;

	            // RUC DEL RECEPTOR
	            $RecipientParty = $xml->getElementsByTagName('RecipientParty');
	            $PartyIdentification = $RecipientParty->item(0)->childNodes;
	            $this->SUNAT_RUC_RECEPTOR = $PartyIdentification->item(0)->nodeValue;

	            // DOCUMENTO REFERENCIADO
	            $DocumentReference = $xml->getElementsByTagName('DocumentReference');
	            $ID = $DocumentReference->item(0)->childNodes;
	            $this->SUNAT_ID_DOCUMENTO_PROCESADO = $ID->item(0)->nodeValue;

	            unlink($archivoXML);

	            return ([
	                'ID' => $this->SUNAT_ID,
	                'ID_DOCUMENTO_ENVIADO' => $this->SUNAT_ID_DOCUMENTO_ENVIADO,
	                'CODIGO' => $this->SUNAT_CODIGO,
	                'DESCRIPCION' => $this->SUNAT_DESCRIPCION,
	                'FECHA_RECEPCION' => $this->SUNAT_FECHA_RECEPCION,
	                'HORA_RECEPCION' => $this->SUNAT_HORA_RECEPCION,
	                'FECHA_GENERACION' => $this->SUNAT_FECHA_GENERACION,
	                'HORA_GENERACION' => $this->SUNAT_HORA_GENERACION,
	                'NOTE' => $this->SUNAT_NOTE,
	                'RUC_RECEPCION' => $this->SUNAT_RUC_RECEPCION,
	                'RUC_PROCESADO' => $this->SUNAT_RUC_PROCESADO,
	                'RUC_RECEPTOR' => $this->SUNAT_RUC_RECEPTOR,
	                'ID_DOCUMENTO_PROCESADO' => $this->SUNAT_ID_DOCUMENTO_PROCESADO,
	            ]);

	        } catch (DOMException $e) {
	            //return $ex->getMessage();
	            $ArrayMessage=array('success'=>0,'errors'=>array('getMessage' =>$e->getMessage() ,'getCode'=>$e->getCode()));
			    return json_encode($ArrayMessage);

	        } catch (\Exception $e) {
	            //return $ex->getMessage();
	            $ArrayMessage=array('success'=>0,'errors'=>array('getMessage' =>$e->getMessage() ,'getCode'=>$e->getCode()));
			    return json_encode($ArrayMessage);
	        }
	    }
	    //Lee respuesta de SUNAT
		function unzipByteArray($data){
		  try{

		  /*this firts is a directory*/
		  $head = unpack("Vsig/vver/vflag/vmeth/vmodt/vmodd/Vcrc/Vcsize/Vsize/vnamelen/vexlen", substr($data,0,30));
		  $filename = substr($data,30,$head['namelen']);
		  $if=30+$head['namelen']+$head['exlen']+$head['csize'];
		 /*this second is the actua file*/
		  $head = unpack("Vsig/vver/vflag/vmeth/vmodt/vmodd/Vcrc/Vcsize/Vsize/vnamelen/vexlen", substr($data,$if,30));
		  $raw = gzinflate(substr($data,$if+$head['namelen']+$head['exlen']+30,$head['csize']));
		  //$raw = gzinflate(substr($data,102,968));
		  /*you can create a loop and continue decompressing more files if the were*/
		 
		  return  $raw;//($if+$head['namelen']+$head['exlen']+30).'-'.$head['csize'];
		}
		catch(Exception $e){
			 $ArrayMessage=array('success'=>0,'errors'=>array('getMessage' =>$e->getMessage() ,'getCode'=>$e->getCode()));
		}

		}

		public function generapdfinvoice($Emcabezado=array(),$NumDocumento,$Datos,$productos=array(),$MontoLetras,$nombre_archivo,$firma='',$NHabitacion=''){		

			$arr=$Emcabezado;
			$medidas = array(90, 350); // Ajustar aqui segun los milimetros necesarios;

			$pdf = new Pdf('P', 'mm', $medidas, true, 'UTF-8', false);
			$pdf->setPageFormat($medidas, $orientation='P');
			

			$pdf->RUCEmpresa=$arr['Encabezado']['Emisor']['RUCEmisor'];
			$pdf->NumDocumento=$NumDocumento;

	        $pdf->SetCreator(PDF_CREATOR);
	        $pdf->SetAuthor('José Manuel Bazán de la Cruz');
	        $pdf->SetTitle($nombre_archivo);
	        $end_y=0;
	       $NomDocumentoImp = '';
	       switch ($Datos['tipo_documento'])
		       {
		          
		            case "01":	            	
		                $pdf->NomDocumento='FACTURA ELECTRÓNICA';
		                $NomDocumentoImp = 'Factura Electrónica';
		                break;
		            case "03":
		                $pdf->NomDocumento='BOLETA DE VENTA ELECTRÓNICA';
		                $NomDocumentoImp = 'Boleta de Venta Electrónica';
		                break;
		            case "07": 
		            	$pdf->NomDocumento='NOTA DE CRÉDITO ELECTRÓNICA';
		            	$NomDocumentoImp = 'Nota de Crédito Electrónica';
		            	break;
		            case "08": 
		            	$pdf->NomDocumento='NOTA DE DÉDITO ELECTRÓNICA';
		            	$NomDocumentoImp = 'Nota de Débito Eletrónica';
		            	break;	                       
		           
		        }
	       
	 		
			
			// Establecer el tipo de letra
	 
			//Si tienes que imprimir carácteres ASCII estándar, puede utilizar las fuentes básicas como
			// Helvetica para reducir el tamaño del archivo.
			$pdf->SetFont('Helvetica', '', 12, '', true);
	 
			// Añadir una página
			// Este método tiene varias opciones, consulta la documentación para más información.
	       	$pdf->Open();
			$pdf->AddPage();
	 		 // ponemos los márgenes 
			//$pdf->SetMargins(15,15); 
			
		     /* incluimos un rectángulo relleno para contener datos del cliente */ 
			//$pdf->Rect(15,47,180,22,'FD','',array(255,255,255));
		    $pdf->SetX(0);
			$pdf->SetFont('Helvetica','B',8);
			$pdf->SetXY(3,40.5);
			$pdf->Cell(3, 5, "RUC/DNI:",0,0,'L');
			$pdf->SetFont('Helvetica','',8);
			$pdf->SetXY(20,40.5);
			$pdf->Cell(10, 5, $arr['Encabezado']['Receptor']['RUCReceptor'],0,0,'L');
			$pdf->SetFont('Helvetica','B',10);	


			$pdf->Ln(4);
			$pdf->SetX(3);
			$pdf->SetFont('Helvetica','B',8);			
			$pdf->SetXY(3,45);

			$pdf->Cell(17, 5, "Cliente:".$end_y,0,0,'L');
			$end_y = $pdf->GetY();
			$pdf->SetFont('Helvetica','',8);
			//$pdf->Cell(17, 48, $arr['Encabezado']['Receptor']['RznSoc'],0,0,'L');
			$pdf->MultiCell(68,2,$arr['Encabezado']['Receptor']['RznSoc'],0,'L',FALSE,1,20,$end_y); //51
			
			//$pdf->Ln();
			$pdf->SetX(3);	
			$end_y = $pdf->GetY();
			$pdf->SetXY(3,$end_y);
			$pdf->SetFont('Helvetica','B',8);
			$pdf->Cell(17, 2, "Dirección:",0,0,'L'); 
			$pdf->SetFont('Helvetica','',7);
			$pdf->MultiCell(68,8,$arr['Encabezado']['Receptor']['Direccion'],0,'L',FALSE,1,20,$end_y+0.5); //54.5
			 
			
			//$pdf->Ln(4);
			$pdf->SetX(3);
			// get the new Y
			$end_y = $pdf->GetY();
			$pdf->SetXY(3,$end_y);
			$pdf->SetFont('Helvetica','B',8);
			$pdf->Cell(30, 2, "Fecha de Emisión:"); //,FALSE,1,2,$pdf->GetY()

			$pdf->SetXY(30,$end_y);
			$pdf->SetFont('Helvetica','',8);			
			$pdf->Cell(30, 2, $Datos['fecharegistro'],0,0,'L'); 
			//$pdf->SetFont('Helvetica','B',8);
			//$pdf->SetXY(2,44);  
			//$pdf->Cell(30, 48, "Moneda:",0,0,'L');
			//$pdf->SetFont('Helvetica','',8);
			//$pdf->SetXY(30,44);
			//$pdf->Cell(30, 48, 'Soles',0,0,'L'); 

			$pdf->Ln(4);
			$pdf->SetX(3);			
			$end_y = $pdf->GetY();
			$pdf->SetXY(3,$end_y);

			$pdf->SetFont('Helvetica','B',8);
			$pdf->Cell(30, 2, "Nº Habitación:",0,0,'L');
			$pdf->SetFont('Helvetica','',8);
			$pdf->SetXY(30,$end_y);
			$pdf->Cell(30, 2, $NHabitacion,0,0,'L'); 

			//$end_y = $pdf->GetY();
			$pdf->Ln(4);
			$end_y = $pdf->GetY();
			$pdf->SetX(1);
			$pdf->SetXY(1,$end_y);
		    $pdf->Cell(100,5,"------------------------------------------------------------------------------------",0,0,'L');
		    $pdf->Ln(3);
		    // Anchuras de las columnas
		    //$w = array(10, 20, 16, 95,18,22);
		    $w = array(43,8,11,14);
		    //$pdf->Cell(100, 5, "                                        ".$pdf->GetY(),0,0,'L');
		    // Títulos de las columnas		
			$pdf->SetFont('Helvetica','B',8);
			$header = array('Descripción','Cant.','P. Unit.','Total');
			$end_y = $pdf->GetY();
			$pdf->SetXY(3,$end_y); 
			
		    // Cabeceras
		    for($i=0;$i<count($header);$i++)		    	
		    	$pdf->Cell($w[$i],7,$header[$i],0,0,'L',0);
		    	
		    	
				
		    $pdf->Ln();		    
		    // Datos
		    $i=1;
		    $pdf->SetFont('Helvetica','',7);
		   	

		   	if($Datos['Moneda']=='PEN'):
		   		$CodMoneda='S/';
		   	else:
		   		$CodMoneda='$';
		   	endif;

		    foreach($productos as $row)
		    {
			
		    	$pdf->SetX(3);
		        
				$current_y = $pdf->GetY(); 
				$current_x = $pdf->GetX(); 

				$acotado = $row['pro_desc'];
				$pdf->MultiCell($w[0],4,$acotado,0,'L'); //$pdf->SetXY(149,$y);		       
		        
				$current_x+=$w[0];       //calculate position for next cell 
				$pdf->SetXY($current_x, $current_y);    //set position for next cell to print

		        $pdf->MultiCell($w[1],4,$row['pro_cantidad'],'0','R');

				$current_x+=$w[1];       //calculate position for next cell 		        
				$pdf->SetXY($current_x, $current_y);    //set position

				$pdf->MultiCell($w[2],4, number_format(($row['pro_total'] / $row['pro_cantidad']),2),'0','R');
		        
				$current_x+=$w[2];       //calculate position for next cell 
				$pdf->SetXY($current_x, $current_y);    //set position
		        
		        //$pdf->SetFont('Helvetica','B',7);
		        $pdf->MultiCell($w[3],4,$CodMoneda.' '.number_format(($row['pro_total']),2),0,'R');       
				$current_x+=$w[3];       //calculate position for next cell 

				$pdf->Ln();
		        $i++;
		       $pdf->SetFont('Helvetica','',7);
		    }
		   	
		   	/*TOTALES*/
		   	$pdf->TotGravadas=$CodMoneda." ".number_format($Datos['TotGravada'],2);
		   	$pdf->TotGratuitas=$CodMoneda." ".number_format($Datos['TotGratuitas'],2);
		   	$pdf->TotExoneradas=$CodMoneda." ".number_format($Datos['TotExoneradas'],2);
		   	$pdf->TotInafectas=$CodMoneda." ".number_format($Datos['TotInafectas'],2);


			//$pdf->Ln();
			
			//VALIDACION PARA EL DESCUENTO GLOBAL
			
			if($Datos['DescuentoGlobal']>0){
		        $Datos['TotVenta']=number_format($Datos['TotVenta'] - $Datos['DescuentoGlobal'],2);
		        $Datos['TotGravada']=$Datos['TotVenta'] / 1.18;
		        $Datos['TotIgv']=number_format($Datos['TotVenta'] - $Datos['TotGravada'],2);
		    }


			$pdf->SetX(0);
			$pdf->Cell(100,5,"------------------------------------------------------------------------------------------------",0,0,'L');
			$pdf->Ln();
			$pdf->SetFont('Helvetica','',7);
			$pdf->Cell(18,5,"OP. GRAVADAS: ".$CodMoneda,0,0,'R');
			$pdf->SetFont('Helvetica','B',7);
			$pdf->Cell(48,5,number_format($Datos['TotGravada'],2),0,0,'R');
			$pdf->Ln();
			$pdf->SetFont('Helvetica','',7);
			$pdf->Cell(21,5,"OP. EXONERADAS: ".$CodMoneda,0,0,'R');
			$pdf->SetFont('Helvetica','B',7);
			$pdf->Cell(45,5,number_format($Datos['TotExoneradas'],2),0,0,'R');
			$pdf->Ln();
			$pdf->SetFont('Helvetica','',7);
			$pdf->Cell(18,5,"OP. INAFECTAS: ".$CodMoneda,0,0,'R');
			$pdf->SetFont('Helvetica','B',7);
			$pdf->Cell(48,5,number_format($Datos['TotInafectas'],2),0,0,'R');
			$pdf->Ln();
			$pdf->SetFont('Helvetica','',7);
			$pdf->Cell(18,5,"OP. GRATUITAS: ".$CodMoneda,0,0,'R');
			$pdf->SetFont('Helvetica','B',7);
			$pdf->Cell(48,5,number_format($Datos['TotGratuitas'],2),0,0,'R');
			$pdf->Ln();
			$pdf->SetFont('Helvetica','',7);
			$pdf->Cell(18,5,"IGV: ".$CodMoneda,0,0,'R');
			$pdf->SetFont('Helvetica','B',7);
			$pdf->Cell(48,5,str_replace(',', '', $Datos['TotIgv']),0,0,'R'); //number_format($Datos['TotIgv'],2)
			$pdf->Ln();
			$pdf->SetFont('Helvetica','',7);
			$pdf->Cell(18,5,"DESCUENTO: ".$CodMoneda,0,0,'R');
			$pdf->SetFont('Helvetica','B',7);
			$pdf->Cell(48,5,number_format($Datos['DescuentoGlobal'],2),0,0,'R');
			$pdf->Ln();
			$pdf->SetFont('Helvetica','',7);
			$pdf->Cell(18,5,"TOTAL: ".$CodMoneda,0,0,'R');
			$pdf->SetFont('Helvetica','B',7);
			$pdf->Cell(48,5,number_format($Datos['TotVenta'],2),0,0,'R');
			$pdf->Ln();
			$pdf->SetX(40);			
			$pdf->Cell(40,5,"-----------------------------------------------------------------------------------------------",0,0,'R');
			$pdf->Ln();
			$pdf->SetX(5);
			
			//$pdf->Cell(50,5,"SON ".$MontoLetras,0,0,'L');
			//$yy1=$pdf->GetY();
			$pdf->MultiCell(70,4,"SON: ".$MontoLetras,0,'L',FALSE,1,6,$pdf->GetY()); 
			//$pdf->Ln();
			$pdf->SetX(40);
			$pdf->Cell(40,5,"-----------------------------------------------------------------------------------------------",0,0,'R');
			$pdf->Ln();
		    $datosAdicionales_CDB=$arr['Encabezado']['Emisor']['RUCEmisor']."|".$Datos['tipo_documento']."|".$NumDocumento."|".$Datos['TotIgv']."|".$Datos['TotVenta']."|".$Datos['fecharegistro']."|".$arr['Encabezado']['Receptor']['TipoDocumento']."|".$arr['Encabezado']['Receptor']['RUCReceptor']; 
		    
		           
			// set style for barcode
			$style = array(
			    'border' => 2,
			    'vpadding' => 'auto',
			    'hpadding' => 'auto',
			    'fgcolor' => array(0,0,0),
			    'bgcolor' => false, //array(255,255,255)
			    'module_width' => 1, // width of a single module in points
			    'module_height' => 1 // height of a single module in points
			);
			$pdf->CodBarras=$datosAdicionales_CDB;
			$pdf->SetX(2);
			$pdf->Ln();
			$pdf->SetX(10);
			//$pdf->Cell(15,5,$firma,0,'C');	
			
			//$pdf->Cell(2,5,'Representación impresa de la '.ucwords(strtolower($pdf->strtolower_utf8($pdf->NomDocumento))).'',0,'C');
			$pdf->Cell(2,5,'Representación impresa de la '.$NomDocumentoImp,0,'C');
			//$pdf->Cell(2,5,'Representación impresa del comprobante electronico'.'',0,'J');	
			$pdf->Ln(2);
			$alto=$pdf->GetY();
			$pdf->SetX(25);
			$pdf->Ln(2);
			$pdf->Cell(2,8,$pdf->write2DBarcode($datosAdicionales_CDB, 'QRCODE', 35, $alto+5, 20, 20, $style, 'N'),0,'J');			
			$pdf->Ln(2);
			$yy=$pdf->GetY();
			$pdf->SetX(26);
			$pdf->Cell(2,5,'Forma de Pago: '.$Datos['formapago'],0,'J'); 			
			
			$pdf->Ln(6);	
			$y=$pdf->GetY();		
			$pdf->MultiCell(70,8,"Gracias... vuelva pronto!!!",0,'C',FALSE,1,6,$y+5); 
		
	        $pdf->Output('PDF/'.$nombre_archivo.'.pdf', 'F');

		}
	 
	}
	
	//echo json_encode($_POST);
	//echo $_POST['idalquiler'];
	$dato=new Generaxml($_POST['idalquiler'],$_POST['tipo_documento'],@$_POST['finicio'],@$_POST['ffin']);
	
	if($_POST['idalquiler']==0 && $_POST['tipo_documento']==0){

		$dato->_xmlResumen();

	}else{
		$dato->generar_xml();
	}
	
?>