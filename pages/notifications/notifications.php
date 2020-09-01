<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../../header/header.php";


if (SessionUtilities::checkIsSessionSet('User')) {
    $currentUser = SessionUtilities::getSession('User');
    $devices = [];

    if (!isset($_GET['typeOfControl']) || ($_GET['typeOfControl'] === 'normal')) {
        $sql = "SELECT S.id, B.allowedBarcodes as barcodeID, C.clientName, S.lastControlMillis, S.creationTimeMillis
            FROM sviuredjaji S, clients C, objects O, barcodes B
            WHERE S.objectID=O.id AND O.clientID=C.id AND S.barcodeID=B.id AND C.companyID = (SELECT companyID
                                                                        FROM users
                                                                        WHERE username='$currentUser')
            ";

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

        $sql .= $sqlFilter;
        $sql .= " ORDER BY S.lastControlMillis ASC";

        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $time = ((int)$row['lastControlMillis']) / 1000;
                $row['lastControlMillis'] = $time;
                $devices[]= $row;
            }
        }

        if (SessionUtilities::checkIsSessionSet('TimeFilter')) {
            applyTimeFilterOnDevices($devices);
        }
    }
    else if ($_GET['typeOfControl'] === 'hvp') {
        $sql = "SELECT S.id, B.allowedBarcodes as barcodeID, C.clientName, S.creationTimeMillis
                FROM sviuredjaji S, clients C, objects O, barcodes B
                WHERE S.objectID=O.id AND O.clientID=C.id AND S.barcodeID=B.id AND type NOT IN ('Hydrants', 'UPP') AND C.companyID = (SELECT companyID
                                                                            FROM users
                                                                            WHERE username='$currentUser')
            ";

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

        $sql .= $sqlFilter;

        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $sqlForHVP = "SELECT P.lastHVP, S.creationTimeMillis
                              FROM ppaparaticontrol P, sviuredjaji S, sviuredjajicontrolhistory C
                              WHERE P.sviUredjajiControlId=C.id AND C.ppaID=S.id AND ppaID=" . (int)$row['id'];

                $resultForHVP = DataBase::selectionQuery($sqlForHVP);
                if ($resultForHVP->num_rows > 0) {
                    $HVPs = [];
                    while ($rowForHVP = $resultForHVP->fetch_assoc()) {
                        if ($rowForHVP['lastHVP'] !== '-')
                            $HVPs []= strtotime($rowForHVP['lastHVP']);
                    }
                    if (!empty($HVPs)) {
                        $row['lastHVP'] = max($HVPs);
                    } else
                        $row['lastHVP'] = (int)($row['creationTimeMillis'] / 1000);
                }
                $time = (int)($row['creationTimeMillis'] / 1000);
                $row['creationTimeMillis'] = $time;
                $devices []= $row;
            }
        }

        for ($i = 0; $i < count($devices) - 1; $i++) {
            for ($j = $i + 1; $j < count($devices); $j++) {
                if ($devices[$i]['lastHVP'] > $devices[$j]['lastHVP']) {
                    $pom = $devices[$i];
                    $devices[$i] = $devices[$j];
                    $devices[$j] = $pom;
                }
            }
        }

        if (SessionUtilities::checkIsSessionSet('TimeFilter')) {
            applyTimeFilterOnHVP($devices);
        }
    }

} else {
    header("location: ../../index.php");
}

function applyTimeFilterOnDevices(&$devices) {
    $timeFilter = SessionUtilities::getSession('TimeFilter');
    $timeFilter = explode(":", $timeFilter);
    $timeFilter[0] = (int)$timeFilter[0];
    $timeFilter[1] = (int)$timeFilter[1] + 86400000;
    $newArray = [];

    foreach ($devices as $device) {
        $nextControl = strtotime("+6 month", (int)$device['lastControlMillis']) * 1000;
        if (($timeFilter[0] < $nextControl) && ($nextControl < $timeFilter[1])) {
            //if (($timeFilter[0] < (int)$device['creationTimeMillis']) && ((int)$device['creationTimeMillis'] < $timeFilter[1])) {
            $newArray []= $device;
        }
    }
    $devices = $newArray;
}

function applyTimeFilterOnHVP(&$devices) {
    $timeFilter = SessionUtilities::getSession('TimeFilter');
    $timeFilter = explode(":", $timeFilter);
    $timeFilter[0] = (int)$timeFilter[0];
    $timeFilter[1] = (int)$timeFilter[1] + 86400000;
    $newArray = [];

    foreach ($devices as $device) {
        if (strtotime("-15 year") > $device['creationTimeMillis'])
            $nextControl = strtotime("+2 year", (int)$device['lastHVP']) * 1000;
        else
            $nextControl = strtotime("+5 year", (int)$device['lastHVP']) * 1000;
        if (($timeFilter[0] < $nextControl) && ($nextControl < $timeFilter[1])) {
            //if (($timeFilter[0] < (int)$device['creationTimeMillis'] * 1000) && ((int)$device['creationTimeMillis'] * 1000 < $timeFilter[1])) {
            $newArray []= $device;
        }
    }
    $devices = $newArray;
}

