<?php
include 'db_config.php';
$con = mysqli_connect($servername, $username, $password, $dbname);
mysqli_set_charset($con, "utf8");
$response = array();

$userType = $_POST["userType"];
$mUsername = $_POST['username'];


if($userType=="controller"){
	$companyName = $_POST['companyName'];
	$fullName = $_POST['fullName'];

	$sql_result_id = "SELECT id FROM companies WHERE companyName='$companyName' LIMIT 1";
	$result_id = mysqli_query($con, $sql_result_id);
	$obj = mysqli_fetch_array($result_id);
	$companyID = $obj["id"];

	$sql = "SELECT *FROM users WHERE username = '$mUsername' AND fullName = '$fullName' AND userType = 'controller' AND companyID = '$companyID' AND allowAccess = '1' LIMIT 1";
	$result = mysqli_query($con, $sql) or die(mysqli_error($con));
	
	if (mysqli_num_rows($result) > 0) {
		$response["success"] = 1;
		echo json_encode($response);
	} else {
		$response["success"] = 0;
		echo json_encode($response);
	}	
}else if($userType=="superAdmin"){
	$sql = "SELECT *FROM users WHERE username = '$mUsername' AND userType = 'superAdmin' AND allowAccess = '1' LIMIT 1";
	$result = mysqli_query($con, $sql) or die(mysqli_error($con));
	
	if (mysqli_num_rows($result) > 0) {
		$response["success"] = 1;
		echo json_encode($response);
	} else {
		$response["success"] = 0;
		echo json_encode($response);
	}
}else {
	$response["success"] = 0;
    echo json_encode($response);
}


	


?>