<?php

require_once __DIR__ . "/../../header/header.php";

if (SessionUtilities::checkIsSessionSet('User')) {
    if (isset($_POST['clientFilter'])) {
        SessionUtilities::createSession('ClientFilter', $_POST['clientFilter']);
    }
    if (isset($_POST['objectFilter'])) {
        SessionUtilities::createSession('ObjectFilter', $_POST['objectFilter']);
    }
    if (isset($_POST['typeFilter'])) {
        SessionUtilities::createSession('TypeFilter', $_POST['typeFilter']);
    }
    if (isset($_POST['timeFilter'])) {
        SessionUtilities::createSession('TimeFilter', $_POST['timeFilter']);
    }
    if (isset($_POST['deleteFilter'])) {
        SessionUtilities::unsetSession($_POST['deleteFilter']);
    }
} else {
    header("location: ../../index.php");
}