<?php
include 'db_config.php';
$con = mysqli_connect($servername, $username, $password, $dbname);
 mysqli_set_charset($con, "utf8");
 $response = array();
 
//Podaci o uredjaju
$companyName = $_POST['companyName'];
$klijent = "klijent";
$klijentObjekat = "klijentObjekat";
$barCode = "barCode";
$tipUredjaja = "tipUredjaja";
$podtipUredjaja = "podtipUredjaja";
$lokacijaPP = "lokacijaPP";
$godinaProizvodnje = "godinaProizvodnje";
$proizvodjacData = "proizvodjacData";
$lastHVP = "lastHVP";
$fabrickiBr = "fabrickiBr";
$statickiPritisak = "statickiPritisak";
$dinamickiPritisak = "dinamickiPritisak";
$oznakaH = "oznakaH";
$nazivPovPrit = "nazivPovPrit";

	$sql_id_0 = "SELECT id FROM companies WHERE companyName='$companyName' LIMIT 1";
	$result_id_0 = mysqli_query($con, $sql_id_0);
	$obj_0 = mysqli_fetch_array($result_id_0);
	$companyID = (int) $obj_0["id"]; //

$sql_barcodes = "SELECT *FROM barcodes WHERE companyId='$companyID'";
$result_barcodes = mysqli_query($con, $sql_barcodes) or die(mysqli_error($con));

$sql_clients = "SELECT *FROM clients WHERE companyID='$companyID'";
$result_clients = mysqli_query($con, $sql_clients) or die(mysqli_error($con));


$sql = "SELECT *FROM sviuredjaji";
$result = mysqli_query($con, $sql) or die(mysqli_error($con));

$sql_objects = "SELECT *FROM objects";
$result_objects = mysqli_query($con, $sql_objects) or die(mysqli_error($con));
 
