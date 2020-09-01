<?php
include 'db_config.php';
$con = mysqli_connect($servername, $username, $password, $dbname);
mysqli_set_charset($con, "utf8");

$companyName = $_POST['companyName'];
$response = array();

    $sql_result_id = "SELECT id FROM companies WHERE companyName='$companyName' LIMIT 1";
	$result_id = mysqli_query($con, $sql_result_id);
	$obj = mysqli_fetch_array($result_id);
	$companyID = $obj["id"];
	
	$sql_count = "SELECT id FROM barcodes WHERE companyId = '$companyID'";  
	$result_count = mysqli_query($con, $sql_count);

if ($result_count){
  
   $rowcount=mysqli_num_rows($result_count);
   $sql_cnt = "UPDATE companies SET numAllowedBarcodes = '$rowcount' WHERE id = '$companyID'";  
   $result_cnt = mysqli_query($con, $sql_cnt);
						
	if ($result_cnt) {
		
		$sql = "UPDATE companies SET request = '0' WHERE id = '$companyID'";  
		$result = mysqli_query($con, $sql_cnt);
   
		if ($result) {
			$response["success"] = 1;  
			echo json_encode($response);						
		}else{
			$response["success"] = 0;  
			echo json_encode($response);				
		}					
	}else{
		$response["success"] = 0;  
		echo json_encode($response);				
	}
  
}
 
?>