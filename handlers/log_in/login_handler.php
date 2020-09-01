<?php

require_once "../../header/header.php";

if(isset($_POST['usersData'])) {
    $usersData = $_POST['usersData'];
    $data = explode(";", $usersData);
    $userLogin = new Login();
    $userLogin->setParameters($data[0], $data[1]);
    if ($userLogin->checkParameters()) {
        $userLogin->loginUser();
        if (SessionUtilities::getSession('UserType') === 'superAdmin')
            echo "success_super";
        else
            echo "success";
    } else
        echo "";
} else {
    header("location: ../index.php");
    die();
}