// check for empty result
if (mysqli_num_rows($result_barcodes) > 0 && mysqli_num_rows($result_clients) > 0 && mysqli_num_rows($result_objects) > 0) {
    // looping through all results
	$dataArray = "dataArray";
	$barcodesArray = "barcodesArray";
	$clientsArray = "clientsArray";
	$objectsArray = "objectsArray";
	
    $response[$objectsArray] = array();
    $response[$dataArray] = array();
	$response[$clientsArray] = array();
	$response[$barcodesArray] = array();
 
    while ($row = mysqli_fetch_array($result)) {
		$bID = $row["barcodeID"];
		
		$sql_check = "SELECT allowedBarcodes, companyId FROM barcodes WHERE id='$bID' LIMIT 1";
		$result_check = mysqli_query($con, $sql_check);
		$obj_check = mysqli_fetch_array($result_check);
		$comp = (int) $obj_check["companyId"];
		
		if($companyID==$comp){
			$product = array();
			$bc = (int) $obj_check["allowedBarcodes"];
			
			$sviUredjajiId = $row["id"];
			$objectID = $row["objectID"];
			$type = $row["type"];
			$milli = $row["lastControlMillis"];		
			
			$sql_id_ppC = "SELECT id, locationPP FROM sviuredjajicontrolhistory WHERE timeControlMillis='$milli' AND ppaID='$sviUredjajiId' LIMIT 1";
			$result_id_ppC = mysqli_query($con, $sql_id_ppC);
			$obj_ppC = mysqli_fetch_array($result_id_ppC);
			$ppaControlHistoryID = (int) $obj_ppC["id"];
	    	$locationPP = (string) $obj_ppC["locationPP"];
		
			$sql_object = "SELECT clientID, objectName FROM objects WHERE id='$objectID' LIMIT 1";
			$result_object = mysqli_query($con, $sql_object);
			$obj_object = mysqli_fetch_array($result_object);
			$clientID = (int) $obj_object["clientID"];
			$objectName = (string) $obj_object["objectName"];
			
			$sql_client = "SELECT clientName FROM clients WHERE id='$clientID' LIMIT 1";
			$result_client = mysqli_query($con, $sql_client);
			$obj_client = mysqli_fetch_array($result_client);
			$clientName = (string) $obj_client["clientName"];
			
		
			
			$product["tipUredjaja"] = $type;
			$product["barCode"] = $bc;
			$product["klijent"] = $clientName;
			$product["klijentObjekat"] = $objectName;
			$product["lokacijaPP"] = $locationPP;
			 
			if($type=="UPP"){
				
				$sql_nameUPP = "SELECT name FROM upp WHERE PPAsId='$sviUredjajiId' LIMIT 1";
				$result_nameUPP = mysqli_query($con, $sql_nameUPP);
				$obj_nameUPP = mysqli_fetch_array($result_nameUPP);
				$nameUPP = (string) $obj_nameUPP["name"];
			 
				$product["nazivPovPrit"] = $nameUPP;
				array_push($response[$dataArray], $product);
			}else{				
				
				if($type=="Hydrants"){
					$sql_hMark = "SELECT hMark, subType FROM hidranti WHERE sviUredjajiId='$sviUredjajiId' LIMIT 1";
					$result_hMark = mysqli_query($con, $sql_hMark);
					$obj_h = mysqli_fetch_array($result_hMark);
					$hMark = (string) $obj_h["hMark"];
					$subType = (string) $obj_h["subType"];
					
					$product["oznakaH"]= $hMark;
					$product["podtipUredjaja"]= $subType;
					
					$sql_hupID = "SELECT id FROM hidrantiuppcontrol WHERE PPAControlId='$ppaControlHistoryID' LIMIT 1";
					$result_hupID = mysqli_query($con, $sql_hupID);
					$obj_hupID = mysqli_fetch_array($result_hupID);
					$hupID = (string) $obj_hupID["id"];
					
					$sql_hc = "SELECT staticPressure, dynamicPressure FROM hidranticontrol WHERE HiUPPId='$hupID' LIMIT 1";
					$result_hc= mysqli_query($con, $sql_hc);
					$obj_hc = mysqli_fetch_array($result_hc);
					$staticPressure = (double) $obj_hc["staticPressure"];
					$dynamicPressure = (double) $obj_hc["dynamicPressure"];
					
					$product["statickiPritisak"]= $staticPressure;
					$product["dinamickiPritisak"]= $dynamicPressure;
					array_push($response[$dataArray], $product);
				}else{
					$sql_lastHVP = "SELECT lastHVP FROM ppaparaticontrol WHERE sviUredjajiControlId='$ppaControlHistoryID' LIMIT 1";
					$result_lastHVP = mysqli_query($con, $sql_lastHVP);
					$obj_lastHVP = mysqli_fetch_array($result_lastHVP);
					$lastHVP = (string) $obj_lastHVP["lastHVP"];
					
					$sql_pp = "SELECT fabricId, manufacturerData, creationYear, subType FROM ppaparati WHERE sviUredjajiId='$sviUredjajiId' LIMIT 1";
					$result_pp = mysqli_query($con, $sql_pp);
					$obj_pp = mysqli_fetch_array($result_pp);
					$fabricId = (string) $obj_pp["fabricId"];
					$manufacturerData = (string) $obj_pp["manufacturerData"];
					$creationYear = (int) $obj_pp["creationYear"];
					$subType = (string) $obj_pp["subType"];
					
					$product["fabrickiBr"]= $fabricId;
					$product["godinaProizvodnje"]= $creationYear;
					$product["proizvodjacData"]= $manufacturerData;
					$product["lastHVP"]= $lastHVP;
					$product["podtipUredjaja"]= $subType;
					
					array_push($response[$dataArray], $product);
				}
			
			}			
		
       
			
		}	
    }

	while ($row = mysqli_fetch_array($result_barcodes)) {
		$product = array();
        $product["allowedBarcodes"] = $row["allowedBarcodes"];
		//$product["used"] = $row["used"];
        array_push($response[$barcodesArray], $product);
    }
	
	while ($row = mysqli_fetch_array($result_clients)) {
		$product = array();
		$id_klijent = $row["id"];
		$product["cID"] = $id_klijent;
        $product["clientName"] = $row["clientName"];
        
        array_push($response[$clientsArray], $product);  
    }
	
	while ($row2 = mysqli_fetch_array($result_objects)) {
		    
		$product = array();
		$product["objectName"] = $row2["objectName"];
		$product["clientID"] = $row2["clientID"];
		
		array_push($response[$objectsArray], $product);  
				
	}
  
    $response["success"] = 1;
    echo json_encode($response);
} else {
    // no products found
    $response["success"] = 0;
    $response["message"] = "No data found";
 
    // echo no users JSON
    echo json_encode($response);
}
?>