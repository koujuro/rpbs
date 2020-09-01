<?php
 
	include 'db_config.php';
	$con = mysqli_connect($servername, $username, $password, $dbname);
	$response = array();
	mysqli_set_charset($con, "utf8");
	
	$companyName = (string) $_POST['companyName'];
	$barcode = (int) $_POST['barcode'];
	$klijent = (string) $_POST['klijent'];
	$klijentObjekat = (string) $_POST['klijentObjekat'];
	$dateTimeMillis = (int) $_POST['dateTimeMillis'];
	$tipUredjaja = (string) $_POST['tipUredjaja'];
	

	$sql_id_0 = "SELECT id FROM companies WHERE companyName='$companyName' LIMIT 1";
	$result_id_0 = mysqli_query($con, $sql_id_0);
	$obj_0 = mysqli_fetch_array($result_id_0);
	$companyID = (int) $obj_0["id"]; 
	
	$sql_id_1 = "SELECT id FROM barcodes WHERE allowedBarcodes='$barcode' LIMIT 1";
	$result_id_1 = mysqli_query($con, $sql_id_1);
	$obj_1 = mysqli_fetch_array($result_id_1); 
	$barcodeID = (int) $obj_1["id"]; 
	
	$sql_id_2 = "SELECT id FROM clients WHERE clientName='$klijent' AND companyID='$companyID' LIMIT 1";
	$result_id_2 = mysqli_query($con, $sql_id_2);
	$obj_2 = mysqli_fetch_array($result_id_2);
	$clientID = (int) $obj_2["id"]; 
	
	$sql_id_3 = "SELECT id FROM objects WHERE clientID='$clientID' AND objectName='$klijentObjekat' LIMIT 1";
	$result_id_3 = mysqli_query($con, $sql_id_3);
	$obj_3 = mysqli_fetch_array($result_id_3);
	$objectID = (int) $obj_3["id"];
 
	$sql_all = "SELECT id FROM sviuredjaji WHERE barcodeID = '$barcodeID'"; 
	$result_all = mysqli_query($con, $sql_all);	
/*
	$response["companyID"] = $companyID;
	$response["barcode"] = $barcode;
	$response["barcodeID"] = $barcodeID;
	$response["objectID"] = $objectID;
	$response["tipUredjaja"] = $tipUredjaja;
	$response["dateTimeMillis"] = $dateTimeMillis;
*/	
	if (mysqli_num_rows($result_all) == 0) { //ne postoji
		$sql_1 = "INSERT INTO sviuredjaji(barcodeID, objectID, type, creationTimeMillis, lastControlMillis) VALUES('$barcodeID', '$objectID', '$tipUredjaja', '$dateTimeMillis', '$dateTimeMillis')";
		$result_1 = mysqli_query($con, $sql_1);
		
		if ($result_1) {
			$sql_update = "UPDATE barcodes SET used='1' WHERE id='$barcodeID' AND companyId='$companyID'";
			$result_update = mysqli_query($con, $sql_update);
	  
			if ($result_update) {		
				$sql_id_4 = "SELECT id FROM sviuredjaji WHERE barcodeID='$barcodeID' LIMIT 1";
				$result_id_4 = mysqli_query($con,$sql_id_4);
				$obj_4 = mysqli_fetch_array($result_id_4);
				$sviUredjajiID = $obj_4["id"];

				allTypesFun();
				
			} else {   
				$response["msg"] = "error9"; 		
				$response["success"] = 0;     
				echo json_encode($response);
			}
		}else {   
			$response["msg"] = "error8"; 
			$response["success"] = 0;     
			echo json_encode($response);
		}
	} else { //vec postoji
		$sql_1 = "UPDATE sviuredjaji SET objectID = '$objectID', type = '$tipUredjaja', lastControlMillis = '$dateTimeMillis' WHERE barcodeID='$barcodeID'";		
		$result_1 = mysqli_query($con, $sql_1);
			
		if ($result_1) {
			$sql_id_4 = "SELECT id FROM sviuredjaji WHERE barcodeID='$barcodeID' LIMIT 1";
			$result_id_4 = mysqli_query($con,$sql_id_4);
			$obj_4 = mysqli_fetch_array($result_id_4);
			$sviUredjajiID = $obj_4["id"];
			
			allTypesFun();
				
		}else{
			$response["msg"] = "error7"; 	
			$response["success"] = 0;     
			echo json_encode($response);
		}  
	}
 


