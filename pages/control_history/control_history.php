<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../../header/header.php";

if (SessionUtilities::checkIsSessionSet('User') && isset($_GET['id'])) {
    $deviceId = $_GET['id'];
    $device = null;
    $basicControlHistories = null;
    $extendedControlHistories = null;
    $operatorsData = null;

    $sql = "SELECT type FROM sviuredjaji WHERE id=$deviceId";
    $result = DataBase::selectionQuery($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $device = DevicesContext::getInstance()->pickDevice($row['type']);
        $device->setId($deviceId);
    }

    if ($device !== null) {
        $basicControlHistories = $device->fetchAllBasicControlHistory();
    }

    for ($i = 0; $i < count($basicControlHistories); $i++) {
        $operatorsData[$i] = fetchOperatorDataByID($basicControlHistories[$i]['operatorID']);
    }

} else {
    header("location: ../../index.php");
}

function fetchOperatorDataByID($operatorID) {
    $sql = "SELECT fullname, username, licenceNumber, companyName
            FROM users U, companies C
            WHERE U.companyID=C.id AND U.id=$operatorID";
    $result = DataBase::selectionQuery($sql);
    if ($result->num_rows > 0)
        return $result->fetch_assoc();
    return null;
}

function printControlHistories($basicControlHistories, $device, $operatorsData) {
    $extendedControlHistories = $device->fetchAllExtendedControlHistory();
    $barcodeID = Device::getBarcodeIDById($device->getId());
    echo "<h1 class='barcode-heading'>Barkod: &nbsp;<span id='barcode'>" . $barcodeID . "</span></h1>";
    echo "<div class='devices'>";
    for ($i = 0; $i < count($basicControlHistories); $i++) {
        echo "<div class='device'>
                <div class='label'>" . ($i + 1) . "</div>";
        echo "<table class='history-table'>";
        echo "<tr class='headings'>
                <th colspan='7'>Podaci kontrolisanja</th>
              </tr>";
        echo "<tr class='headings'>
                <th>ID operatera</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Vreme kontrolisanja</th>
                <th>Beleska</th>
                <th>Lokacija uredjaja</th>
                <th>Trenutno stanje</th>
              </tr>";

        echo "<tr>";
        echo "<td>" . $basicControlHistories[$i]['operatorID'] . "</td>";
        echo "<td>" . $basicControlHistories[$i]['latitude'] . "</td>";
        echo "<td>" . $basicControlHistories[$i]['longitude'] . "</td>";
        echo "<td>" . date("D d M Y H:i:s", (int)($basicControlHistories[$i]['timeControlMillis'] / 1000)) . "</td>";
        echo "<td>" . $basicControlHistories[$i]['note'] . "</td>";
        echo "<td>" . $basicControlHistories[$i]['locationPP'] . "</td>";
        echo "<td>" . (($basicControlHistories[$i]['curState'] === "in order")?"Ispravan":"Neispravan") . "</td>";
        echo "</tr>";

        echo $device->printExtendedControlHistory($extendedControlHistories[$i]);

        echo "<tr>
                 <th colspan='7'>Podaci o kontroloru</th>
              </tr>";
        echo "<tr class='headings'>
                <th>Ime i prezime</th>
                <th>Username</th>
                <th>Broj licence</th>
                <th>Firma</th>
              </tr>";

        echo "<tr>";
        echo "<td>" . $operatorsData[$i]['fullname'] . "</td>";
        echo "<td>" . $operatorsData[$i]['username'] . "</td>";
        echo "<td>" . $operatorsData[$i]['licenceNumber'] . "</td>";
        echo "<td>" . $operatorsData[$i]['companyName'] . "</td>";
        echo "</tr>";

        echo "</table></div>";

        if ($basicControlHistories[$i]['imgPath'] === "-" || $basicControlHistories[$i]['imgPath'] === "")
            echo "No image<br>";
        else
            echo "<img src='" . $basicControlHistories[$i]['imgPath'] . "'/>";
    }
    echo "</div>";
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Istorijat</title>

    <!-- Styles -->
    <?=HTMLUtilities::ImportLinks('../../css/shared/history.css')?>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto|Montserrat|Open+Sans" rel="stylesheet">

    <!-- Scripts -->
    <script src="../../js/scripts.js"></script>
</head>
<body onload="showAccountName()">

<section id="wrapper">

    <?php
    require_once "../../header/nav_menu.php";
    ?>

    <br/><br/>
    <div class="container">
        <?php
        printControlHistories($basicControlHistories, $device, $operatorsData);
        ?>
    </div>

</section>

</body>
</html>
