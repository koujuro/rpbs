<?php
 include 'db_config.php';
$con = mysqli_connect($servername, $username, $password, $dbname);
mysqli_set_charset($con, "utf8");

$mUsername = $_POST['username'];
$mPassword = $_POST['password'];

$sql = "SELECT *FROM users WHERE username = '$mUsername' AND password = '$mPassword' AND userType!='admin' AND allowAccess = '1' LIMIT 1";
$result = mysqli_query($con, $sql) or die(mysqli_error($con));
$companyName="";

$response = array();

if (mysqli_num_rows($result) > 0) {
   
	$row = mysqli_fetch_array($result);

	
		$companyID = $row["companyID"];	
		$sql_1 = "SELECT companyName FROM companies WHERE id='$companyID' LIMIT 1";
		$result_1 = mysqli_query($con, $sql_1) or die(mysqli_error($con));
		$obj_1 = mysqli_fetch_array($result_1);
		$companyName = $obj_1["companyName"];

		$response["username"] = $row["username"];
		$response["companyName"] = $companyName;
		$response["fullname"] = $row["fullname"];
		$response["userType"] = $row["userType"];
			
	    $response["success"] = 1;
		echo json_encode($response);		
	
  
   
    
} else {
 
    $response["success"] = 0;
    echo json_encode($response);
}
?>