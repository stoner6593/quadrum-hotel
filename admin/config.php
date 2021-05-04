<?php

	global $mysqli;
	$mysqli = new mysqli("localhost:3306", "root", "stoner93", "quadrum2");
	if ($mysqli->connect_errno) {
		echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno.") " . $mysqli->connect_error;
	}

	$acentos = $mysqli->query("SET NAMES 'utf8'");


	/*
	AGREGADO PARA QUE FUNCIONE CON LAS CLASES DE FE SIN MODIFICAR LOS ARCHIVOS YA EXISTENTES
	 */
	/**
	* 
	*/
	
	class Conexion 
	{
		
		/**
		* Gestiona la conexi�1�3n con la base de datos
		*/

		private $dbhost = 'localhost:3306';

		private $dbuser = 'root';

		private $dbpass = 'stoner93';

		private $dbname = 'quadrum2';
		public function __construct(){}

		public function conexion () {

			/**
			* @return object link_id con la conexi�1�3n
			*/

			$link_id = new mysqli($this->dbhost,$this->dbuser,$this->dbpass,$this->dbname);

			if ($link_id ->connect_error) {

				echo "Error de Connexion ($link_id->connect_errno)

				$link_id->connect_error\n";

				

				exit;

			} else {

				return $link_id;

			}

		}
	}
?>