<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../../header/header.php";

if (SessionUtilities::checkIsSessionSet('User')) {
    $measuringDevice = new MeasuringDevice();

    if (isset($_POST['newMD_submit'])) {
        $measuringDevice->setParametersForNewMD($_POST);
        $measuringDevice->insertNewMDIntoDatabase();
    }
    if (isset($_POST['editMD_submit']) && ($_POST['editMD_id'] !== "init")) {
        $measuringDevice->setParametersFromPOST($_POST);
        $measuringDevice->updateMDInDatabase();
    }
    if (isset($_POST['deleteMD_submit']) && ($_POST['editMD_id'] !== "init")) {
        $measuringDevice->setParametersFromPOST($_POST);
        $measuringDevice->deleteMDInDatabase();
    }

    $measuringDevices = MeasuringDevice::fetchAllMDsFromDatabase();
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
    <title>PBS - Merni uredjaji</title>

    <!-- Styles -->
    <?=HTMLUtilities::ImportLinks('../../css/shared/measuring-devices.css')?>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto|Montserrat|Open+Sans" rel="stylesheet">

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
    <script src="../../js/jq_functions.js"></script>
    <script src="../../js/scripts.js"></script>
    <script src="../../handlers/measuring_devices/measuring_devices_ajax.js"></script>
</head>
<body onload="showAccountName()">

<section id="wrapper">

    <?php
    require_once "../../header/nav_menu.php";
    ?>

    <div class="container">

        <div class="devices-grid">

            <div class="new-device-entry">
                <h1 class="panel-heading">Unos novog mernog instrumenta</h1>

                <form action="measuring_devices.php" class="devices-form" method="post">
                    <div class="form-group">
                        <label for="">Tip</label>
                        <input type="text" id="newMD_type" name="newMD_type">
                    </div>

                    <div class="form-group">
                        <label for="">Proizvođač</label>
                        <input type="text" id="newMD_manufacturer" name="newMD_manufacturer">
                    </div>

                    <div class="form-group">
                        <label for="">Fabrički broj</label>
                        <input type="text" id="newMD_fabricID" name="newMD_fabricID">
                    </div>

                    <div class="form-group">
                        <label for="">Klasa tačnosti</label>
                        <input type="text" id="newMD_accuracyClass" name="newMD_accuracyClass">
                    </div>

                    <div class="form-group">
                        <label for="">Uverenje o etaloniranju</label>
                        <input type="text" id="newMD_calibrationTestimonial" name="newMD_calibrationTestimonial">
                    </div>


                    <div class="form-group submit">
                        <input type="submit" id="newMD_submit" name="newMD_submit" value="Unesi">
                    </div>
                </form>
            </div>


            <div class="change-device">
                <h1 class="panel-heading">Izmene podataka o mernom instrumentu</h1>

                <div class="panel-body">
                    <div class="types-page">
                        <h3 class="filter-heading">Uredjaji</h3>

                        <div class="filter-body">
                            <div class="filter-search">
                                <input type="text" class="search" placeholder="Pretraga">
                                <button><img src="../../assets/img/search-icon.png" alt=""></button>
                            </div>
                            <?php
                            $measuringDevice->generateHTMLListOfDevices($measuringDevices);
                            ?>
                        </div>

                    </div>

                    <form action="measuring_devices.php" class="devices-form" method="post">
                        <input type="hidden" id="editMD_id" name="editMD_id" value="init">
                        <div class="form-group">
                            <label for="">Tip</label>
                            <input type="text" id="editMD_type" name="editMD_type">
                        </div>

                        <div class="form-group">
                            <label for="">Proizvođač</label>
                            <input type="text" id="editMD_manufacturer" name="editMD_manufacturer">
                        </div>

                        <div class="form-group">
                            <label for="">Fabrički broj</label>
                            <input type="text" id="editMD_fabricID" name="editMD_fabricID">
                        </div>

                        <div class="form-group">
                            <label for="">Klasa tačnosti</label>
                            <input type="text" id="editMD_accuracyClass" name="editMD_accuracyClass">
                        </div>

                        <div class="form-group">
                            <label for="">Uverenje o etaloniranju</label>
                            <input type="text" id="editMD_calibrationTestimonial" name="editMD_calibrationTestimonial">
                        </div>


                        <div class="form-group submit">
                            <input type="submit" class="del" id="deleteMD_submit" name="deleteMD_submit" value="Obrisi">
                            <input type="submit" id="editMD_submit" name="editMD_submit" value="Sacuvaj">
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </div>

</section>

<script>
    function filterOptionsClick(option) {
        let filterOptions = option.parentElement.querySelectorAll('.filter-option');

        // Menjanje klasa

        filterOptions.forEach(e => {
            if(e !== option)
                e.classList.remove('active');
        });

        option.classList.toggle('active');
    }
</script>
</body>
</html>
