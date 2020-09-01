<?php
include 'db_config.php';
$con = mysqli_connect($servername, $username, $password, $dbname);
 mysqli_set_charset($con, "utf8");
 $response = array();
 
$companyName = $_POST['companyName'];
$fullName = "fullname";
$mUsername = "username";
$licenceNumber = "licenceNumber";
$userType = "userType";
$allowAccess = "allowAccess";


$sql_result_id = "SELECT id FROM companies WHERE companyName='$companyName' LIMIT 1";
$result_id = mysqli_query($con, $sql_result_id);
$obj = mysqli_fetch_array($result_id);
$companyID = $obj["id"];

$sql = "SELECT *FROM users WHERE companyID = '$companyID'";
$result = mysqli_query($con, $sql) or die(mysqli_error($con));

 
// check for empty result
if (mysqli_num_rows($result) > 0) {
    // looping through all results
	$dataArray = "dataArray";
    $response[$dataArray] = array();
	
    while ($row = mysqli_fetch_array($result)) {
			$data = array();
			$data[$fullName] = $row[$fullName];
			$data[$mUsername] = $row[$mUsername];
			$data[$licenceNumber] = $row[$licenceNumber];
			$data[$userType] = $row[$userType];
			$data[$allowAccess] = $row[$allowAccess];
		
			array_push($response[$dataArray], $data);        
    }
  
    $response["success"] = 1;
    echo json_encode($response);
} else {
    $response["success"] = 0;
 
    // echo no users JSON
    echo json_encode($response);
}
?>