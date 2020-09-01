<?php

require_once __DIR__ . "/../../header/header.php";

if (SessionUtilities::checkIsSessionSet('User')) {
    if (isset($_POST['measuringDevices'])) {
        $measuringDevices = $_POST['measuringDevices'];
        if ($measuringDevices === "") {
            SessionUtilities::unsetSession('MDFilter');
        }
        SessionUtilities::createSession('MDFilter', $measuringDevices);
    }
} else {
    header("location: ../../index.php");
}

?>