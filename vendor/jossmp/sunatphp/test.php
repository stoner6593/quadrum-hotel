<?php
/*	require_once("./src/autoload.php");
	
	$cliente = new \Sunat\Sunat(true, true);
	//$ruc = "20601075246"; // RUC de 11 digitos
	//$dni = "00000000"; // DNI de 8 digitos
	$documento=$_POST['documento'];
	$tipo_documento=$_POST['tipo_documento'];
	
	if(strlen ($documento)==8 and $tipo_documento==1){
		echo ( $cliente->search( $documento ,true) );
	}else if(strlen ($documento)==11 and $tipo_documento==6){
		echo ( $cliente->search( $documento ,true) );
	}else{
		echo json_encode(array(	"success" 	=> false,
								"msg" 		=> "No se ha encontrado resultados."
							));
	}
	//print_r ( $cliente->search( $ruc ) );
	//print_r ( $cliente->search( $dni ) );*/
	
	$documento=$_POST['documento'];
	$tipo_documento=$_POST['tipo_documento'];

$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjM5YjExNzAxYTg5YmMwYTgzOWE2OTY0NzliZGJmZGFhYmY1NmE3MjVjNWYxODMwYWRiYTAwNmIwYTk3OGViYTFmMzUwMGY2NWI4NTQ5NjY2In0.eyJhdWQiOiIxIiwianRpIjoiMzliMTE3MDFhODliYzBhODM5YTY5NjQ3OWJkYmZkYWFiZjU2YTcyNWM1ZjE4MzBhZGJhMDA2YjBhOTc4ZWJhMWYzNTAwZjY1Yjg1NDk2NjYiLCJpYXQiOjE2MDI5Nzg5ODUsIm5iZiI6MTYwMjk3ODk4NSwiZXhwIjoxOTE4NTExNzg1LCJzdWIiOiIxNjQwIiwic2NvcGVzIjpbIioiXX0.gJyalKSr1C3iQ-k3OmZfSpCskTGDztASXcNn4qUSya5E74vJfJ0zSpdmwmOmNtpBuNQP-07UQth7w7JSVGBogGeCESBiqyEYFt92MBlmuFYppB2Nnqt9vW2BrMc_xtQ-3glGQsgWtxDjlApohAnLYKGNzFOIEeg4Ziv2yF_biR7pM4F4h9dKY6tNCvljWzSrM0-L5bU7eOXrBZZdatdU8E5hsflFSapoZFSKRaVzGrMC3uQMx8XVfrdG1MmUtIpOWx8AFXNE3OcEn_I1PevEECw6G7I1kS_EaddK5l-SIolVGGBMN5D0aXgUtENj18KZlALMNBsEa4sVbVGtWFGvJy7R3I7WNtJ80TdSVJ1mOF_gKbayJU46pAMWGt1nxEypN4mM0qElx9h0rvnNNwXxU5hvbAc4DpNzYNMa3EsHHq9lBXx-PezosthauCLCXoXa8o3sh9nhrjsTq3mhmVZT00U65ZVu-x7fPLtsy0Ua52WC0IiYFjDlbyFmEM2VKzn3pXfChSy_DGy7LwoacdR9LoLB_XqGbGgL5bZ2rVqCK0PS0t2ZANuHEiyeKiPG_u2loTwCMR-M5UAI4SN_A8phYlPZOVE9Bt9SAyLpbwn0UL5U9cvnQYsmnrg-O7H69yaWfuTy3tgYZJX-WheBWelpObVgvJPUcwt-qUnq41rFaQU"; // Get your token from a cookie or database
$post = array('dni'=>	$documento); // Array of data with a trigger
//$request = jwt_request($token,$post); // Send or retrieve data

       header('Content-Type: application/json'); // Specify the type of data
       $ch = curl_init('https://servicio.apirest.pe/api/getDniPremium'); // Initialise cURL
       $post = json_encode($post); // Encode the data array into a JSON string
       $authorization = "Authorization: Bearer ".$token; // Prepare the authorisation token
       curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization )); // Inject the token into the header
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLOPT_POST, 1); // Specify the request method as POST
       curl_setopt($ch, CURLOPT_POSTFIELDS, $post); // Set the posted fields
       curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // This will follow any redirects
       $result = curl_exec($ch); // Execute the cURL statement
       curl_close($ch); // Close the cURL connection
       print_r(json_encode($result)); // Return the received data
       
       ?>
