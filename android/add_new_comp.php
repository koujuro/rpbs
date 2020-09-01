<?php
 
include 'db_config.php';
$con = mysqli_connect($servername, $username, $password, $dbname);
	mysqli_set_charset($con, "utf8");
	
	$response = array();
 
	$companyName = (string) $_POST['companyID'];
	$uniqueNumber = (int) $_POST['randomNum'];
	$request = 0;
	$AllowedBarcodes = (int) $_POST['numAllowedBarcodes'];
 
	$sql = "INSERT INTO companies(companyName, uniqueNumber, request, numAllowedBarcodes, 
		street, number, city, PAC, phoneNumber, webSite, eMail, officePhoneNumber, 
		servicePhoneNumber, mobilePhoneNumber, numberATS, controlLicenceNumber, imgName) 
		VALUES('$companyName', '$uniqueNumber', '$request', '$AllowedBarcodes', 
	 ' ', 0, ' ',0, ' ', ' ', ' ', ' ', ' ', ' ', 0, 0, ' ')";
    $result = mysqli_query($con,$sql);
  
    if ($result) {		
        $response["success"] = 1;      
        echo json_encode($response);
    } else {      
        $response["success"] = 0;     
        echo json_encode($response);
    }

?>