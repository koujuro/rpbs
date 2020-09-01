<?php

require_once __DIR__ . "/../../header/header.php";

if (SessionUtilities::checkIsSessionSet('User')) {
    $myObj = new \stdClass();
    $myObj->error = "";
    //$myObj->data = "mrk";
    if (isset($_POST['clientId'])) {
        $userId = $_POST['clientId'];
        $sql = "SELECT id, clientName, streetAndNumber, city, PAC FROM clients WHERE id=$userId";
        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $myObj->data = $row;
            $myObj->error = "success";
        }
    } else if (isset($_POST['objectId'])) {
        $objectId = $_POST['objectId'];
        $sql = "SELECT id, streetAndNumber, city, PAC, objectName, floorsAboveGround, floorsUnderground, highestObjectAltitude FROM objects WHERE id=$objectId";
        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $myObj->data = $row;
            $myObj->error = "success";
        }
    } else {
        $myObj->error = "idError";
    }

    $json = json_encode($myObj);
    echo $json;
} else {
    header("location: ../../index.php");
}
