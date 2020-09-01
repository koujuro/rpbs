<?php
include 'db_config.php';
$con = mysqli_connect($servername, $username, $password, $dbname);
mysqli_set_charset($con, "utf8");

$companyName = $_POST['companyName'];
$barcodesPhp = $_POST['barcodesPhp'];
$used = 0;

    $sql_result_id = "SELECT id FROM companies WHERE companyName='$companyName' LIMIT 1";
	$result_id = mysqli_query($con, $sql_result_id);
	$obj = mysqli_fetch_array($result_id);
	$companyID = $obj["id"];
	
$response = array();
$barcodes = array();
$barcodes = explode(".", $barcodesPhp);

$count = count($barcodes);

$x=true;
$i=1;



foreach ($barcodes as $value) {
	$sql = "INSERT INTO barcodes(allowedBarcodes, companyId, used) VALUES('$value', '$companyID', '$used')";  
    $result = mysqli_query($con,$sql);
	 
    if ($result) {
		if($x=true){
		$i = $i + 1;
			if($i==$count){
				$response["success"] = 1;
				echo json_encode($response);				
			}
		}
    } else {
		$x=false;
		foreach ($barcodes as $valuee) {
			$sql = "DELETE FROM barcodes WHERE allowedBarcodes='$valuee'";  
			$result = mysqli_query($con, $sql);
		}
		 $response["success"] = 0;
		 $response["Error1"] = 0;
		 
		 $response["companyID"] = $companyID;
		 $response["barcodes"] = $barcodes;
        echo json_encode($response);
		break;
    }
}
	
 
   

?>