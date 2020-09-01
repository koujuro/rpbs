<?php
include 'db_config.php';
$con = mysqli_connect($servername, $username, $password, $dbname);
 mysqli_set_charset($con, "utf8");
 $response = array();
 
$companyName = "companyName";
$AllowedBarcodes = "numAllowedBarcodes";
$Requests = "request";
$UniqueNum = "uniqueNumber";

$sql = "SELECT *FROM companies";
$result = mysqli_query($con, $sql) or die(mysqli_error($con));

// check for empty result
if (mysqli_num_rows($result) > 0) {

	$dataArray = "dataArray";
    $response[$dataArray] = array();
    while ($row = mysqli_fetch_array($result)) {    
		$data = array();
        $data[$companyName] = $row[$companyName];
		$data[$AllowedBarcodes] = $row[$AllowedBarcodes];
		$data[$Requests] = $row[$Requests];
		$data[$UniqueNum] = $row[$UniqueNum];
		
		array_push($response[$dataArray], $data);
    }
	
    $response["success"] = 1;
    echo json_encode($response);
} else {
    // no data found
    $response["success"] = 0;
    $response["message"] = "No data found";
 
    // echo no users JSON
    echo json_encode($response);
}
?>