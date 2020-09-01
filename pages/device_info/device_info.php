<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../../header/header.php";

if (SessionUtilities::checkIsSessionSet('User') && isset($_GET['id'])) {
    $deviceId = $_GET['id'];
    $device = null;

    $sql = "SELECT type FROM sviuredjaji WHERE id=$deviceId";
    $result = DataBase::selectionQuery($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $device = DevicesContext::getInstance()->pickDevice($row['type']);
        $device->setId($deviceId);
    }

    if ($device !== null) {
        $device->getBasicInfo();
        $device->getExtendedInfo();
    }

} else {
    header("location: ../../index.php");
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PBS - Podaci o uredjaju</title>

    <!-- Styles -->
    <?=HTMLUtilities::ImportLinks("../../css/shared/history.css")?>

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
        <div id="previewTableSection" class="device">
            <div class="label">1.</div>
            <!-- Refactor -->
            <table id="previewTableSection" class="history-table">
                <tr class="previewTableHeaderRow">
                    <th>ID</th>
                    <th>BAR kod</th>
                    <th>Object ID</th>
                    <th>Tip</th>
                    <th>Vreme instalacije</th>
                    <th>Vreme poslednje kontrole</th>
                </tr>
                <?php
                $device->printBasicData();
                $device->printExtendedData();
                ?>
            </table>

        </div>
    </div>

</section>

</body>
</html>


