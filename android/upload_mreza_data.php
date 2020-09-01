<?php
 
include 'db_config.php';
$con = mysqli_connect($servername, $username, $password, $dbname);
mysqli_set_charset($con, "utf8");

	$companyName = (string) $_POST['companyName'];
	$klijent = (string) $_POST['klijent'];
	$klijentObjekat = (string) $_POST['klijentObjekat'];
	$operator = (string) $_POST['operator'];
	$longitude = (double) $_POST['longitude'];
	$latitude = (double) $_POST['latitude'];
	//$dateTime = (string) $_POST['dateTime'];
	$dateTimeMillis = (int) $_POST['dateTimeMillis'];
	$statickiMrezniPritisak = (double) $_POST['statickiMrezniPritisak'];
	$dinamickiMrezniPritisak = (double) $_POST['dinamickiMrezniPritisak'];
	$m3hMreza = (double) $_POST['m3hMreza'];
	$lsMreza = (double) $_POST['lsMreza'];
	$precnikUsnika = (double) $_POST['precnikUsnika'];
	$stanjeMrezniPritisak = (string) $_POST['stanjeMrezniPritisak'];
	$noteMrezaData = (string) $_POST['noteMrezaData'];
	$skeniraniHidranti = (string) $_POST['skeniraniHidranti'];
	
	

	$sql_id_0 = "SELECT id FROM users WHERE username='$operator' LIMIT 1";
	$result_id_0 = mysqli_query($con, $sql_id_0);
	$obj_0 = mysqli_fetch_array($result_id_0);
	$operatorID = (int) $obj_0["id"];
	
	$sql_id_1 = "SELECT id FROM companies WHERE companyName='$companyName' LIMIT 1";
	$result_id_1 = mysqli_query($con, $sql_id_1);
	$obj_1 = mysqli_fetch_array($result_id_1);
	$companyID = (int) $obj_1["id"]; 
	
	$sql_id_2 = "SELECT id FROM clients WHERE clientName='$klijent' AND companyID='$companyID' LIMIT 1";
	$result_id_2 = mysqli_query($con, $sql_id_2);
	$obj_2 = mysqli_fetch_array($result_id_2);
	$clientID = (int) $obj_2["id"]; 
	
	$sql_id_3 = "SELECT id FROM objects WHERE clientID='$clientID' AND objectName='$klijentObjekat' LIMIT 1";
	$result_id_3 = mysqli_query($con, $sql_id_3);
	$obj_3 = mysqli_fetch_array($result_id_3);
	$objectID = (int) $obj_3["id"];
 
	$sql_1 = "INSERT INTO mrezniprotok(objectID, operatorID, timeMillis, longitude, latitude, lsNetwork, m3hNetwork, gapeDiameter, 
									noteNetworkData, networkPressureState, staticNetworkPressure, dynamicNetworkPressure) 
							VALUES('$objectID', '$operatorID', '$dateTimeMillis', '$longitude', '$latitude', '$lsMreza', '$m3hMreza', '$precnikUsnika', 
									'$noteMrezaData', '$stanjeMrezniPritisak', '$statickiMrezniPritisak', '$dinamickiMrezniPritisak')";
    $result_1 = mysqli_query($con, $sql_1);
     
	 
	$response = array();
    if ($result_1) {
		$sql_id_4 = "SELECT id FROM mrezniprotok WHERE timeMillis='$dateTimeMillis' AND objectID='$objectID' LIMIT 1";
		$result_id_4 = mysqli_query($con, $sql_id_4);
		$obj_4 = mysqli_fetch_array($result_id_4);
		$mrezniprotokID = (int) $obj_4["id"];

		$scannedNet = array();
		$scannedNet = explode("_", $skeniraniHidranti);
		foreach ($scannedNet as $value) {
			$partInfo = array();
			$partInfo = explode("-", $value);
			
			$barcode = $partInfo[0];
			$staticPressure = $partInfo[1];
			$dynamicPressure = $partInfo[2];
			
			$sql_id_5 = "SELECT id FROM barcodes WHERE allowedBarcodes='$barcode' LIMIT 1";
			$result_id_5 = mysqli_query($con, $sql_id_5);
			$obj_5 = mysqli_fetch_array($result_id_5);
			$barcodeID = (int) $obj_5["id"];
		
			$sql_2 = "INSERT INTO scannednetworkhydrants(mrezniProtokID, barcodeID, staticPressure, dynamicPressure) 
											VALUES('$mrezniprotokID', '$barcodeID', '$staticPressure', '$dynamicPressure')";
			$result_2 = mysqli_query($con, $sql_2);
		}

		
        $response["success"] = 1;     
        echo json_encode($response);
	} else {   
		$response["msg"] = "error00"; 		
        $response["success"] = 0;     
        echo json_encode($response);
    }
			


?>