function allTypesFun() {
	global $con, $response, $sviUredjajiID, $tipUredjaja;
	
    if($tipUredjaja=="UPP"){
		$nazivPovPrit = (string) $_POST['nazivPovPrit'];
		
		$sql_upp = "SELECT *FROM upp WHERE PPAsId = '$sviUredjajiID'"; 
		$result_upp = mysqli_query($con, $sql_upp);		
		if (mysqli_num_rows($result_upp) == 0) { //ne postoji
			$sql_2 = "INSERT INTO upp(PPAsId, name) VALUES('$sviUredjajiID', '$nazivPovPrit')";
			$result_2 = mysqli_query($con, $sql_2);
		
			if ($result_2) {
				$response["success"] = 1;      
				echo json_encode($response);	
			}else {   
				$response["msg"] = "error6"; 
				$response["success"] = 0;     
				echo json_encode($response);
			}
		} else { //vec postoji
			$sql_2 = "UPDATE upp SET name='$nazivPovPrit' WHERE PPAsId='$sviUredjajiID'";
			$result_2 = mysqli_query($con, $sql_2);
			
			if ($result_2) {
				$response["success"] = 1;      
				echo json_encode($response);	
			}else {   
				$response["msg"] = "error5"; 
				$response["success"] = 0;     
				echo json_encode($response);
			}  
		}
				
	}else{
		$podtipUredjaja = (string) $_POST['podtipUredjaja'];
				
		if($tipUredjaja=="Hydrants"){
			$oznakaH = (string) $_POST['oznakaH'];
													
			$sql_h = "SELECT *FROM hidranti WHERE sviUredjajiId = '$sviUredjajiID'";
			$result_h = mysqli_query($con, $sql_h);
			if (mysqli_num_rows($result_h) == 0) { //ne postoji
				
				$sql_3 = "INSERT INTO hidranti(sviUredjajiId, hMark, subType) VALUES('$sviUredjajiID', '$oznakaH', '$podtipUredjaja')";
				$result_3 = mysqli_query($con, $sql_3);
		
				if ($result_3) {
					$response["success"] = 1;      
					echo json_encode($response);	
				}else {  
					$response["msg"] = "error4";  						
					$response["success"] = 0;     
					echo json_encode($response);
				}			
			
			} else { //vec postoji
				$sql_3 = "UPDATE hidranti SET hMark='$oznakaH', subType='$podtipUredjaja' WHERE sviUredjajiId='$sviUredjajiID'";
				$result_3 = mysqli_query($con, $sql_3);
					
				if ($result_3) {
					$response["success"] = 1;      
					echo json_encode($response);	
				}else {  
					$response["msg"] = "error3";  						
					$response["success"] = 0;     
					echo json_encode($response);
				}	
			}
		}else{
			$fabrickiBr = (string) $_POST['fabrickiBr'];
			$proizvodjacData = (string) $_POST['proizvodjacData'];	
			$godinaProizvodnje = (int) $_POST['godinaProizvodnje'];
				
			$sql_pp = "SELECT *FROM ppaparati WHERE sviUredjajiId = '$sviUredjajiID'";
			$result_pp = mysqli_query($con, $sql_pp);
			if (mysqli_num_rows($result_pp) == 0) { //ne postoji
				
				$sql_3 = "INSERT INTO ppaparati(sviUredjajiId, fabricId, manufacturerData, creationYear, subType) 
										VALUES('$sviUredjajiID', '$fabrickiBr', '$proizvodjacData', '$godinaProizvodnje', '$podtipUredjaja')";
				$result_3 = mysqli_query($con, $sql_3);		
		
				if ($result_3) {
					$response["success"] = 1;      
					echo json_encode($response);	
				}else {  
					$response["msg"] = "error2";  						
					$response["success"] = 0;     
					echo json_encode($response);
				}			
			
			} else { //vec postoji
				$sql_3 = "UPDATE ppaparati SET fabricId='$fabrickiBr', manufacturerData='$proizvodjacData', creationYear='$godinaProizvodnje', subType='$podtipUredjaja'
							WHERE sviUredjajiId='$sviUredjajiID'";
				$result_3 = mysqli_query($con, $sql_3);
					
				if ($result_3) {
					$response["success"] = 1;      
					echo json_encode($response);	
				}else {  
					$response["msg"] = "error1";  						
					$response["success"] = 0;     
					echo json_encode($response);
				}	
			}															
		}
	}	
}
	
?>