function calculateTimeLeftForNextRegularControl($lastControlMillis) {
    $nextControl = date("Y-m-d", strtotime("+6 month", $lastControlMillis));
    $currentDate = date("Y-m-d");
    $timeLeft = date_diff(date_create($nextControl), date_create($currentDate));

    return ((strtotime($currentDate) > strtotime($nextControl)) ? "-" : "") . $timeLeft->y . "/" . $timeLeft->m . "/" . $timeLeft->d;
}

function printData($devices) {
    foreach ($devices as $device) {
        echo "<tr>";
        echo "<td>" . $device['id'] . "</td>";
        echo "<td>" . $device['barcodeID'] . "</td>";
        echo "<td>" . $device['clientName'] . "</td>";
        echo "<td>" . calculateTimeLeftForNextRegularControl($device['lastControlMillis']) . "</td>";
        echo "<td><a href='../device_info/device_info.php?id=" . $device['id'] . "'><button name='moreButton'>More</button></a></td>";
        echo "</tr>";
    }
}

function printDataForHVP($devices) {
    foreach ($devices as $device) {
        echo "<tr>";
        echo "<td>" . $device['id'] . "</td>";
        echo "<td>" . $device['barcodeID'] . "</td>";
        echo "<td>" . $device['clientName'] . "</td>";
//        echo "<td>" . date("d.m.Y", $device['lastHVP']) . "</td>";
//        echo "<td>" . $device['creationTimeMillis'] . "</td>";
        echo "<td>" . calculateTimeLeftForNextHVPControl($device['lastHVP'], $device['creationTimeMillis']) . "</td>";
        echo "<td><a href='../device_info/device_info.php?id=" . $device['id'] . "'><button name='moreButton'>More</button></a></td>";
        echo "</tr>";
    }
}

function calculateTimeLeftForNextHVPControl($lastHVP, $creationTimeMillis) {
    if (strtotime("-15 year") > $creationTimeMillis)
        $nextControl = date("Y-m-d", strtotime("+2 year", $lastHVP));
    else
        $nextControl = date("Y-m-d", strtotime("+5 year", $lastHVP));
    $currentDate = date("Y-m-d");
    $timeLeft = date_diff(date_create($nextControl), date_create($currentDate));

    $negative = (strtotime($nextControl) < strtotime($currentDate)) ? "-" : "";
    return $negative . $timeLeft->y . "/" . $timeLeft->m . "/" . $timeLeft->d;
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

?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Planogram</title>

    <!-- Styles -->
    <?=HTMLUtilities::ImportLinks("notifications.css");?>


    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto|Montserrat|Open+Sans" rel="stylesheet">

    <!-- Scripts -->
    <script src="../../js/scripts.js"></script>
    <script src="../../js/filters.js"></script>
    <script src="../../handlers/add_filter/add_filter_ajax.js"></script>
</head>
<body onload="showAccountName()">

<section id="wrapper">

    <?php
    require_once "../../header/nav_menu.php";
    ?>


    <div class="container">

        <div class="applied-filters">
            <div class="filters-section">
                <a href="../add_filter/add_filter.php?fromPage=notifications"><button id="addFilterButton" name="addFilterButton">+ Dodaj filter</button></a>
                <?php
                checkIfFiltersAreSet();
                ?>

            </div>
            <div class="save">

            </div>
        </div>

        <div id="followingDaysSection" style="align-content: center; margin-top: 10px; margin-bottom: 10px">
            Prikazi kontrole u narednih <input type="number" id="followingDays" name="followingDays" min="0" max="365" value="0">
              (dana)  <button id="submitFollowingDays" onclick="applyTimeFilterForFollowingPeriod(this.id)">Prikaži</button>
        </div>


        <div id="controlTypeSection" style="align-content: center">
            <button id="normal" onclick="sendControlType(this.id)">Redovna kontrola</button>
            <button id="hvp" onclick="sendControlType(this.id)">Ispitivanje na HVP</button>
        </div>

        <br>

        <div id="previewTableSection">
            <table id="previewTableSection" class="default-table">
                <tr class="previewTableHeaderRow">
                    <th>ID</th>
                    <th>Barkod</th>
                    <th>Klijent</th>
                    <?php
                    if (isset($_GET['typeOfControl']) && ($_GET['typeOfControl'] === 'hvp'))
                        echo "<th>Ispitivanje na HVP za (g/m/d)</th>";
                    else
                        echo "<th>Redovna kontrola za (g/m/d)</th>";
                    ?>
                    <th></th>
                </tr>
                <?php
                if (isset($_GET['typeOfControl']) && ($_GET['typeOfControl'] === 'hvp'))
                    printDataForHVP($devices);
                else
                    printData($devices);
                ?>
            </table>
        </div>
    </div>

</section>
</body>
</html>
