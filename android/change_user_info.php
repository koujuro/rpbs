<?php
 
include 'db_config.php';
$con = mysqli_connect($servername, $username, $password, $dbname);
	mysqli_set_charset($con, "utf8");
	
	$response = array();
 
	$type = $_POST['type'];
	$mUsername = $_POST['mUsername'];
	$anything = $_POST['anything'];
  
    if($type=="pass") $sql = "UPDATE users SET password = '$anything' WHERE username='$mUsername'";
	elseif ($type=="name") $sql = "UPDATE users SET fullName = '$anything' WHERE username='$mUsername'";
	elseif ($type=="block") $sql = "UPDATE users SET allowAccess = '$anything' WHERE username='$mUsername'";
	elseif ($type=="licence") $sql = "UPDATE users SET licenceNumber = '$anything' WHERE username='$mUsername'";
	elseif ($type=="delete") $sql = "DELETE FROM users WHERE username='$mUsername'";
		
    $result = mysqli_query($con,$sql);
  
    if ($result) {		
        $response["success"] = 1;      
        echo json_encode($response);
    } else {      
        $response["success"] = 0;     
        echo json_encode($response);
    }

?>