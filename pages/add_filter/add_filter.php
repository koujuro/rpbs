<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../../header/header.php";

if (SessionUtilities::checkIsSessionSet('User')) {

    if (isset($_GET['fromPage']))
        SessionUtilities::createSession('RedirectFromFilters', $_GET['fromPage']);
    $objects = [];
    $clients = fetchClientsFromDB();
    if (isset($_GET['clientID'])) {
        $objects = fetchObjectsFromDB($_GET['clientID']);
    }

} else {
    header("location: ../../index.php");
}

function checkIfFiltersAreSet() {
    if (SessionUtilities::checkIsSessionSet('ClientFilter')) {
        $clientFilter = SessionUtilities::getSession('ClientFilter');
        $clientFilter = explode(":", $clientFilter);
        echo "<div class='filter'><button id='ClientFilter' class='removeFilter' onclick='deleteFilter(this.id)'>&times;</button>  <span class='filter-text'>" . $clientFilter[1] . "</span></div>";
    }
    if (SessionUtilities::checkIsSessionSet('ObjectFilter')) {
        $objectFilter = SessionUtilities::getSession('ObjectFilter');
        $objectFilter = explode(":", $objectFilter);
        echo "<div class='filter'><button id='ObjectFilter' class='removeFilter' onclick='deleteFilter(this.id)'>&times;</button>  <span class='filter-text'>" . $objectFilter[1] . "</span></div>";
    }
    if (SessionUtilities::checkIsSessionSet('TypeFilter')) {
        $typeFilter = SessionUtilities::getSession('TypeFilter');
        echo "<div class='filter'><button id='TypeFilter' class='removeFilter' onclick='deleteFilter(this.id)'>&times;</button>  <span class='filter-text'>" . $typeFilter . "</span></div>";
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

function fetchObjectsFromDB($clientID) {
    $objects = [];
    $sql = "SELECT id, objectName
            FROM objects
            WHERE clientID = $clientID";

    $result = DataBase::selectionQuery($sql);
    if ($result->num_rows > 0)
        while ($row = $result->fetch_assoc())
            $objects []= $row;

    return $objects;
}

function printClients($clients) {
    foreach ($clients as $client) {
        $selected = (isset($_GET['clientID']) && ($_GET['clientID'] === $client['id'])) ? "active" : "";
        echo "<div class=\"clients filter-option " . $selected . "\" id=\"" . $client['id'].":".$client['clientName'] . "\" onclick=\"filterOptionsClick(this);sendChosenClient(this.id)\" data-user-id=\"".$client['id']."\"> <span class=\"option-id\">".$client['id']."</span> " . $client['clientName'] . "</div>";
    }
}

function printObjects($objects) {
    foreach ($objects as $object) {
        echo "<div class=\"objects filter-option\" id=\"" . $object['id'].":".$object['objectName'] . "\" onclick=\"filterOptionsClick(this)\" data-object-id=\"".$object['id']."\"> <span class=\"option-id\">".$object['id']."</span> " . $object['objectName'] . "</div>";
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
    <?=HTMLUtilities::ImportLinks('add-filter.css', '../../css/plugins/pickmeup.css')?>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto|Montserrat|Open+Sans" rel="stylesheet">

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
    <script src="../../js/scripts.js"></script>
    <script src="../../js/filters.js"></script>
    <script src="../../js/plugins/pickmeup.min.js"></script>
    <script src="../../handlers/add_filter/add_filter_ajax.js"></script>
    <script src="../../js/shared/jq_functions.js"></script>

</head>
<body onload="showAccountName()">

<section id="wrapper">

    <?php
    require_once "../../header/nav_menu.php";
    ?>

    <div class="container">
        <div id="filterSection">

            <div class="applied-filters">
                <div class="filters-section">
                    <h2>Odabrani filteri:</h2>
                    <?php
                    checkIfFiltersAreSet()
                    ?>
                </div>
                <div class="save">

                </div>
            </div>

            <input type="submit" id="applyFilters" name="applyFilters" value="Apply filters" onclick="applyFilters('<?php echo SessionUtilities::getSession('RedirectFromFilters') ?>')"/>

            <div class="filter-grid">
                <div class="users">
                    <h3 class="filter-heading">Korisnici</h3>

                    <div class="filter-body">
                        <div class="filter-search">
                            <input type="text" class="clientSearch" placeholder="Pretraga">
                            <button><img src="../../assets/img/search-icon.png" alt=""></button>
                        </div>

                        <?=printClients($clients);?>
                    </div>
                </div>
                <div class="objects">
                    <h3 class="filter-heading">Objekti</h3>

                    <div class="filter-body">
                        <div class="filter-search">
                            <input type="text" class="objectSearch" placeholder="Pretraga">
                            <button><img src="../../assets/img/search-icon.png" alt=""></button>
                        </div>

                        <?=printObjects($objects);?>
                    </div>

                </div>
                <div class="dates">
                    <h3 class="filter-heading">
                        <?php
                        if (SessionUtilities::getSession('RedirectFromFilters') === 'dashboard')
                            echo "Datum ugradnje od:";
                        else if (SessionUtilities::getSession('RedirectFromFilters') === 'notifications')
                            echo "Sledeća kontrola od:";
                        ?>
                    </h3>
                    <div class="date-from">

                    </div>
                    <h3 class="filter-heading">
                        <?php
                        if (SessionUtilities::getSession('RedirectFromFilters') === 'dashboard')
                            echo "Datum ugradnje do:";
                        else if (SessionUtilities::getSession('RedirectFromFilters') === 'notifications')
                            echo "Sledeća kontrola do:";
                        ?>
                    </h3>
                    <div class="date-to">

                    </div>
                </div>
                <div class="types">
                    <h3 class="filter-heading">Tip uredjaja</h3>

                    <div class="filter-body">
                        <div class="filter-search">
                            <input type="text" class="typeSearch" placeholder="Pretraga">
                            <button><img src="../../assets/img/search-icon.png" alt=""></button>
                        </div>

                        <!-- Ovde ubacuj tipove -->

                        <div class="types filter-option" id="TIP S" onclick="filterOptionsClick(this)" data-type-id="TIP S"> <span class="option-id">1</span> TIP S</div>
                        <div class="types filter-option" id="TIP CO2" onclick="filterOptionsClick(this)" data-type-id="TIP CO2"> <span class="option-id">2</span> TIP CO2</div>
                        <div class="types filter-option" id="TIP HL" onclick="filterOptionsClick(this)" data-type-id="TIP HL"> <span class="option-id">3</span> TIP HL</div>
                        <div class="types filter-option" id="TIP NAF" onclick="filterOptionsClick(this)" data-type-id="TIP NAF"> <span class="option-id">4</span> TIP NAF</div>
                        <div class="types filter-option" id="TIP Foxer" onclick="filterOptionsClick(this)" data-type-id="TIP Foxer"> <span class="option-id">5</span> TIP Foxer</div>
                        <div class="types filter-option" id="TIP Pz" onclick="filterOptionsClick(this)" data-type-id="TIP Pz"> <span class="option-id">6</span> TIP Pz</div>
                        <div class="types filter-option" id="TIP Fe36kg" onclick="filterOptionsClick(this)" data-type-id="TIP Fe36kg"> <span class="option-id">7</span> TIP Fe36kg</div>
                        <div class="types filter-option" id="CeilingFe" onclick="filterOptionsClick(this)" data-type-id="CeilingFE"> <span class="option-id">8</span> CeilingFE</div>
                        <div class="types filter-option" id="Hydrants" onclick="filterOptionsClick(this)" data-type-id="Hydrants"> <span class="option-id">9</span> Hydrants</div>
                        <div class="types filter-option" id="UPP" onclick="filterOptionsClick(this)" data-type-id="UPP"> <span class="option-id">10</span> UPP</div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<script>
    pickmeup('.date-from', {
        flat: true
    });

    pickmeup('.date-to', {
        flat: true
    });

    function filterOptionsClick(option) {
        let filterOptions = option.parentElement.querySelectorAll('.filter-option');
        filterOptions.forEach(e => {
            if(e !== option)
                e.classList.remove('active');
        });

        option.classList.toggle('active');
    }
</script>
</body>
</html>
