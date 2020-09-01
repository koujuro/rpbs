<?php
 
include 'db_config.php';
$con = mysqli_connect($servername, $username, $password, $dbname);
$response = array();
mysqli_set_charset($con, "utf8");

$mCompanyName = $_POST['companyName'];
$fullName = $_POST['fullName'];
$mUsername = $_POST['username'];
$mPassword = $_POST['password'];
$licenca = $_POST['licence'];
$userType = $_POST['userType'];
$millisCreated = $_POST['millisCreated'];


$sql_result_id = "SELECT id FROM companies WHERE companyName='$mCompanyName' LIMIT 1";
$result_id = mysqli_query($con, $sql_result_id);
$obj = mysqli_fetch_array($result_id);
$companyID = $obj["id"];

$sql_select = "SELECT *FROM users WHERE username = '$username'";
$result_all = mysqli_query($con, $sql_select);
if (mysqli_num_rows($result_all) == 0) {
	
    $sql = "INSERT INTO users(companyID, fullName, username, password, licenceNumber, userType, millisCreated, allowAccess) 
            VALUES('$companyID', '$fullName', '$mUsername', '$mPassword', '$licenca', '$userType', '$millisCreated', '1')";  
	$result = mysqli_query($con,$sql);
	 
	if ($result) {
		$response["success"] = 1;
	}else{
		$response["success"] = 0;
		$response["msg"] = "Error";
	}
		
    echo json_encode($response);
} else {
    $response["success"] = 0;
    $response["msg"] = "Username vec postoji";
    echo json_encode($response);
}
?>