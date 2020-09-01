<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../../header/header.php";

if (SessionUtilities::checkIsSessionSet('User') && (SessionUtilities::getSession('UserType') === 'superAdmin')) {

    $companies = Company::fetchAllCompaniesFromDB();

    $devices = fetchBasicDevicesInfoFromDB();
    $numberOfDevices = count($devices);
    $clients = fetchClientsFromDB();
    $objects = fetchObjectsFromDB();

} else {
    header("location: ../../index.php");
}

function checkIfFiltersAreSet() {
    if (SessionUtilities::checkIsSessionSet('ClientFilter')) {
        $clientFilter = SessionUtilities::getSession('ClientFilter');
        $clientFilter = explode(":", $clientFilter);
        echo "<div class='filter'><button id='ClientFilter' class='removeFilter' onclick='deleteFiltersFromDashboard(this.id)'>&times;</button>  <span class='filter-text'>" . $clientFilter[1] . "</span></div>";
    }
    if (SessionUtilities::checkIsSessionSet('ObjectFilter')) {
        $objectFilter = SessionUtilities::getSession('ObjectFilter');
        $objectFilter = explode(":", $objectFilter);
        echo "<div class='filter'><button id='ObjectFilter' class='removeFilter' onclick='deleteFiltersFromDashboard(this.id)'>&times;</button>  <span class='filter-text'>" . $objectFilter[1] . "</span></div>";
    }
    if (SessionUtilities::checkIsSessionSet('TypeFilter')) {
        $typeFilter = SessionUtilities::getSession('TypeFilter');
        echo "<div class='filter'><button id='TypeFilter' class='removeFilter' onclick='deleteFiltersFromDashboard(this.id)'>&times;</button>  <span class='filter-text'>" . $typeFilter . "</span></div>";
    }
    if (SessionUtilities::checkIsSessionSet('TimeFilter')) {
        $timeFilter = SessionUtilities::getSession('TimeFilter');
        $timeFilter = explode(":", $timeFilter);
        $timeFilter = "od " . date("d M Y", (int)($timeFilter[0] / 1000)) . " do " . date("d M Y", (int)($timeFilter[1] / 1000));
        echo "<div class='filter'><button id='TimeFilter' class='removeFilter' onclick='deleteFiltersFromDashboard(this.id)'>&times;</button>  <span class='filter-text'>" . $timeFilter . "</span></div>";
    }
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

function fetchObjectsFromDB() {
    $objects = [];
    $currentUser = SessionUtilities::getSession('User');
    $sql = "SELECT id, objectName
            FROM objects
            WHERE clientID IN (SELECT id
                              FROM clients
                              WHERE companyID IN (SELECT companyID
                                                  FROM users
                                                  WHERE username='$currentUser'))";

    $result = DataBase::selectionQuery($sql);
    if ($result->num_rows > 0)
        while ($row = $result->fetch_assoc())
            $objects []= $row;

    return $objects;
}

function printClientDropdownMenuOptions($clients) {
    foreach ($clients as $client) {
        echo "<option value='" . $client['id'] . ";" . $client['clientName'] . "'>" . $client['clientName'] . "</option>";
    }
}

function printObjectDropdownMenuOptions($objects) {
    foreach ($objects as $object) {
        echo "<option value='" . $object['id'] . ";" . $object['objectName'] . "'>" . $object['objectName'] . "</option>";
    }
}

function fetchBasicDevicesInfoFromDB() {
    $devices = [];
    $currentUser = SessionUtilities::getSession('User');
    $sql = "SELECT S.id, B.allowedBarcodes as barcodeID, S.creationTimeMillis, O.objectName, C.clientName
            FROM sviuredjaji S, objects O, clients C, barcodes B
            WHERE S.objectID=O.id AND O.clientID=C.id AND S.barcodeID=B.id AND C.companyID = (SELECT companyID
                                                                       FROM users
                                                                       WHERE username='$currentUser')";

    $sqlFilter = "";
    if (SessionUtilities::checkIsSessionSet('ClientFilter')) {
        $clientFilter = explode(":", SessionUtilities::getSession('ClientFilter'));
        $sqlFilter .= " AND C.id=" . $clientFilter[0];
    }
    if (SessionUtilities::checkIsSessionSet('ObjectFilter')) {
        $objectFilter = explode(":", SessionUtilities::getSession('ObjectFilter'));
        $sqlFilter .= " AND O.id=" . $objectFilter[0];
    }
    if (SessionUtilities::checkIsSessionSet('TypeFilter')) {
        $typeFilter = SessionUtilities::getSession('TypeFilter');
        $sqlFilter .= " AND S.type='" . $typeFilter . "'";
    }
    if (SessionUtilities::checkIsSessionSet('TimeFilter')) {
        $timeFilter = SessionUtilities::getSession('TimeFilter');
        $timeFilter = explode(":", $timeFilter);
        $timeFilter[0] = (int)$timeFilter[0];
        $timeFilter[1] = (int)$timeFilter[1] + 86400000;
        $sqlFilter .= " AND (S.creationTimeMillis BETWEEN " . $timeFilter[0] . " AND  " . $timeFilter[1] . ")";
    }
    $sql .= $sqlFilter;

    $result = DataBase::selectionQuery($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $devices []= $row;
        }
    }

    return $devices;
}

