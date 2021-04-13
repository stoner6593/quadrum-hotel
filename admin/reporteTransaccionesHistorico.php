<?php
date_default_timezone_set('America/Lima');
session_start();
include "validar.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Transacciones histórico</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

	<!-- Data Picker-->
	<link rel="stylesheet" type="text/css" href="jqueryui/jquery-ui.css">
	<script src="jqueryui/jquery-ui.js"></script>
	<script src="jqueryui/jquery-1.12.4.js"></script>
	<script src="jqueryui/jquery-ui.js"></script>

	<link rel="stylesheet" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

	<!--<script type="text/javascript" src="../admin/datatable/jquery-1.12.4.js"></script>-->
<script type="text/javascript" src="../admin/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="../admin/datatable/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="../admin/datatable/buttons.flash.min.js"></script>
<script type="text/javascript" src="../admin/datatable/jszip.min.js"></script>
<script type="text/javascript" src="../admin/datatable/pdfmake.min.js"></script>
<script type="text/javascript" src="../admin/datatable/vfs_fonts.js"></script>
<script type="text/javascript" src="../admin/datatable/buttons.html5.min.js"></script>
<script type="text/javascript" src="../admin/datatable/buttons.print.min.js"></script>


	<style type="text/css">
		.nav-item a:hover{
			background-color: #E1583E;
		}

		.tit-pantalla{
			color:#E1583E;
		}

		.bg-dark{
			background-color: #515151 !important;
		}
	</style>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#iniTurnoDesde").datepicker();
			$("#iniTurnoHasta").datepicker();

		    

		    $("#btnGenerar").click(function(){
		    	if($("#iniTurnoDesde").val() !== "" && $("#iniTurnoHasta").val() !== ""){
			    	$.ajax({
			    		url:"include/transacciones/view_reporte_transHistorico.php",
			    		type:"post",
			    		dataType:'json',
			    		data:{
			    			fechaDesde:$("#iniTurnoDesde").val(),
			    			fechaHasta:$("#iniTurnoHasta").val()
			    		},			    		
			    		success:function(resp){
			    			if(resp.success){
			    				var html = "";
			    				for(var i = 0; i < resp.data.length; i++){
			    					console.log(resp.data[i]);
			    					var totGral = 
			    						parseFloat(resp.data[i].totalhabitacion) +
			    						parseFloat(resp.data[i].totalproducto) -
			    						parseFloat(resp.data[i].totaldescuento);

			    					html += "<tr>";
			    					html += "<td>"+resp.data[i].idturno+"</td>";
			    					html += "<td>"+resp.data[i].idusuario+"</td>";
			    					html += "<td>"+(resp.data[i].turno == 1 ? "Dia" : "Noche")+"</td>";
			    					html += "<td>"+resp.data[i].fechaapertura+"</td>";
			    					html += "<td>"+(resp.data[i].fechacierre=="1900-01-01 00:00:00" ? 
			    						"<i>En progreso</i>" : resp.data[i].fechacierre)+"</td>";
			    					html += "<td>S/ "+totGral.toFixed(2)+"</td>";
			    					html += "<td>";
                                    html += "<a ";
                                    html += "href='reporteTransacciones.php?idturno="+
                                    			resp.data[i].idturno+"' target='_blank' ";
                                    html += "class='btn btn-danger mb1 bg-red'>";
                                    html += "<i class='fa fa-print'></i> Reporte</a>";
                                	html += "</td>";
			    					//html += "<td><a href='javascript:verTrans("+resp.data[i].idturno+")'>Ver</a></td>";
			    					html += "</tr>";
			    				}
			    				
			    				$("#row-transacciones").html(html);
			    				$('#myTable').DataTable({
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
					{ "sTitle": "ID Turno"},
					{ "sTitle": "Usuario" },
					{ "sTitle": "Turno"},
					{ "sTitle": "Inicio" },
					{ "sTitle": "Fin" },
					{ "sTitle": "Total General" },
					{ "sTitle": "Acción" }
				],
				 buttons: [
		            {
		                extend: 'print',
		                exportOptions: {
		                  columns: [0,1,2,3,4,5]
		                }
		            },
		            {
		                extend: 'excel',
		                exportOptions: {
		                  columns: [0,1,2,3,4,5]
		                }
		            },
		            {
		                extend: 'pdf',
		                exportOptions: {
		                  columns: [0,1,2,3,4,5]
		                }
		            }
	            ]
			});
			    			}
			    		},
			    		error:function(resp){
			    			alert("Error de servidor, reintente.");
			    		}
			    	});
		    	}else{
		    		alert("Especifica un rango de fechas");
		    	}
		    });

		    $("#btnClose, #otroClose").click(function(){
		    	$("#modaldetalle").hide("toggle");
		    });
		});

		function verTrans(idTurno){
			$.ajax({
	    		url:"include/transacciones/view_reporte_transHistoricoUno.php",
	    		type:"post",
	    		dataType:'json',
	    		data:{
	    			idTurno:idTurno
	    		},			    		
	    		success:function(resp){
	    			if(resp.success){
	    				var register = resp.data[0];
	    				var vt = resp.dataVT;
	    				var pt = resp.dataPT;

	    				$("#sIdTrans").html(register.idturno);
	    				var html = "";
	    				
	    				html += "<p>Usuario: "+register.user_nombre+"</p>";
	    				html += "<p>Apertura: "+register.fechaapertura+"</p>";
	    				html += "<p>Cierre: "+register.fechacierre+"</p>";

	    				html += "<br/>";

	    				html += "<h4>Ingresos de turno</h4>";

	    				html += "<p>Ingreso de Habitaciones: S/ "+register.totalhabitacion+"</p>";

						html += "<table>";
						html += "<tr>";
						html += "<th>Orden</th>";
						html += "<th>Habitación</th>";
						html += "<th>Huesped</th>";
						html += "<th>Total</th>";
						html += "</tr>";
						for(var i = 0; i < vt.length; i++){
							html += "<tr>";
							html += "<td>"+vt[i].orden+"</td>";
							html += "<td>"+vt[i].habitacion+"</td>";
							html += "<td>"+vt[i].huesped+"</td>";
							html += "<td>S/ "+vt[i].total+"</td>";
							html += "</tr>";
						}
						html += "</table>";

						html += "<br/>";

						html += "<p>Ingreso de Productos/Servicios: S/ "+register.totalproducto+"</p>";

						html += "<table>";
						html += "<tr>";
						html += "<th>Orden</th>";
						html += "<th>Huesped</th>";
						html += "<th>Total</th>";
						html += "</tr>";
						for(var i = 0; i < pt.length; i++){
							html += "<tr>";
							html += "<td>"+pt[i].orden+"</td>";
							html += "<td>"+pt[i].huesped+"</td>";
							html += "<td>S/ "+pt[i].total+"</td>";
							html += "</tr>";
						}
						html += "</table>";

						html += "<br/>";

						html += "<p>Total general: S/ "+(parseFloat(register.totalhabitacion)+parseFloat(register.totalproducto)-parseFloat(register.totaldescuento)).toFixed(2)+"</p>";
						html += "<p>Total visa: S/ "+register.totalvisa+"</p>";
						html += "<p>Total efectivo: S/ "+(parseFloat(register.totalefectivo)-parseFloat(register.totaldescuento)).toFixed(2)+"</p>";

	    				$("#cont-modal").html(html);

	    				$("#modaldetalle").show("toggle");
	    			}
	    		},
	    		error:function(resp){
	    			alert("Error de servidor, reintente.");
	    		}
	    	});
		}
	</script>
