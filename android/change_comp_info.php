<?php
 
include 'db_config.php';
$con = mysqli_connect($servername, $username, $password, $dbname);
	mysqli_set_charset($con, "utf8");
	
	$response = array();
	$mCompanyName = $_POST['mCompanyName'];
	$allowAccess = $_POST['allowAccess'];
  
    $sql_result_id = "SELECT id FROM companies WHERE companyName='$mCompanyName' LIMIT 1";
	$result_id = mysqli_query($con, $sql_result_id);
	$obj = mysqli_fetch_array($result_id);
	$companyID = $obj["id"];
	
 
    $sql_1 = "UPDATE users SET allowAccess = '$allowAccess' WHERE companyID='$companyID'";
    $result_1 = mysqli_query($con, $sql_1);

	
    if ($result_1) {	
	
		$response["success"] = 1;      
		echo json_encode($response);
	
    } else {      
        $response["success"] = 0;  
		$response["msg"] = 1; 		
        echo json_encode($response);
    }

?>