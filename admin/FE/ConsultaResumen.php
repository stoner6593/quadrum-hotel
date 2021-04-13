<?php
require_once "../../init.php";

	include("Invoice.php");
	include("generales/convertir.php");
	include "../validar.php";
	include "../config.php";
	include "../include/functions.php";
	include "Firmado.php";
	include "CustomHeaders.php";
	include "Pdf.php";
	include "Barcode.php";
	

	class TimeoutException extends RuntimeException {}
	class Generaxml extends Invoice
	{
		
	
		public $ticket;
		
		function __construct($ticket)
		{
			$this->ticket=$ticket;
			
			
		}

		
		//Consultar ticket a SUNAT
		function Consulta_Ticket(){
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

					
					$respuesta=$client->getStatus(array('ticket' => $this->ticket));

					//print_r($client->__getLastResponse());
					if($respuesta):

						
						$leer=$client->__getLastResponse();					
									

						$xml = new DOMDocument('1.0', 'ISO-8859-1');

			            // Se desactivan los errores al leer el Documento XML
			            $xml->loadxml($leer, LIBXML_NOWARNING | LIBXML_NOERROR);
			            // DOCUMENTO DE RESPUESTA SUNAT
			            $DocumentResponse = $xml->getElementsByTagName('getStatusResponse');
			            $itemsDocumentResponse = $DocumentResponse->item(0)->childNodes;
			            $ResponseValue = null;
			            if ($itemsDocumentResponse->length > 0) {
			                for ($DocRes = 0; $DocRes < $itemsDocumentResponse->length; $DocRes++) {
			                    $Response = $itemsDocumentResponse->item($DocRes);
			                    if (isset($Response->tagName)) {
			                        $valtag = $Response->tagName;
			                        if ($valtag == 'status') {
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
			                        if ($tagResnNodes == 'content') {
			                            $contenido = $ResNodes->item($res)->nodeValue;
			                        }
			                        if ($tagResnNodes == 'statusCode') {
			                            $codigo= $ResNodes->item($res)->nodeValue;
			                        }
			                        
			                    }
			                }
			            }
			         
			         	$contenido1=base64_decode($contenido);
			         	$nom='CDR/R-RC-'.date("Y-m-d").'.zip';
			         
			         	if($codigo ==0){
			         		
			         		file_put_contents('CDR/R-RC-'.date("Y-m-d").'.zip', $contenido1);
			         			
			         		$res=($this->getDataCDR($nom,1));
			         		//print_r($resquest);
			         		$ArrayMessage=array('success'=> array('ReferenceID' => $res['ID_DOCUMENTO_ENVIADO'],'codRespuesta' =>$res['CODIGO'],
								'Description' => $res['DESCRIPCION'],'nombre_archivo'=>"" ),'errors'=>0);
			         		
							
						//201802394024846
						////1530115506765
							
			         	}else if ($codigo ==98) {
			         		# code...
			         		//$res=($this->getDataCDR($nom,1));
			         		$ArrayMessage=array('success'=> array('ReferenceID' => 0,'codRespuesta' =>$codigo,
							'Description' =>("Aún en procesp"),'nombre_archivo'=>"" ),'errors'=>0);
							//echo json_encode($ArrayMessage);

			         	}else if($codigo==99){
			         		file_put_contents('CDR/R-RC-'.date("Y-m-d").'.zip', $contenido1);
			         		$res=($this->getDataCDR2($nom,2));			         	
			         		$ArrayMessage=array('success'=> array('ReferenceID' => $res['ID_DOCUMENTO_ENVIADO'],'codRespuesta' =>$res['CODIGO'],
								'Description' => $res['DESCRIPCION'],'nombre_archivo'=>"" ),'errors'=>0);
			         		
			         	}else{
			         		$ArrayMessage=array('success'=> array('ReferenceID' => 0,'codRespuesta' =>$codigo,
							'Description' =>$contenido,'nombre_archivo'=>"" ),'errors'=>0);
			         	}
			         	$db = new conexion();
						$link = $db->conexion();
							
			            $link->query("UPDATE al_venta SET mensaje_respuesta='".$res['DESCRIPCION']."' WHERE ticket ='".$this->ticket."'");
			            $link->query("UPDATE resumen SET mensaje_respuesta='".$res['DESCRIPCION']."' WHERE codigo_respuesta ='".$this->ticket."'");
						
						
					else:
						$ArrayMessage=json_encode(array('success'=>0,'errors'=>array('getMessage' =>'ERROR SUNAT' ,'getCode'=>0)));
	                	 throw new \Exception($ArrayMessage);
					endif;
					

					
					
				 } catch(TimeoutException $e) {
	            	$ArrayMessage=json_encode(array('success'=>0,'errors'=>array('getMessage' =>'ERROR SUNAT' ,'getCode'=>0)));
	                echo json_encode($ArrayMessage);
	            
	            } catch (Exception $e) {
	                // relanzarla
	                $ArrayMessage=array('success'=>0,'nombre_archivo'=>"",'errors'=>array('getMessage' =>$e->getMessage() ,'getCode'=>$e->getCode()));
	                echo json_encode($ArrayMessage);
	            }	
				
			 } catch(TimeoutException $e) {
	            	$error=json_encode(array('success'=>0,'errors'=>array('getMessage' =>'ERROR SUNAT' ,'getCode'=>0)));
	                echo json_encode($ArrayMessage);	

			} catch (SoapFault $fault) {
				$filtrar=str_replace("soap-env:Client."," ", $fault->faultcode);
			    $ArrayMessage=array('success'=>0,'errors'=>array('getCode'=>$filtrar,'getMessage'=>$fault->faultstring),'nombre_archivo'=>"");
			  
			    
			} catch (Exception $e) { 
			    
			    $ArrayMessage=array('success'=>0,'nombre_archivo'=>"",'errors'=>array('getMessage' =>$e->getMessage() ,'getCode'=>$e->getCode()));
			}

			echo json_encode($ArrayMessage);
			
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
	            $extraido=$zip->extractTo('CDR/');

	            // Se valida que se haya extraido correctamente.
	            if ($readFile !== TRUE) {
	                
	                $error=json_encode(array('success'=>0,'errors'=>array('getMessage' =>'Al extraer la respuesta SUNAT ocurrio un error.' ,'getCode'=>0)));
	                throw new \Exception($error);
	            }

	            /* Si el archivo se extrajo correctamente listamos los nombres de los
				 * archivos que contenia de lo contrario mostramos un mensaje de error
				*/
				if($extraido == TRUE){
				 for ($x = 0; $x < $zip->numFiles; $x++) {
				 	$archivo = $zip->statIndex($x);
				 	$file[]=$archivo['name'];
				 }
					
				}
				else {
				
				  $error=json_encode(array('success'=>0,'errors'=>array('getMessage' =>'Ocurrió un error y el archivo no se pudó descomprimir' ,'getCode'=>0)));
	                throw new \Exception($error);
				}
	            // archivo XML
	            $archivoXML = "CDR/".$file[0];
	            //echo $archivoXML;
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

	            //unlink($archivoXML);

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
	     public function getDataCDR2($archivo, $type = '2')
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
	            $extraido=$zip->extractTo('CDR/');

	            // Se valida que se haya extraido correctamente.
	            if ($readFile !== TRUE) {
	                
	                $error=json_encode(array('success'=>0,'errors'=>array('getMessage' =>'Al extraer la respuesta SUNAT ocurrio un error.' ,'getCode'=>0)));
	                throw new \Exception($error);
	            }

	            /* Si el archivo se extrajo correctamente listamos los nombres de los
				 * archivos que contenia de lo contrario mostramos un mensaje de error
				*/
				if($extraido == TRUE){
				 for ($x = 0; $x < $zip->numFiles; $x++) {
				 	$archivo = $zip->statIndex($x);
				 	$file[]=$archivo['name'];
				 }
				
				}
				else {
				
				  $error=json_encode(array('success'=>0,'errors'=>array('getMessage' =>'Ocurrió un error y el archivo no se pudó descomprimir' ,'getCode'=>0)));
	                throw new \Exception($error);
				}
	            // archivo XML
	            $archivoXML = "CDR/".$file[0];

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

	            //unlink($archivoXML);

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

	 
	}
	
	//echo json_encode($_POST);
	//echo $_POST['idalquiler'];
	$dato=new Generaxml($_POST['ticket']);
	
	$dato->Consulta_Ticket();
	
?>