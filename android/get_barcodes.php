<?php
include 'db_config.php';
$con = mysqli_connect($servername, $username, $password, $dbname);
 mysqli_set_charset($con, "utf8");
 $response = array();
//Podaci o uredjaju
$companyName = $_POST['companyName'];
$allowedBarcodes = "allowedBarcodes";

    $sql_result_id = "SELECT id FROM companies WHERE companyName='$companyName' LIMIT 1";
	$result_id = mysqli_query($con, $sql_result_id);
	$obj = mysqli_fetch_array($result_id);
	$companyID = $obj["id"];
	
$sql = "SELECT allowedBarcodes FROM barcodes WHERE companyId = '$companyID'";
$result = mysqli_query($con, $sql) or die(mysqli_error($con));

 
// check for empty result
if (mysqli_num_rows($result) > 0) {
    // looping through all results
    $response[$allowedBarcodes] = array();
    while ($row = mysqli_fetch_array($result)) {
        // push single product into final response array
			array_push($response[$allowedBarcodes], $row[$allowedBarcodes]);
        
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