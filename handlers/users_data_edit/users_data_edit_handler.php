<?php

require_once __DIR__ . "/../../header/header.php";

if (SessionUtilities::checkIsSessionSet('User')) {
    $myObj = new \stdClass();
    $myObj->error = "";
    if (isset($_POST['id'])) {
        $userId = $_POST['id'];
        $sql = "SELECT id, username, password, fullName, licenceNumber, userType FROM users WHERE id=$userId";
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
