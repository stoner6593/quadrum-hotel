<?php

	
	$documento=$_GET['documento'];


    $token = "8c1025919e3291fd2f34b8a466ad6f143426a7441d375731c306cbb7b308479f"; // Get your token from a cookie or database
   
    header('Content-Type: application/json');
    $cURLConnection = curl_init();
    
    curl_setopt($cURLConnection, CURLOPT_URL, 'https://apiperu.dev/api/ruc/'. $documento);
    $authorization = "Authorization: Bearer ".$token; // Prepare the authorisation token
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    
    $phoneList = curl_exec($cURLConnection);
    curl_close($cURLConnection);
    
    print_r(json_encode($phoneList));
?>
