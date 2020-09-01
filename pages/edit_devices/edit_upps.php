<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../../header/header.php";

if (SessionUtilities::checkIsSessionSet('User')) {
    if (!SessionUtilities::checkIsSessionSet('EditUPPsFilter'))
        SessionUtilities::createSession('EditUPPsFilter', '');
    if (isset($_POST['chosenBarcode']))
        SessionUtilities::createSession('EditUPPsFilter', 'barcodeID');

    if (isset($_POST['editDeviceSubmit'])) {
        $basicInfo = [$_POST['editDeviceId'],
            $_POST['barcodeID'],
            $_POST['objectDropdown'],
            strtotime($_POST['creationTimeMillis']) * 1000,
            strtotime($_POST['lastControlMillis']) * 1000];

        $device = DevicesContext::getInstance()->pickDevice(Device::getDeviceTypeById($basicInfo[0]));
        $device->setId($basicInfo[0]);
        $device->updateBasicInfoInDB($basicInfo);

        $device->updateExtendedInfoInDB($_POST, $basicInfo[0]);
    }

    $devices = fetchDevicesFromDB();

} else {
    header("location: ../../index.php");
}

function fetchDevicesFromDB() {
    $devices = [];
    $currentUser = SessionUtilities::getSession('User');
    $additionalFields = '';

    $sql = "SELECT S.id, B.allowedBarcodes as barcodeID" . $additionalFields . "
            FROM sviuredjaji S, barcodes B, upp U
            WHERE S.barcodeID=B.id AND U.PPAsId=S.id AND S.type='UPP' 
                    AND objectID IN (SELECT id
                                      FROM objects
                                      WHERE clientID IN (SELECT id
                                                        FROM clients
                                                        WHERE companyID IN ((SELECT companyID
                                                                            FROM users
                                                                            WHERE username='$currentUser'))))";

    $result = DataBase::selectionQuery($sql);
    if ($result->num_rows > 0)
        while ($row = $result->fetch_assoc())
            $devices []= $row;

    return $devices;
}

function printDevices($devices) {
    foreach ($devices as $device) {
        echo "<div class=\"filter-option\" id=\"" . $device['id'] . "\" onclick=\"sendChosenUPP(this.id)\" data-type-id=\"".$device['id']."\" data-search-term=\"" . $device['barcodeID'] . "\"> <span class=\"option-id\">".$device['id']."</span> ".$device['barcodeID']."</div>";
    }
}

function printSentDeviceData() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $sql = "SELECT type FROM sviuredjaji WHERE id=$id";

        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $device = DevicesContext::getInstance()->pickDevice($row['type']);
            $device->setId($id);
            $device->getBasicInfo();
            $device->getExtendedInfo();
            printClientObjectDropdowns($id);
            echo generateHTMLForDevice($device);
        }
    }
}

function printClientObjectDropdowns($deviceId) {
    $clientID = null;
    $objectID = null;
    if (isset($_GET['clientID'])) {
        $clientID = $_GET['clientID'];
    } else {
        $sql = "SELECT id, clientID
                FROM objects 
                WHERE id = (SELECT objectID
                            FROM sviuredjaji
                            WHERE id=$deviceId )";

        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        }
        $clientID = $row['clientID'];
        $objectID = $row['id'];
    }

    printClientsDropdown($clientID);
    printObjectsDropdown($clientID, $objectID);
}

function printClientsDropdown($clientID) {
    $clients = fetchClientsFromDB();
    echo "<div class='input-group'>";
    echo "<label for=''>Klijent:</label> <select id='clientDropdown' name='clientDropdown' onchange='sendChosenClient(this.value, " . $_GET['id'] . ")'>";
    foreach ($clients as $client) {
        $selected = ($client['id'] === $clientID)?"selected":"";
        echo "<option value='" . $client['id'] . "' " . $selected . ">" . $client['clientName'] . "</option>";
    }
    echo "</select></div>";
}

function printObjectsDropdown($clientID, $objectID) {
    $objects = fetchObjectsFromDB($clientID);
    echo "<div class='input-group'>";
    echo "<label for=''>Objekat:</label> <select id='objectDropdown' name='objectDropdown'>";
    foreach ($objects as $object) {
        $selected = (isset($objectID) && ($object['id'] === $objectID))?"selected":"";
        echo "<option value='" . $object['id'] . "' " . $selected . ">" . $object['objectName'] . "</option>";
    }
    echo "</select></div>";
}

function fetchClientsFromDB() {
    $clients = [];
    $currentUser = SessionUtilities::getSession('User');
    $sql = "SELECT id, clientName
            FROM clients
            WHERE companyID IN (SELECT companyID
                                FROM users
                                WHERE username='$currentUser')";

    $result = DataBase::selectionQuery($sql);
    if ($result->num_rows > 0)
        while ($row = $result->fetch_assoc())
            $clients []= $row;

    return $clients;
}

function fetchObjectsFromDB($clientID) {
    $objects = [];
    $sql = "SELECT id, objectName
            FROM objects
            WHERE clientID=$clientID";

    $result = DataBase::selectionQuery($sql);
    if ($result->num_rows > 0)
        while ($row = $result->fetch_assoc())
            $objects []= $row;

    return $objects;
}

function generateHTMLForDevice($device) {
    $htmlContent = $device->toHTMLBasicInfo() . $device->toHTMLExtendedInfo() . "<div class='input-group buttons'>
                            <input type='submit' name='editDeviceSubmit' id='unesi' value='Sacuvaj'>
                            <a id='istorijat' href='../control_history/control_history.php?id=" . $device->getId() . "'>Istorijat</a>
                        </div>";

    return $htmlContent;
}

function checkIfGetIsSet() {
    echo isset($_GET['id'])?$_GET['id']:"init";
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PBS - Podaci o UPP</title>

    <!-- Styles -->
    <link rel="stylesheet" href="../../css/common.css">
    <link rel="stylesheet" href="edit-devices.css">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto|Montserrat|Open+Sans" rel="stylesheet">

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
    <script src="../../js/jq_functions.js"></script>
    <script src="../../js/scripts.js"></script>
    <script src="edit_devices.js"></script>
</head>
<body onload='showAccountName();filterOptionsClick("<?php checkIfGetIsSet() ?>")'>

<section id="wrapper">

    <?php
    require_once "../../header/nav_menu.php";
    ?>

    <div class="container">

        <div class="filter-grid">
            <div class="types-page">
                <h3 class="filter-heading">Uredjaji</h3>
                <div class="actions">
                    <form action="edit_upps.php" method="post">
                        <input name="chosenBarcode" class="buttons default-button" type="submit" value="Barkod">
                    </form>
                </div>
                <div class="filter-body">
                    <div class="filter-search">
                        <input type="text" class="search" placeholder="Pretraga">
                        <button><img src="../../assets/img/search-icon.png" alt=""></button>
                    </div>

                    <div class="devices-wrapper">
                        <?=printDevices($devices)?>
                    </div>

                </div>

            </div>


            <div id="devicePreviewSection">
                <form action='edit_devices.php' method='post'>
                    <div class="deviceInputs">

                        <?php
                        printSentDeviceData();
                        ?>

                    </div>
                </form>
            </div>

        </div>

    </div>

</section>


<script>
    function filterOptionsClick(deviceId) {
        if (deviceId !== "init") {
            let filterOptions = document.getElementById(deviceId).parentElement.querySelectorAll('.filter-option');

            // Menjanje klasa

            filterOptions.forEach(e => {
                if(e.id !== deviceId)
                    e.classList.remove('active');
            });

            document.getElementById(deviceId).classList.add('active');

        }
    }
</script>
</body>
</html>