function parseTimeFilterData($timeFilter) {
    $timeArray = explode("-", $timeFilter);
    return strtotime($timeArray[0]) . "-" . strtotime($timeArray[1]);
}

function printData($devices) {
    foreach ($devices as $device) {
        printDeviceData($device);
    }
}

function printDeviceData($device) {
    echo "<tr>";
    echo "<td>" . $device['id'] . "</td>";
    echo "<td>" . $device['barcodeID'] . "</td>";
    echo "<td>" . $device['clientName'] . "</td>";
    echo "<td>" . $device['objectName'] . "</td>";
    echo "<td>" . date("d.m.Y H:i:s", (int)($device['creationTimeMillis'] / 1000)) . "</td>";

    echo "<td><a href='device_info.php?id=" . $device['id'] . "'><button name='moreButton'>More <img src=\"img/arrow-icon.png\"> </button></a></td>";
    echo "</tr>";
}

function printReportSection() {
    if (checkAreClientObjectFiltersSet()) {
        echo "
        <div id=\"reportSection\" name=\"reportSection\" style=\"z-index: 1\">
        <div class=\"button-wrapper\">
            <button id=\"reportButton\" onclick=\"sendReportData('zapis-aparati')\"><img src=\"./assets/img/download-icon-1.png\" alt=\"\"> Izvezi Radni nalog - PP Aparati</button>
            <button id=\"reportButton\" onclick=\"sendReportData('zapis-kontrolisanje')\"><img src=\"./assets/img/download-icon-1.png\" alt=\"\"> Izvezi Radni nalog - Hidranti</button>
            <button id=\"reportButton\" onclick=\"sendReportData('obr-38-2')\"><img src=\"./assets/img/download-icon-1.png\" alt=\"\"> Izvezi Ispravu o kontrolisanju Hidranatske Mreze</button>
            <button id=\"reportButton\" onclick=\"sendReportData('obr-r08-35-1')\"><img src=\"./assets/img/download-icon-1.png\" alt=\"\"> Izvezi Ispravu o kontrolisanju mreze PP aparata</button>
        </div>
    </div>
        ";
    }
}

function checkAreClientObjectFiltersSet() {
    return (SessionUtilities::checkIsSessionSet('ClientFilter') && SessionUtilities::checkIsSessionSet('ObjectFilter'));
}

function checkIfAnyFilterIsSet() {
    return (SessionUtilities::checkIsSessionSet('ClientFilter') ||
        SessionUtilities::checkIsSessionSet('ObjectFilter') ||
        SessionUtilities::checkIsSessionSet('TypeFilter') ||
        SessionUtilities::checkIsSessionSet('TimeFilter'));
}

function printNumberOfDevices($devices) {
    $sql = "SELECT COUNT(*) as total
            FROM sviuredjaji
            WHERE objectID IN (SELECT id
                               FROM objects
                               WHERE clientID IN (SELECT id
                                                  FROM clients
                                                  WHERE companyID = (SELECT companyID
                                                                     FROM users
                                                                     WHERE username='" . SessionUtilities::getSession('User') . "')))";
    $result = DataBase::selectionQuery($sql);
    if ($result->num_rows > 0) {
        $rows = $result->fetch_assoc();
        $totalNumber = $rows['total'];
    }
    echo "<div>Ukupan broj uredjaja: <strong>" . $totalNumber . "</strong>";
    if (checkIfAnyFilterIsSet())
        echo "&nbsp;&nbsp;&nbsp; Broj isfiltriranih uredjaja: <strong>" . count($devices) . "</strong>";

    echo "</div>";
}

/**
 * @param $companies Company[]
 */
function printBasicCompaniesInfo($companies) {
    foreach ($companies as $company) {
        $company->printBasicCompanyInfoInTable();
    }
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PBS</title>

    <!-- Styles -->
    <?=HTMLUtilities::ImportLinks("../../css/dashboard.css");?>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto|Montserrat|Open+Sans" rel="stylesheet">

    <!-- Scripts -->
    <script src="js/sa_scripts.js"></script>
    <script src="../../js/filters.js"></script>
</head>
<body onload="showAccountName()">

<section id="wrapper">

    <?php
    require_once "../../header/nav_menu.php";
    ?>

    <div class="container">

        <br><br><br>

        <h1 style="font-size: 35px;font-weight: normal;color: #555;">Podaci o firmama</h1>
        <div id="previewTableSection">
            <!-- Refactor -->
            <table id="previewTableSection" class="default-table">
                <tr class="previewTableHeaderRow">
                    <th>Naziv firme</th>
                    <th>Lokacija</th>
                    <th>Broj dozvoljenih barkodova</th>
                    <th>Broj iskoriscenih barkodova</th>
                    <th></th>
                </tr>
                <?php
                printBasicCompaniesInfo($companies);
                ?>
            </table>
        </div>
    </div>

</section>

</body>
</html>


