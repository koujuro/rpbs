<?php

require_once "../../header/header.php";


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

    <!-- Styles -->
    <?=HTMLUtilities::ImportLinks("../../css/dashboard.css", "map.css");?>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto|Montserrat|Open+Sans" rel="stylesheet">

    <script src="../../js/scripts.js"></script>
    <script src="../../js/filters.js"></script>
    <title>Mapa</title>

</head>
<body onload="showAccountName()">
<section id="wrapper">

    <?php
    require_once "../../header/nav_menu.php";
    ?>

    <div class="applied-filters">
        <div class="filters-section">
            <a href="../add_filter/add_filter.php?fromPage=map"><button id="addFilterButton" name="addFilterButton">+ Dodaj filter</button></a>
            <?php
            checkIfFiltersAreSet();
            ?>

        </div>
        <div class="save">

        </div>
    </div>

    <br/><br/>

</section>
<div id="map"></div>

<script>
    function getLocations() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200)
                setLocationsOnMap(JSON.parse(this.responseText));
        };
        xhttp.open("POST", "get_locations.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send();
    }

    function setLocationsOnMap(locations) {
        console.log(locations.length);
        let belgradeLocation = {lat: 44.787197, lng: 20.457273};
        //set map to point Belgrade
        let map = new google.maps.Map(document.getElementById('map'), {zoom: 8, center: belgradeLocation});
        for (let i = 0; i < locations.length; i++) {
            let location = {lat: parseFloat(locations[i]['latitude']), lng: parseFloat(locations[i]['longitude'])};
            new google.maps.Marker({position: location, map: map});
        }
    }
</script>
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=null&callback=getLocations">
</script>

</body>
</html>

