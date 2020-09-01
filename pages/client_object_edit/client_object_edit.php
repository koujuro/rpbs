<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../../header/header.php";

if (SessionUtilities::checkIsSessionSet('User')) {
    if (isset($_POST['newClientSubmit'])) {
        $currentUser = SessionUtilities::getSession('User');
        $companyID = null;
        $clientName = $_POST['newClientName'];
        $streetAndNumber = $_POST['newStreetAndNumber'];
        $city = $_POST['newCity'];
        $PAC = $_POST['newPAC'];
        $sql = "SELECT companyID FROM users WHERE username='$currentUser'";
        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $companyID = $row['companyID'];
        }

        $sql = "INSERT INTO clients (`companyID`, `clientName`, `streetAndNumber`, `city`, `PAC`) 
                VALUES ($companyID, '$clientName', '$streetAndNumber', '$city', '$PAC')";
        DataBase::executionQuery($sql);
    }
    if (isset($_POST['editClientSubmit'])) {
        $info = [$_POST['editClientId'],
            $_POST['editClientName'],
            $_POST['editClientStreetAndNumber'],
            $_POST['editClientCity'],
            $_POST['editClientPAC']];
        updateClientsTableInDB($info);
    }
    if (isset($_POST['newObjectSubmit'])) {
        $clientID = $_POST['newObjectClientDropdown'];
        $streetAndNumber = $_POST['newObjectStreetAndNumber'];
        $city = $_POST['newObjectCity'];
        $PAC = $_POST['newObjectPAC'];
        $objectName = $_POST['newObjectName'] . " " . $_POST['newObjectPurpose'];
        $floorsAboveGround = $_POST['newFloorsAboveGround'];
        $floorsUnderground = $_POST['newFloorsUnderground'];
        $highestObjectAltitude = $_POST['newHighestObjectAltitude'];
        $sql = "INSERT INTO `objects`(`clientID`, `streetAndNumber`, `city`, `PAC`, `objectName`, `floorsAboveGround`, `floorsUnderground`, `highestObjectAltitude`) 
                VALUES ($clientID, '$streetAndNumber', '$city', $PAC, '$objectName', $floorsAboveGround, $floorsUnderground, $highestObjectAltitude)";
        DataBase::executionQuery($sql);
    }
    if (isset($_POST['editObjectSubmit'])) {
        updateObjectsTableInDB($_POST);
    }
    if (isset($_POST['deleteClientSubmit'])) {
        $clientID = $_POST['editClientId'];
        $sql = "DELETE FROM clients WHERE id=$clientID";
        DataBase::executionQuery($sql);
    }
    if (isset($_POST['deleteObjectSubmit'])) {
        $objectID = $_POST['editObjectId'];
        $sql = "DELETE FROM objects WHERE id=$objectID";
        DataBase::executionQuery($sql);
    }

    $clients = fetchClientsFromDB();
    //$objects = fetchObjectsFromDB();

} else {
    header("location: ../../index.php");
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

function printClientsOptions($clients) {
    foreach ($clients as $client) {
        echo "<div class='clients filter-option clients' data-search-term=\"" . $client['clientName'] . "\" onclick='filterOptionsClick(this);sendChosenClient(" . $client['id'] . ")'> <span class='option-id'>" . $client['id'] . "</span> " . $client['clientName'] . "</div>";
    }
}

function printNewObjectClientDropdownMenuOptions($clients) {
    echo "<div class='form-group'>";
    echo "<label for=''>Klijent:</label> <select id='newObjectClientDropdown' name='newObjectClientDropdown'>";
    foreach ($clients as $client) {
        echo "<option value='" . $client['id'] . "'>" . $client['clientName'] . "</option>";
    }
    echo "</select></div>";
}

function printEditObjectClientDropdownMenuOptions($clients) {
    echo "<div class='form-group'>";
    echo "<label for=''>Klijent:</label> <select id='newObjectClientDropdown' name='newObjectClientDropdown' onchange='sendChosenClientForEdit(this.value)'>
                    <option value='init'>- Choose Client -</option>";
    foreach ($clients as $client) {
        $selected = (isset($_GET['editClientId']) && ($_GET['editClientId'] === $client['id'])) ? "selected" : "";
        echo "<option value='" . $client['id'] . "' " . $selected . ">" . $client['clientName'] . "</option>";
    }
    echo "</select></div>";
}

function printObjectsOptions() {
    if (isset($_GET['editClientId'])) {
        $clientID = $_GET['editClientId'];
        $objects = fetchObjectsFromDB($clientID);
        foreach ($objects as $object) {
            echo "<div class='objects filter-option objects' data-search-term=\"" . $object['objectName'] . "\" onclick='filterOptionsClick(this);sendChosenObject(" . $object['id'] . ")'> <span class='option-id'>" . $object['id'] . "</span> " . $object['objectName'] . "</div>";
        }
    }
}

function updateClientsTableInDB($info) {
    $sql = "UPDATE clients SET clientName='$info[1]',
                               streetAndNumber='$info[2]',
                               city='$info[3]',
                               PAC=$info[4]
                               WHERE id=$info[0]";

    DataBase::executionQuery($sql);
}

function updateObjectsTableInDB($info) {
    $sql = "UPDATE objects SET objectName='" . $info['editObjectName'] . " " . $info['editObjectPurpose'] . "',
                               streetAndNumber='" . $info['editObjectStreetAndNumber'] . "',
                               city='" . $info['editObjectCity'] . "',
                               PAC=" . $info['editObjectPAC'] . ",
                               floorsAboveGround=" . $info['editFloorsAboveGround'] . ",
                               floorsUnderground=" . $info['editFloorsUnderground'] . ",
                               highestObjectAltitude=" . $info['editHighestObjectAltitude'] . " 
                               WHERE id=" . $info['editObjectId'];

    DataBase::executionQuery($sql);
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PBS - Podaci klijenti i objekti</title>

    <!-- Styles -->
    <?=HTMLUtilities::ImportLinks("client_object_edit.css", "../../css/shared/measuring-devices.css");?>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto|Montserrat|Open+Sans" rel="stylesheet">

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
    <script src="../../js/scripts.js"></script>
    <script src="../../handlers/client_object_edit/client_object_edit_ajax.js"></script>
    <script src="../../js/shared/jq_functions.js"></script>
</head>
<body onload="showAccountName()">

<section id="wrapper">

    <?php
    require_once "../../header/nav_menu.php";
    ?>

    <div id="clientObjectSection">

        <div class="container">

            <div class="devices-grid operators users-and-objects">

                <div class="new-device-entry">
                    <h1 class="panel-heading">Unos korisnika</h1>

                    <form action="client_object_edit.php" class="devices-form" method="post">
                        <div class="form-group">
                            <label for="">Ime</label>
                            <input type="text" id="newClientName" name="newClientName">
                        </div>

                        <div class="form-group">
                            <label for="">Adresa</label>
                        </div>

                        <div class="form-group">
                            <label for="">Ulica i broj</label>
                            <input type="text" id="newStreetAndNumber" name="newStreetAndNumber">
                        </div>

                        <div class="form-group">
                            <label for="">Mesto</label>
                            <input type="text" id="newCity" name="newCity">
                        </div>

                        <div class="form-group">
                            <label for="">Postanski broj</label>
                            <input type="text" id="newPAC" name="newPAC">
                        </div>

                        <div class="form-group submit">
                            <input type="submit" id="newClientSubmit" name="newClientSubmit" value="Unesi">
                        </div>
                    </form>
                </div>

                <div class="change-device">
                    <h1 class="panel-heading">Izmene podataka o korisniku</h1>

                    <div class="panel-body">
                        <div class="types-page">
                            <h3 class="filter-heading">Korisnici</h3>

                            <div class="filter-body">
                                <div class="filter-search">
                                    <input type="text" class="clientSearch" placeholder="Pretraga">
                                    <button><img src="../../assets/img/search-icon.png" alt=""></button>
                                </div>
                                <?php
                                printClientsOptions($clients);
                                ?>
                            </div>
                        </div>

                        <form action="client_object_edit.php" class="devices-form" method="post">
                            <input type="hidden" id="editClientId" name="editClientId" value="">
                            <div class="form-group">
                                <label for="">Ime</label>
                                <input type="text"  id='editClientName' name='editClientName'>
                            </div>

                            <div class="form-group">
                                <label for="">Adresa</label>
                            </div>

                            <div class="form-group">
                                <label for="">Ulica i broj</label>
                                <input type="text" id='editClientStreetAndNumber' name='editClientStreetAndNumber'>
                            </div>

                            <div class="form-group">
                                <label for="">Mesto</label>
                                <input type="text" id='editClientCity' name='editClientCity'>
                            </div>

                            <div class="form-group">
                                <label for="">Postanski broj</label>
                                <input type="text" id='editClientPAC' name='editClientPAC'>
                            </div>

                            <div class="form-group submit">
                                <input type="submit" class="del" id="deleteClientSubmit" name="deleteClientSubmit" value="Obrisi">
                                <input type="submit" id="editClientSubmit" name="editClientSubmit" value="Sacuvaj">
                            </div>
                        </form>
                    </div>
                </div>

            </div>

            <div class="devices-grid operators users-and-objects">

                <div class="new-device-entry">
                    <h1 class="panel-heading">Unos objekta</h1>

                    <form action="client_object_edit.php" class="devices-form" method="post">
                        <?php
                        printNewObjectClientDropdownMenuOptions($clients);
                        ?>

                        <div class="form-group">
                            <label for="">Adresa</label>
                        </div>

                        <div class="form-group">
                            <label for="">Ulica i br</label>
                            <input type="text" id='newObjectStreetAndNumber' name='newObjectStreetAndNumber'>
                        </div>

                        <div class="form-group">
                            <label for="">Mesto</label>
                            <input type="text" id='newObjectCity' name='newObjectCity'>
                        </div>

                        <div class="form-group">
                            <label for="">Postanski broj</label>
                            <input type="text" id='newObjectPAC' name='newObjectPAC'>
                        </div>

                        <div class="form-group">
                            <label for="">Ime objekta</label>
                            <input type="text" id='newObjectName' name='newObjectName'>
                        </div>

                        <div class="form-group">
                            <label for="">Namena objekta</label>
                            <input type="text" id='newObjectPurpose' name='newObjectPurpose'>
                        </div>

                        <div class="form-group">
                            <label for="">Br nadzemnih etaza</label>
                            <input type="text" id='newFloorsAboveGround' name='newFloorsAboveGround'>
                        </div>

                        <div class="form-group">
                            <label for="">Br podzemnih etaza</label>
                            <input type="text" id='newFloorsUnderground' name='newFloorsUnderground'>
                        </div>

                        <div class="form-group">
                            <label for="">Najvisa visinska kvota</label>
                            <input type="text" id='newHighestObjectAltitude' name='newHighestObjectAltitude'>
                        </div>


                        <div class="form-group submit">
                            <input type="submit" id="newObjectSubmit" name="newObjectSubmit" value="Unesi">
                        </div>
                    </form>
                </div>

                <div class="change-device">
                    <h1 class="panel-heading">Izmene podataka o objektu</h1>

                    <div class="panel-body">
                        <div class="types-page">
                            <h3 class="filter-heading">Objekat</h3>

                            <div class="filter-body">
                                <div class="filter-search">
                                    <input type="text" class="objectSearch" placeholder="Pretraga">
                                    <button><img src="../../assets/img/search-icon.png" alt=""></button>
                                </div>
                                <?php
                                printObjectsOptions();
                                ?>
                            </div>

                        </div>

                        <form action="client_object_edit.php" class="devices-form" method="post">
                            <input type="hidden" id="editObjectId" name="editObjectId" value="">
                            <?php
                            printEditObjectClientDropdownMenuOptions($clients);
                            ?>
                            <div class="form-group">
                                <label for="">Adresa</label>
                            </div>

                            <div class="form-group">
                                <label for="">Ulica i br</label>
                                <input type="text" id='editObjectStreetAndNumber' name='editObjectStreetAndNumber'>
                            </div>

                            <div class="form-group">
                                <label for="">Mesto</label>
                                <input type="text" id='editObjectCity' name='editObjectCity'>
                            </div>

                            <div class="form-group">
                                <label for="">Postanski broj</label>
                                <input type="text" id='editObjectPAC' name='editObjectPAC'>
                            </div>

                            <div class="form-group">
                                <label for="">Ime objekta</label>
                                <input type="text" id='editObjectName' name='editObjectName'>
                            </div>

                            <div class="form-group">
                                <label for="">Namena objekta</label>
                                <input type="text" id='editObjectPurpose' name='editObjectPurpose'>
                            </div>

                            <div class="form-group">
                                <label for="">Br nadzemnih etaza</label>
                                <input type="text" id='editFloorsAboveGround' name='editFloorsAboveGround'>
                            </div>

                            <div class="form-group">
                                <label for="">Br podzemnih etaza</label>
                                <input type="text" id='editFloorsUnderground' name='editFloorsUnderground'>
                            </div>

                            <div class="form-group">
                                <label for="">Najvisa visinska kvota</label>
                                <input type="text" id='editHighestObjectAltitude' name='editHighestObjectAltitude'>
                            </div>

                            <div class="form-group submit">
                                <input type="submit" id="deleteObjectSubmit" name="deleteObjectSubmit" class="del" value="Obrisi">
                                <input type="submit" id="editObjectSubmit" name="editObjectSubmit" value="Sacuvaj">
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

        // Menjanje klasa
    }
</script>
</body>
</html>


