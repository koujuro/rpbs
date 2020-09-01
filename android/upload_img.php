<?php

include 'db_config.php';
$con = mysqli_connect($servername, $username, $password, $dbname);
mysqli_set_charset($con, "utf8");

$bytes = $_POST['bytes'];
$companyName = $_POST['companyName'];
$barcode = $_POST['barcode'];
$millis = $_POST['millis'];

$imageName = $barcode . '_' . $millis;
$imagePath = "images/$imageName.jpg";
$url = "android/$imagePath";

$sql_id_0 = "SELECT id FROM barcodes WHERE allowedBarcodes='$barcode' LIMIT 1";
$result_id_0 = mysqli_query($con, $sql_id_0);
$obj_0 = mysqli_fetch_array($result_id_0);
$barcodeID = (int) $obj_0["id"];

$sql_id_1 = "SELECT id FROM sviuredjaji WHERE barcodeID='$barcodeID' LIMIT 1";
$result_id_1 = mysqli_query($con, $sql_id_1);
$obj_1 = mysqli_fetch_array($result_id_1);
$ppaID = (int) $obj_1["id"];


$sql_update = "UPDATE sviuredjajicontrolhistory SET imgPath='$url' WHERE ppaID='$ppaID' AND timeControlMillis='$millis'";
$result_update = mysqli_query($con, $sql_update);

$response["barcode"] = $barcode;
$response["millis"] = $millis;
$response["ppaID"] = $ppaID;
if ($result_update) {
    file_put_contents($imagePath, base64_decode($bytes));

    $response["success"] = 1;
    echo json_encode($response);
} else {
    $response["msg"] = "error10";
    $response["success"] = 0;
    echo json_encode($response);
}
?>