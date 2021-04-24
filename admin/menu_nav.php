<link href='http://fonts.googleapis.com/css?family=Quicksand:400,700' rel='stylesheet' type='text/css' />
<link href='http://fonts.googleapis.com/css?family=Metrophobic:400,700' rel='stylesheet' type='text/css' />

<style>
	/*Menu Empresa*/
	#buttonempresa {
		width: 100%;
		border-right: 0px solid #585858;
		padding: 0 0 1em 0;
		margin-bottom: 1em;
		font-family: 'Metrophobic', Verdana, Arial, sans-serif;
		background-color: #515151;
		color: #333333;
		font-size:13px;
		font-weight:500;
	}

	#buttonempresa ul {
		list-style: none;
		margin: 0;
		padding: 0;
		border: none;
	}

	#buttonempresa li {
		border-bottom: 1px solid #585858;
		border-bottom-style:solid;
		margin: 0;
		list-style: none;
		list-style-image: none;
	}

	#buttonempresa li a {
		display: block;
		padding: 15px 15px 15px 20px;

		background-color: #515151;
		color: #C4C4C4;
		text-decoration: none;
		width: 100%;
	}

	html>body #buttonempresa li a {
		width: auto;
	}

	#buttonempresa li a:hover {

		background-color: #E1583E;
		color: #fff;
	}
</style>

