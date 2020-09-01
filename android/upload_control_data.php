<?php
 
include 'db_config.php';
$con = mysqli_connect($servername, $username, $password, $dbname);
$response = array();
mysqli_set_charset($con, "utf8");

	$companyName = (string) $_POST['companyName'];
	$operator = (string) $_POST['operator'];
	$barcode = (string) $_POST['barcode'];
	$longitude = (double) $_POST['longitude'];
	$latitude = (double) $_POST['latitude'];
	//$dateTime = (string) $_POST['dateTime'];
	$dateTimeMillis = (int) $_POST['dateTimeMillis'];
	$stanje = (string) $_POST['stanje'];
	$noteData = (string) $_POST['noteData'];
	$lokacijaPP = (string) $_POST['lokacijaPP'];
	$tipUredjaja = (string) $_POST['tipUredjaja'];

	
	$sql_id_0 = "SELECT id FROM companies WHERE companyName='$companyName' LIMIT 1";
	$result_id_0 = mysqli_query($con, $sql_id_0);
	$obj_0 = mysqli_fetch_array($result_id_0);
	$companyID = (int) $obj_0["id"]; //
	
	$sql_id_1 = "SELECT id FROM barcodes WHERE allowedBarcodes='$barcode' LIMIT 1";
	$result_id_1 = mysqli_query($con, $sql_id_1);
	$obj_1 = mysqli_fetch_array($result_id_1); //
	$barcodeID = (int) $obj_1["id"]; //
	
	$sql_id_2 = "SELECT id FROM users WHERE username='$operator' || fullname='$operator' LIMIT 1";
	$result_id_2 = mysqli_query($con, $sql_id_2);
	$obj_2 = mysqli_fetch_array($result_id_2);
	$operatorID = (int) $obj_2["id"]; //
	
	$sql_id_3 = "SELECT id FROM sviuredjaji WHERE barcodeID='$barcodeID' LIMIT 1";
	$result_id_3 = mysqli_query($con, $sql_id_3);
	$obj_3 = mysqli_fetch_array($result_id_3);
	$ppaID = (int) $obj_3["id"];
 
	$sql_1 = "INSERT INTO sviuredjajicontrolhistory(ppaID, operatorID, longitude, latitude, timeControlMillis, note, imgPath, locationPP, curState) 
						VALUES('$ppaID', '$operatorID', '$longitude', '$latitude', '$dateTimeMillis', '$noteData', '-', '$lokacijaPP', '$stanje')";
    $result_1 = mysqli_query($con, $sql_1);
    
/*
 $response["companyID"] = $companyID;
 $response["barcode"] = $barcode;
 $response["barcodeID"] = $barcodeID;
 $response["ppaID"] = $ppaID;
 $response["operatorID"] = $operatorID;
 $response["longitude"] = $longitude;
 $response["latitude"] = $latitude;
 $response["dateTimeMillis"] = $dateTimeMillis;
 $response["noteData"] = $noteData;
 $response["lokacijaPP"] = $lokacijaPP;
 $response["stanje"] = $stanje;
*/
if ($result_1) {		
     
		$sql_id_4 = "SELECT id FROM sviuredjajicontrolhistory WHERE timeControlMillis='$dateTimeMillis' AND ppaID='$ppaID' LIMIT 1";
		$result_id_4 = mysqli_query($con,$sql_id_4);
		$obj_4 = mysqli_fetch_array($result_id_4);
		$sviUredjajiControlID = (int) $obj_4["id"];
		
		$sql_update = "UPDATE sviuredjaji SET lastControlMillis = '$dateTimeMillis' WHERE barcodeID='$barcodeID'";		
	    $result_update = mysqli_query($con, $sql_update);
		
	if ($result_update) {	
		if($tipUredjaja=="UPP" || $tipUredjaja=="Hydrants"){
			$stanjePritisak = (string) $_POST['stanjePritisak'];
			$noteDataPritisak = (string) $_POST['noteDataPritisak'];
	
			$sql_2 = "INSERT INTO hidrantiuppcontrol(PPAControlId, pressureState, noteDataPressure) 
											VALUES('$sviUredjajiControlID', '$stanjePritisak', '$noteDataPritisak')";
			$result_2 = mysqli_query($con, $sql_2);
			
		/*	$response["sviUredjajiControlID"] = $sviUredjajiControlID;
			$response["stanjePritisak"] = $stanjePritisak;
			$response["noteDataPritisak"] = $noteDataPritisak;
		*/	
			if($result_2){
				$sql_id_5 = "SELECT id FROM hidrantiuppcontrol WHERE PPAControlId='$sviUredjajiControlID' LIMIT 1";
				$result_id_5 = mysqli_query($con,$sql_id_5);
				$obj_5 = mysqli_fetch_array($result_id_5);
				$HUPPID = (int) $obj_5["id"];
				
				if($tipUredjaja=="UPP"){
					$ulazniPritisak = (double) $_POST['ulazniPritisak'];
					$izlazniPritisak = (double) $_POST['izlazniPritisak'];
	
					$sql_3 = "INSERT INTO uppcontrol(HiUPPId, outputPressure, inputPressure) 
											VALUES('$HUPPID', '$izlazniPritisak', '$ulazniPritisak')";
					$result_3 = mysqli_query($con, $sql_3);
					
					if($result_3){
						$response["success"] = 1;     
						echo json_encode($response);
					} else { 
						$response["msg"] = "error4C";
						$response["success"] = 0;     
						echo json_encode($response);
					}	
					
				}elseif($tipUredjaja=="Hydrants"){
					$m3hMreza = (double) $_POST['m3hMreza'];
					$lsMreza = (double) $_POST['lsMreza'];
					$precnikUsnika = (double) $_POST['precnikUsnika'];
					$statickiPritisak = (double) $_POST['statickiPritisak'];
					$dinamickiPritisak = (double) $_POST['dinamickiPritisak'];
	
					$sql_3 = "INSERT INTO hidranticontrol(HiUPPId, staticPressure, dynamicPressure, m3hNetwork, lsNetwork, gapeDiameter) 
											VALUES('$HUPPID', '$statickiPritisak', '$dinamickiPritisak', '$m3hMreza', '$lsMreza', '$precnikUsnika')";
					$result_3 = mysqli_query($con, $sql_3);
					
					if($result_3){
						$response["success"] = 1;     
						echo json_encode($response);
					} else {   					  
						$response["msg"] = "error3C";
						$response["success"] = 0;     
						echo json_encode($response);
					}					
						
				}
				
			} else {   
				$response["msg"] = "error2C";     
				$response["success"] = 0;     
				echo json_encode($response);
			}	
			
		}else{
			$lastHVP = (string) $_POST['lastHVP'];
			$vrstaNeispravnosti = (string) $_POST['vrstaNeispravnosti'];
	
			$sql_2 = "INSERT INTO ppaparaticontrol(sviUredjajiControlId, lastHVP, malfunctionType) 
											VALUES('$sviUredjajiControlID', '$lastHVP', '$vrstaNeispravnosti')";
			$result_2 = mysqli_query($con, $sql_2);
			
			if($result_2){
				$response["success"] = 1;     
				echo json_encode($response);
			} else {   
				$response["msg"] = "error1C";     
				$response["success"] = 0;     
				echo json_encode($response);
			}	
			
		}
	} else {
		$response["msg"] = "error0C";     
        $response["success"] = 0;     
        echo json_encode($response);
	}
		
	  
} else {
	$response["msg"] = "error00C";     
    $response["success"] = 0;     
    echo json_encode($response);
}

?>