</head>
<body>
	<nav class="navbar navbar-expand-md bg-dark navbar-dark fixed-top">
		<a class="navbar-brand" href="#">
			<img src="../imagenesv/logohotelpalacemovil.png" alt="">
		</a>
	    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
	        <span class="navbar-toggler-icon"></span>
	    </button>
	    <div class="collapse navbar-collapse" id="collapsibleNavbar">
	        <ul class="navbar-nav w-75">
	            <li class="nav-item">
	                <a class="nav-link" href="#">
	                    Administración del Hotel
	                </a>
	            </li>
	        </ul>
	        <ul class="navbar-nav flex-row-reverse">
	        	<li class="nav-item">
	                <a class="nav-link" href="#">
	                	<span class="fa-stack" style="color:#E1583E;">
	                        <i class="fa fa-circle fa-stack-2x"></i>
	                        <i class="fa fa-user fa-stack-1x" style="color:#FFFFFF;"></i>
	                    </span>
	                    <?php echo $_SESSION['xyznombre'].' ('.$_SESSION['xyzusuario'].')'; ?> 
	                </a>
	            </li>
	        </ul>
	    </div> 
	</nav>
	<div class="row mt-5">
		<div class="col-md-2 mt-3">
			<?php include ("menu_nav.php"); ?>
		</div>
		<div class="col-md-10 mt-3">
			<div class="container mt-3">
				<h5 class="tit-pantalla">Reporte Transacciones por turno (Histórico)</h5>
				<form action="" method="post" name="frmTransHistorico">
					<div class="row">					
						<div class="col-md-4">
							<div class="form-group">
								<label for="iniTurnoDesde">Inicio de turno desde:</label>
								<input type="text" name="iniTurnoDesde" id="iniTurnoDesde" class="form-control" />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="iniTurnoHasta">Inicio de turno hasta:</label>
								<input type="text" name="iniTurnoHasta" id="iniTurnoHasta" class="form-control" />
							</div>
						</div>	
						<div class="col-md-4">
							<button type="button" class="btn btn-primary" id="btnGenerar">Generar</button>
						</div>									
					</div>
				</form>
				<div class="row">
					<div class="col-md-12">
						<table id="myTable" class="display" style="width:100%">
							<thead>
					            <tr>
					                <th>ID Turno</th>
					                <th>Usuario</th>
					                <th>Turno</th>
					                <th>Inicio</th>
					                <th>Fin</th>
					                <th>Total General</th>
					                <th>Acción</th>
					            </tr>
					        </thead>
					        <tbody id="row-transacciones"></tbody>
						</table>
					</div>
				</div>
			</div>			
		</div>
	</div>
	<div id="modaldetalle" class="modal" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-dialog-scrollable" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">
						Reporte de transacciones del turno: <span id="sIdTrans"></span>
					</h5>
					<button type="button" class="close" aria-label="Close" id="otroClose">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" id="cont-modal">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" id="btnClose">Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</body>
</html>