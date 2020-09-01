<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../../header/header.php";

if (SessionUtilities::checkIsSessionSet('User') && isset($_GET['reportFor'])) {
    $errorMeasuringDevices = "";
    /** @var  $izvestaj Report */
    $izvestaj = ReportsContext::getInstance()->pickReport($_GET['reportFor']);
    $izvestaj->fetchMainReportDataFromSessions();
    $izvestaj->fetchExtendedReportData();

    if (isset($_POST['generateReport'])) {
        $izvestaj->updateDB($_POST);
        generateReport($_GET['reportFor'], $errorMeasuringDevices);
        $izvestaj->fetchExtendedReportData();
    }
    $measuringDevices = fetchMeasuringDevicesFromDB();

} else {
    header("location: ../../index.php");
}

function fetchMeasuringDevicesFromDB() {
    $measuringDevices = [];
    $currentUser = SessionUtilities::getSession('User');
    $sql = "SELECT id, type FROM measuringdevices WHERE companyID=(SELECT companyID FROM users WHERE username='$currentUser')";

    $result = DataBase::selectionQuery($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $measuringDevices []= $row;
        }
    }

    return $measuringDevices;
}

function generateReport($reportFor, &$errorMeasuringDevices) {
    $profile = SessionUtilities::getSession('User');

    $filters = "";
    addingFiltersToReportLink($filters);
    $command = "xvfb-run wkhtmltopdf \"http://pbs.rs/assets/reports/$reportFor.php?" .
                    "reportGenerationDate=" . $_POST['reportGenerationDate'] . "&reportEvidenceNum=" . $_POST['reportEvidenceNum'] .
                    "&profile=$profile$filters";
    //echo "localhost/PBS/radni_nalozi/$reportFor.php?profile=$profile$filters&measuringDevices=$measuringDevices<br>";

    if (checkIfMeasuringDevicesAreNeeded($reportFor)) {
        if (checkIfMeasuringDevicesAreSet()) {
            $measuringDevices = SessionUtilities::getSession('MDFilter');
            $command .= "&numOfObjects=" . $_POST['numOfObjects'] . "&measuringDevices=$measuringDevices\" report.pdf";
            SessionUtilities::unsetSession('MDFilter');
            //echo $command . "<br>";
            executingShellCommandForGeneratingReport($command);
        } else {
            $errorMeasuringDevices = "Niste izabrali nijedan merni uredjaj!";
        }
    } else {
        $command .= "\" report.pdf";
        //echo $command . "<br>";
        executingShellCommandForGeneratingReport($command);
    }
}

function addingFiltersToReportLink(&$filters) {
    if (SessionUtilities::checkIsSessionSet('ClientFilter')) {
        $filters .= "&clientFilter=" . SessionUtilities::getSession('ClientFilter');
    }
    if (SessionUtilities::checkIsSessionSet('ObjectFilter')) {
        $filters .= "&objectFilter=" . SessionUtilities::getSession('ObjectFilter');
    }
    if (SessionUtilities::checkIsSessionSet('TypeFilter')) {
        $filters .= "&typeFilter=" . SessionUtilities::getSession('TypeFilter');
    }
    if (SessionUtilities::checkIsSessionSet('TimeFilter')) {
        $filters .= "&timeFilter=" . SessionUtilities::getSession('TimeFilter');
    }
}

function checkIfMeasuringDevicesAreNeeded($reportFor) {
    return ($reportFor === 'obr-38-2' || $reportFor === 'obr-r08-35-1')?true:false;
}

function checkIfMeasuringDevicesAreSet() {
    if (SessionUtilities::checkIsSessionSet('MDFilter')) {
        if (SessionUtilities::getSession('MDFilter') !== '')
            return true;
    }
    return false;
}

function executingShellCommandForGeneratingReport($command) {
    shell_exec($command);
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="'.date("d_m_Y_H:i:s").'.pdf"');
    readfile('report.pdf');
    shell_exec("rm -rf report.pdf");
}

function printDevices($devices) {
    foreach ($devices as $device) {
        echo "<div class=\"devices filter-option\" id=\"" . $device['id'] . "\" onclick=\"filterOptionsClick(this.id);sendChosenMeasuringDevices()\" data-type-id=\"".$device['id']."\"> <span class=\"option-id\">".$device['id']."</span> ".$device['type']."</div>";
    }
}

function printGenericReportInfo() {

}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Generisanje izvestaja</title>

    <!-- Styles -->
    <?=HTMLUtilities::ImportLinks("../edit_devices/edit-devices.css", "report_generating.css");?>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto|Montserrat|Open+Sans" rel="stylesheet">

    <!-- Scripts -->
    <script src="../../js/scripts.js"></script>
    <script src="../../handlers/reportGenerator/reportGeneratorAjax.js"></script>
    <script>
        function filterOptionsClick(deviceId) {
            if (deviceId !== "init") {
                let filterOptions = document.getElementById(deviceId).parentElement.querySelectorAll('.filter-option');

                document.getElementById(deviceId).classList.toggle('active');

            }
        }
    </script>
</head>
<body onload="showAccountName()">

<section id="wrapper">

    <?php
    require_once "../../header/nav_menu.php";
    ?>


    <div class="container">

        <div class="filter-grid">
            <div class="types-page">
                <h3 class="filter-heading">Instrumenti</h3>

                <div class="filter-body">
                    <div class="filter-search">
                        <input type="text" placeholder="Pretraga">
                        <button><img src="../../assets/img/search-icon.png" alt=""></button>
                    </div>

                    <?php
                    printDevices($measuringDevices);
                    ?>
                </div>

            </div>
            <form action='report_generating.php?reportFor=<?php echo $_GET['reportFor'];?>' method="post">

                <?php
                echo $izvestaj->generateHTML();
                ?>

                <div>
                    Datum stampanja izvestaja: <input type='text' name='reportGenerationDate' value='<?php echo date("d.m.Y") ?>'>
                    Evidencijski broj isprave: <input type='text' name='reportEvidenceNum' value=''>
                </div>

                <div style="margin-top: 50px; margin-bottom: 50px; color: #01aef0">
                    <label for="" style="font-size: 20px; font-weight: bold">Generiši izveštaj:</label><br><br>
                    <input type="radio" name="numOfObjects" value="single" checked>Za izabrani objekat<br>
                    <input type="radio" name="numOfObjects" value="multi">Za sve objekte na adresi izabranog objekta<br>
                </div>

                <input type="submit" id="unesi" name="generateReport" value="Generisi"/>
            </form>
            <?php
            if ($errorMeasuringDevices !== "" && checkIfMeasuringDevicesAreNeeded($_GET['reportFor'])) {
                echo "<script>alert('" . $errorMeasuringDevices . "')</script>";
            }
            ?>
        </div>

    </div>

</section>

</body>
</html>