<ul id="buttonempresa">

    <?php
    	if (isset($_SESSION['xyzrol'])){
	      switch ($_SESSION['xyzrol']) :
              case 'superadmin':
                      ?>
                      <li><a href="index.php"><i class="fa fa-home"></i> &nbsp;&nbsp;&nbsp; Inicio </a></li>
                      <li><a href="control-habitaciones.php"><i class="fa fa-cog"></i> &nbsp;&nbsp;&nbsp; Control</a></li>
                      <li><a href="venta.php"><i class="fa fa-shopping-basket"></i> &nbsp;&nbsp;&nbsp; Venta Productos</a>
                      </li>
                      <li><a href="huespedes.php"><i class="fa fa-users"></i> &nbsp;&nbsp;&nbsp; Huéspedes </a></li>

                      <li><a href="habitaciones.php"><i class="fa fa-hotel"></i> &nbsp;&nbsp;&nbsp; Habitaciones </a></li>

                      <li><a href="productos.php"><i class="fa fa-qrcode"></i> &nbsp;&nbsp;&nbsp; Productos </a></li>
                      <li><a href="servicios.php"><i class="fa fa-qrcode"></i> &nbsp;&nbsp;&nbsp; Servicios </a></li>
                      <li><a href="compra-gastos.php"><i class="fa fa-qrcode"></i> &nbsp;&nbsp;&nbsp; Compras/Gastos</a></li>
                      <li><a href="reporte.php"><i class="fa fa-line-chart"></i> &nbsp;&nbsp;&nbsp; Reporte </a></li>
                      <li><a href="reportecierreturno.php"><i class="fa fa-line-chart"></i> &nbsp;&nbsp;&nbsp; Reporte Cierre
                              Turno </a></li>
                      <li><a href="reporteTransacciones.php"><i class="fa fa-line-chart"></i> &nbsp;&nbsp;&nbsp; Reporte Transacciones </a></li>
                      <li><a href="reporteTransaccionesHistorico.php"><i class="fa fa-line-chart"></i> &nbsp;&nbsp;&nbsp; Reporte Transacciones Historico </a></li>
                      <li><a href="usuarios.php"><i class="fa fa-user"></i> &nbsp;&nbsp;&nbsp; Usuarios </a></li>
                      <li><a href="empleado.php"><i class="fa fa-group"></i> &nbsp;&nbsp;&nbsp; Empleados </a></li>
                      <li><a href="listadosunat.php"><i class="fa fa-list-ol"></i> &nbsp;&nbsp;&nbsp;Enviados a SUNAT </a>
                      </li>
                      <li><a href="resumendiario.php"><i class="fa fa-list"></i> &nbsp;&nbsp;&nbsp;Resumen diario </a></li>
                      <li><a href="resumendiariohistorico.php"><i class="fa fa-list-alt"></i> &nbsp;&nbsp;&nbsp;Listado de alquileres</a></li>
                      <li><a href="resumencontable.php"><i class="fa fa-list-alt"></i> &nbsp;&nbsp;&nbsp;Resumen Contable</a>
                      </li>
<!--                  <li><a href="librohuespedes.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Libro Huéspedes</a></li>    -->
                      <li><a href="librohuespedesmincetur.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Libro Huéspedes mincetur</a></li>
                      <li><a href="resumenAlquileresAnuladosPrincipal.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Reporte
                              Alquileres Anulados</a></li>
                      <li><a href="resumenAlquileresAnulados.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Reporte
                              Alquileres con Detalle Anulado</a></li>
                      <li><a href="reporteMantenimiento.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Reporte de mantenimiento</a></li>
                      <li><a href="reporteCuentasCobrar.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Reporte Cuentas por cobrar</a></li>
                      <li><a href="reporteVentaProducto.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Reporte Venta de producto</a></li>
                      <li><a href="reporteReservas.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Reporte por tipo de reserva</a></li>
                      <li><a href="reporteReservas2.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Reporte de reserva</a></li>
                      <li><a href="listadoresumendiario.php"><i class="fa fa-list-ul"></i> &nbsp;&nbsp;&nbsp;Listado resumen
                              diario </a></li>
                      <li><a href="comunicacionBaja.php"><i class="fa fa-download"></i> Comunicación de Baja</a></li>
                      <li><a href="listadocomunicacionBaja.php"><i class="fa fa-cloud-download"></i> Listado Comunicación de Baja</a></li>
                      <li><a href="tarifa-especial.php"><i class="fa fa-money"></i> Tarifa Especial</a></li>
                      <li><a href="ajustarCaja.php"><i class="fa fa-money"></i> Ajustar caja</a></li>
                      <li><a href="series.php"><i class="fa fa-list-alt"></i> &nbsp;&nbsp;&nbsp;Series</a></li>
                      <?php
                  break;
	        case 'admin':
                if($_SESSION['estadomenu'] != 0) {
                ?>
                <li><a href="index.php"><i class="fa fa-home"></i> &nbsp;&nbsp;&nbsp; Inicio </a></li>
                <li><a href="control-habitaciones.php"><i class="fa fa-cog"></i> &nbsp;&nbsp;&nbsp; Control</a></li>
                <li><a href="venta.php"><i class="fa fa-shopping-basket"></i> &nbsp;&nbsp;&nbsp; Venta Productos</a>
                </li>
                <li><a href="huespedes.php"><i class="fa fa-users"></i> &nbsp;&nbsp;&nbsp; Huéspedes </a></li>

                <li><a href="habitaciones.php"><i class="fa fa-hotel"></i> &nbsp;&nbsp;&nbsp; Habitaciones </a></li>

                <li><a href="productos.php"><i class="fa fa-qrcode"></i> &nbsp;&nbsp;&nbsp; Productos </a></li>
                <li><a href="servicios.php"><i class="fa fa-qrcode"></i> &nbsp;&nbsp;&nbsp; Servicios </a></li>
                <li><a href="compra-gastos.php"><i class="fa fa-qrcode"></i> &nbsp;&nbsp;&nbsp; Compras/Gastos</a></li>
                <li><a href="reporte.php"><i class="fa fa-line-chart"></i> &nbsp;&nbsp;&nbsp; Reporte </a></li>
                <li><a href="reportecierreturno.php"><i class="fa fa-line-chart"></i> &nbsp;&nbsp;&nbsp; Reporte Cierre
                        Turno </a></li>
                <li><a href="reporteTransacciones.php"><i class="fa fa-line-chart"></i> &nbsp;&nbsp;&nbsp; Reporte Transacciones </a></li>
                <li><a href="reporteTransaccionesHistorico.php"><i class="fa fa-line-chart"></i> &nbsp;&nbsp;&nbsp; Reporte Transacciones Historico </a></li>
                <li><a href="usuarios.php"><i class="fa fa-user"></i> &nbsp;&nbsp;&nbsp; Usuarios </a></li>
<!--            <li><a href="empleado.php"><i class="fa fa-group"></i> &nbsp;&nbsp;&nbsp; Empleados </a></li>   -->
                <li><a href="listadosunat.php"><i class="fa fa-list-ol"></i> &nbsp;&nbsp;&nbsp;Enviados a SUNAT </a>
                </li>
                <li><a href="resumendiario.php"><i class="fa fa-list"></i> &nbsp;&nbsp;&nbsp;Resumen diario </a></li>
                <li><a href="listadoresumendiario.php"><i class="fa fa-list-ul"></i> &nbsp;&nbsp;&nbsp;Listado resumen diario </a></li>
                <li><a href="resumendiariohistorico.php"><i class="fa fa-list-alt"></i> &nbsp;&nbsp;&nbsp;Listado de alquileres</a></li>
                <li><a href="resumencontable.php"><i class="fa fa-list-alt"></i> &nbsp;&nbsp;&nbsp;Resumen Contable</a>
                </li>
<!--                <li><a href="librohuespedes.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Libro Huéspedes</a></li>  -->
                <li><a href="librohuespedesmincetur.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Libro Huéspedes mincetur</a></li>
                <li><a href="resumenAlquileresAnuladosPrincipal.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Reporte
                            Alquileres Anulados</a></li>
                <li><a href="resumenAlquileresAnulados.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Reporte
                        Alquileres con Detalle Anulado</a></li>
                <li><a href="reporteMantenimiento.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Reporte de mantenimiento</a></li>
                <li><a href="reporteCuentasCobrar.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Reporte Cuentas por cobrar</a></li>
                <li><a href="reporteVentaProducto.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Reporte Venta de producto</a></li>
                <li><a href="reporteReservas.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Reporte por tipo de reserva</a></li>
                <li><a href="reporteReservas2.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Reporte de reserva</a></li>
                
<!--                <li><a href="cargaDataNube.php"><i class="fa fa-list-alt"></i> &nbsp;&nbsp;&nbsp;Carga de Comprobantes a
                        la Nube</a></li> -->
<!--							<li><a href="tarifa-especial.php"><i class="fa fa-money"></i> Tarifa Especial</a></li>  -->	
<!--								<li><a href="series.php"><i class="fa fa-list-alt"></i> &nbsp;&nbsp;&nbsp;Series</a></li> -->
                <?php
            }
			break;

                        case 'recep2': 
                        if($_SESSION['estadomenu'] != 0) {
                ?>
 <li><a href="index.php"><i class="fa fa-home"></i> &nbsp;&nbsp;&nbsp; Inicio </a></li>
                <li><a href="control-habitaciones.php"><i class="fa fa-cog"></i> &nbsp;&nbsp;&nbsp; Control</a></li>
                <li><a href="venta.php"><i class="fa fa-shopping-basket"></i> &nbsp;&nbsp;&nbsp; Venta Productos</a>
                </li>
                <li><a href="huespedes.php"><i class="fa fa-users"></i> &nbsp;&nbsp;&nbsp; Huéspedes </a></li>

                <li><a href="habitaciones.php"><i class="fa fa-hotel"></i> &nbsp;&nbsp;&nbsp; Habitaciones </a></li>

                <li><a href="productos.php"><i class="fa fa-qrcode"></i> &nbsp;&nbsp;&nbsp; Productos </a></li>
                <li><a href="servicios.php"><i class="fa fa-qrcode"></i> &nbsp;&nbsp;&nbsp; Servicios </a></li>
                <li><a href="compra-gastos.php"><i class="fa fa-qrcode"></i> &nbsp;&nbsp;&nbsp; Compras/Gastos</a></li>
                <li><a href="reporte.php"><i class="fa fa-line-chart"></i> &nbsp;&nbsp;&nbsp; Reporte </a></li>
                <li><a href="reportecierreturno.php"><i class="fa fa-line-chart"></i> &nbsp;&nbsp;&nbsp; Reporte Cierre
                        Turno </a></li>
                <li><a href="reporteTransacciones.php"><i class="fa fa-line-chart"></i> &nbsp;&nbsp;&nbsp; Reporte Transacciones </a></li>
                <li><a href="reporteTransaccionesHistorico.php"><i class="fa fa-line-chart"></i> &nbsp;&nbsp;&nbsp; Reporte Transacciones Historico </a></li>
                <li><a href="usuarios.php"><i class="fa fa-user"></i> &nbsp;&nbsp;&nbsp; Usuarios </a></li>
<!--            <li><a href="empleado.php"><i class="fa fa-group"></i> &nbsp;&nbsp;&nbsp; Empleados </a></li>   -->
                <li><a href="listadosunat.php"><i class="fa fa-list-ol"></i> &nbsp;&nbsp;&nbsp;Enviados a SUNAT </a>
                </li>
                <li><a href="resumendiario.php"><i class="fa fa-list"></i> &nbsp;&nbsp;&nbsp;Resumen diario </a></li>
                <li><a href="listadoresumendiario.php"><i class="fa fa-list-ul"></i> &nbsp;&nbsp;&nbsp;Listado resumen diario </a></li>
                <li><a href="resumendiariohistorico.php"><i class="fa fa-list-alt"></i> &nbsp;&nbsp;&nbsp;Listado de alquileres</a></li>
                <li><a href="resumencontable.php"><i class="fa fa-list-alt"></i> &nbsp;&nbsp;&nbsp;Resumen Contable</a>
                </li>
<!--                <li><a href="librohuespedes.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Libro Huéspedes</a></li>  -->
                <li><a href="librohuespedesmincetur.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Libro Huéspedes mincetur</a></li>
                <li><a href="resumenAlquileresAnuladosPrincipal.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Reporte
                            Alquileres Anulados</a></li>
                <li><a href="resumenAlquileresAnulados.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Reporte
                        Alquileres con Detalle Anulado</a></li>
                <li><a href="reporteMantenimiento.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Reporte de mantenimiento</a></li>
                <li><a href="reporteCuentasCobrar.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Reporte Cuentas por cobrar</a></li>
                <li><a href="reporteVentaProducto.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Reporte Venta de producto</a></li>
                <li><a href="reporteReservas.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Reporte por tipo de reserva</a></li>
                <li><a href="reporteReservas2.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Reporte de reserva</a></li>
                <?php
                        }
                break;
			case 'recep':
                if($_SESSION['estadomenu'] != 0) {
                    ?>
                    <li><a href="index.php"><i class="fa fa-home"></i> &nbsp;&nbsp;&nbsp; Inicio </a></li>
                    <li><a href="control-habitaciones.php"><i class="fa fa-cog"></i> &nbsp;&nbsp;&nbsp; Control </a>
                    </li>
                    <li><a href="venta.php"><i class="fa fa-shopping-basket"></i> &nbsp;&nbsp;&nbsp; Venta Productos</a>
                    </li>
                    <li><a href="huespedes.php"><i class="fa fa-users"></i> &nbsp;&nbsp;&nbsp; Huéspedes </a></li>

                    <li><a href="habitaciones.php"><i class="fa fa-hotel"></i> &nbsp;&nbsp;&nbsp; Habitaciones </a></li>

                    <li><a href="productos.php"><i class="fa fa-qrcode"></i> &nbsp;&nbsp;&nbsp; Productos </a></li>
                    <li><a href="servicios.php"><i class="fa fa-qrcode"></i> &nbsp;&nbsp;&nbsp; Servicios </a></li>
                    <li><a href="compra-gastos.php"><i class="fa fa-qrcode"></i> &nbsp;&nbsp;&nbsp; Compras/Gastos</a>
                    </li>
                    <li><a href="reporte.php"><i class="fa fa-line-chart"></i> &nbsp;&nbsp;&nbsp; Reporte </a></li>
                    <li><a href="reporteTransacciones.php"><i class="fa fa-line-chart"></i> &nbsp;&nbsp;&nbsp; Reporte Transacciones </a></li>
                    <li><a href="reporteTransaccionesHistorico.php"><i class="fa fa-line-chart"></i> &nbsp;&nbsp;&nbsp; Reporte Transacciones Historico </a></li>
                    <li><a href="listadosunat.php"><i class="fa fa-list-ol"></i> &nbsp;&nbsp;&nbsp;Enviados a SUNAT </a>

                    <li><a href="resumendiariohistorico.php"><i class="fa fa-list-alt"></i> &nbsp;&nbsp;&nbsp;Listado de Alquileres</a></li>
                    <li><a href="librohuespedes.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Libro Huéspedes</a>
                    </li>
                    <li><a href="librohuespedesmincetur.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Libro Huéspedes mincetur</a></li>
                    <li><a href="listadoresumendiario.php"><i class="fa fa-list-ul"></i> &nbsp;&nbsp;&nbsp;Listado
                            resumen diario </a></li>

                    <?php
                }
			break;
			case 'recep2':
                if($_SESSION['estadomenu'] != 0) {
                ?>
                <li><a href="index.php"><i class="fa fa-home"></i> &nbsp;&nbsp;&nbsp; Inicio </a></li>
                <li><a href="control-habitaciones.php"><i class="fa fa-cog"></i> &nbsp;&nbsp;&nbsp; Control</a></li>
                <li><a href="venta.php"><i class="fa fa-shopping-basket"></i> &nbsp;&nbsp;&nbsp; Venta Productos</a>
                </li>
                <li><a href="huespedes.php"><i class="fa fa-users"></i> &nbsp;&nbsp;&nbsp; Huéspedes </a></li>

                <li><a href="habitaciones.php"><i class="fa fa-hotel"></i> &nbsp;&nbsp;&nbsp; Habitaciones </a></li>

                <li><a href="productos.php"><i class="fa fa-qrcode"></i> &nbsp;&nbsp;&nbsp; Productos </a></li>
                <li><a href="servicios.php"><i class="fa fa-qrcode"></i> &nbsp;&nbsp;&nbsp; Servicios </a></li>
                <li><a href="compra-gastos.php"><i class="fa fa-qrcode"></i> &nbsp;&nbsp;&nbsp; Compras/Gastos</a></li>
                <li><a href="reporte.php"><i class="fa fa-line-chart"></i> &nbsp;&nbsp;&nbsp; Reporte </a></li>
                <li><a href="reporteTransacciones.php"><i class="fa fa-line-chart"></i> &nbsp;&nbsp;&nbsp; Reporte Transacciones </a></li>
<!--            <li><a href="empleado.php"><i class="fa fa-group"></i> &nbsp;&nbsp;&nbsp; Empleados </a></li>   -->
                <li><a href="listadosunat.php"><i class="fa fa-list-ol"></i> &nbsp;&nbsp;&nbsp;Enviados a SUNAT </a>
                </li>
                <li><a href="listadoresumendiario.php"><i class="fa fa-list-ul"></i> &nbsp;&nbsp;&nbsp;Listado resumen diario </a></li>
                <li><a href="resumendiariohistorico.php"><i class="fa fa-list-alt"></i> &nbsp;&nbsp;&nbsp;Listado de alquileres</a></li>
                </li>
<!--                <li><a href="librohuespedes.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Libro Huéspedes</a></li>  -->
                <li><a href="librohuespedesmincetur.php"><i class="fa fa-book"></i> &nbsp;&nbsp;&nbsp;Libro Huéspedes mincetur</a></li>
                
<!--                <li><a href="cargaDataNube.php"><i class="fa fa-list-alt"></i> &nbsp;&nbsp;&nbsp;Carga de Comprobantes a
                        la Nube</a></li> -->
<!--							<li><a href="tarifa-especial.php"><i class="fa fa-money"></i> Tarifa Especial</a></li>  -->	
<!--								<li><a href="series.php"><i class="fa fa-list-alt"></i> &nbsp;&nbsp;&nbsp;Series</a></li> -->
                <?php
            }
			break;
			case 'mante':
	?>
                <li><a  href="control-mantenimiento.php"><i class="fa fa-cog"></i> &nbsp;&nbsp;&nbsp; Control Mantenimiento</a></li>
	<?php
			break;
		  endswitch;
		}
	?>
    <li><a href="salir.php"><i class="fa fa-lock"></i>  &nbsp;&nbsp;&nbsp; Salir </a></li>
</ul>
<script type="text/javascript">
	function abrir(){
		window.open("../Factura", "_blank");
	}
</script>
