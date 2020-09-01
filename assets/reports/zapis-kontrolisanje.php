<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../../header/header.php";

//security ???
$profile = $_GET['profile'];
SessionUtilities::createSession('User', $profile);
$objectID = explode(":", $_GET['objectFilter'])[0];
$clientID = explode(":", $_GET['clientFilter'])[0];

/** @var ZapisKontrolisanje $izvestaj */
$izvestaj = ReportsContext::getInstance()->pickReport('zapis-kontrolisanje');
$izvestaj->fetchMainReportData($profile, $clientID, $objectID);
$izvestaj->fetchExtendedReportData();

?>

    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>
    </head>

    <style>
        table {
            width: 100%;
            border: 2px solid black;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            border-collapse: collapse;
            text-align: center;
        }
        #title {
            font-size: 20px;
            border: 2px solid black;
            width: 90%;
        }
        #box {
            width: 10px;
            height: 10px;
            border: 1px solid black;
            margin: auto;
        }
        #opis {
            text-align: center;
            margin-top: 3em;
            width: 100%;
            border: 3px solid black;
        }
        .dataInDivs {
            margin-top: 2em;
        }
        #footer {
            margin-top: 2em;
            text-align: right;
            margin-left: 2em;
        }
    </style>

    <body>
    <table>
        <tr>
            <td id="title">
                <strong>RADNI NALOG - ZAPIS O KONTROLISANJU</strong><br>INSTALACIJE HIDRANTSKE MREŽE ZA GAŠENJE POŽARA
            </td>
            <th>
                Radni nalog br. <br>
                <?php echo $_GET['reportEvidenceNum'] ?>
            </th>
        </tr>
        <tr>
            <td colspan="2">
                <strong>PODACI O NARUČIOCU / KORISNIKU USLUGA</strong>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td width="25%">
                <strong>Korisnik</strong>
            </td>
            <td>
                <?php
                echo $izvestaj->getClient()['clientName'];
                ?>
            </td>
        </tr>
        <tr>
            <td>
                Adresa korisnika
            </td>
            <td>
                <?php
                echo $izvestaj->getClient()['streetAndNumber'] . " - " . $izvestaj->getClient()['city'];
                ?>
            </td>
        </tr>
        <tr>
            <td>
                Adresa i namena objekta:
            </td>
            <td>
                <?php
                echo $izvestaj->getObject()->getStreetAndNumber() . " " . $izvestaj->getObject()->getCity() . " - " . $izvestaj->getObject()->getObjectName();
                ?>
            </td>
        </tr>
        <tr>
            <td>
                Broj podzemnih etaža
            </td>
            <td>
                <?php
                echo $izvestaj->getObject()->getFloorsAboveGround();
                ?>
            </td>
        </tr>
        <tr>
            <td>
                Broj nadzemnih etaža
            </td>
            <td>
                <?php
                echo $izvestaj->getObject()->getFloorsUnderground();
                ?>
            </td>
        </tr>
        <tr>
            <td>
                Ime i prezime kontrolora
            </td>
            <td>
                <?php
                echo $izvestaj->getOperator()['fullname'];
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border: 2px solid black">
                Dana <?php echo $_GET['reportGenerationDate']; ?> god. u vremenu od _______ do ______ časova prilikom kontrole dobijene su sledeće<br>
                vrednosti za statički,radni pritisak i protok vode vode u hidrantskoj instalaciji.
            </td>
        </tr>
        <tr>
            <th colspan="2" style="border: 2px solid black">
                Ocena ispravnosti instalacije hidrantske mreže, spisak hidrantskih ormara koji su<br>
                predmet kontrolisanja i vrednosti dobijene kontrolisanjem
            </th>
        </tr>
    </table>
    <table>
        <tr>
            <th>
                Red.<br>br.
            </th>
            <th>
                Lokacija hidrantskog<br>
                priključka,(ormara)
            </th>
            <th>
                Oznaka
            </th>
            <th>
                Tip<br>hidranta
            </th>
            <th>
                Statički<br>pritisak(bar)
            </th>
            <th>
                Dinamički<br>pritisak(bar)
            </th>
            <th>
                Protok(m3/h)
            </th>
            <th>
                Istovremeni rad<br>hidranta
            </th>
            <th>
                Napomena
            </th>
            <th>
                Ispravno
            </th>
            <th>
                Neispravno
            </th>
        </tr>
        <?php

        createRows($izvestaj->getDevices());

        ?>
    </table>
    <div id="opis">
        <strong>Opis instalacije koja je predmet kontrolisanja:</strong>
    </div>
    <div class="dataInDivs">
        1. <strong>Izvor za snabdevanje vodom</strong><br>gradska vodovodna mreža □ , Reka □ , REZERVOAR □______m 3
    </div>
    <div class="dataInDivs">
        2. <strong>Tip i vrsta hidrantske mreže:</strong><br>
        mokra □ suva □<br>
        spoljna □ unutrašnja □<br>
        <?php
        printTypesOfHydrantNetwork($izvestaj->getDevices());
        ?>
    </div>
    <div class="dataInDivs">
        3. <strong>Uređaj za povišenje pritiska:</strong> DA NE<br>
        Tip (marka) uređaja za povišenje pritiska: ______________________________________________________<br>
        Broj pumpi: _____________________<br>
        Snaga pumpi (KW) _____________________________bar<br>
        Pritisak na izlaznoj grani uređaja za povišenje pritiska je: _________bar<br>
        Uređaj za povišenje pritiska, (pumpa), se aktivira na pritisku :_______bar<br>
        Uređaj za povišenje pritiska, (pumpa), se isključuje na pritisku od:________bar
    </div>
    <?php
    printInnerAndOutterHydrantNetwork($izvestaj->getDevices());
    ?>
    <div class="dataInDivs">
        6. <strong>Napajanje pumpnog postrojenja:</strong><br>KPK □ DEA □
    </div>
    <div class="dataInDivs">
        7. <strong>UOČENA ODSTUPANJA KOD PRVOG ISPITIVANJA:</strong><br>
        7.1 <strong>Opis uočenih odstupanja kod prvog ispitivanja:</strong> _____________________________________________
    </div>
    <?php
    printDaLiZadovoljavaMreza($izvestaj->getNetwork());
    ?>
    <div class="dataInDivs">
        9. <strong>Hidranti:</strong><br>imaju potrebnu opremu □  nemaju potrebnu opremu □
    </div>
    <div class="dataInDivs">
        10. <strong>Uređaj za povišenje pritiska:</strong><br>ISPRAVAN □ NEISPRAVAN □
    </div>
    <div class="dataInDivs">
        11. <strong>OCENA ISPRAVNOSTI HIDRANTSKE INSTALACIJE:</strong><br>ISPRAVNA □ NEISPRAVNA □
    </div>
    <div class="dataInDivs">
        12. <strong>PROJEKAT HIDRANTSKE MREŽE DAT NA UVID:</strong><br>DA □ NE □
    </div>
    <div class="dataInDivs">
        13. <strong>PROJEKTOVANA KOLIČINA VODE za objekat (lit/sec):</strong><br>
    </div>
    <div class="dataInDivs">
        14. <strong>IZRAČUNATA KOLIČINA VODE za objekat (lit/sec):</strong><br>
    </div>
    <div class="dataInDivs">
        15. <strong>NA OSNOVU PROJEKTOVANE/IZRAČUNATE KOLIČINE VODE, za objekat, POTREBAN JE ISTOVREMENI RAD</strong><br>unutrašnjih hidranata(kom):_____<br>spoljašnjih hidranata(kom):_____<br>(na najudaljenijoj poziciji/lokaciji od stabilnog izvora napajanja sa vodom)
    </div>
    <div id="footer">
        Potpis kompetentnog lica koje je izvršilo kontrolisanje<br>
        ________________________________________
    </div>
    </body>
    </html>

<?php

function createRows($devices) {
    for ($i = 0; $i < count($devices); $i++) {
        $inOrder = "";
        $outOfOrder = "";
        echo "<tr>";
        echo "<td>";
        echo $i + 1;
        echo "</td>";

        echo "<td>";
        echo $devices[$i]['locationPP'];
        echo "</td>";

        echo "<td>";
        echo $devices[$i]['hMark'];
        echo "</td>";

        echo "<td>";
        echo $devices[$i]['subType'];
        echo "</td>";

        echo "<td>";
        echo $devices[$i]['staticPressure'];
        echo "</td>";

        echo "<td>";
        echo $devices[$i]['dynamicPressure'];
        echo "</td>";

        echo "<td>";
        echo $devices[$i]['m3hNetwork'];
        echo "</td>";

        echo "<td>";
        echo "</td>";

        echo "<td>";
        echo $devices[$i]['note'];
        echo "</td>";

        if ($devices[$i]['curState'] === 'in order')
            $inOrder = "&times";
        else
            $outOfOrder = "&times";
        echo "<td>";
        echo "<div id='box'>" . $inOrder . "</div>";
        echo "</td>";
        echo "<td>";
        echo "<div id='box'>" . $outOfOrder . "</div>";
        echo "</td>";
        echo "</tr>";
    }
}

function printInnerAndOutterHydrantNetwork($devices) {
    $aboveGround = 0;
    $underground = 0;
    $wallHydrants = 0;

    foreach ($devices as $device) {
        if ($device['subType'] === 'Podzemni')
            $underground++;
        else if ($device['subType'] === 'Nadzemni')
            $aboveGround++;
        else if ($device['subType'] === 'Zidni')
            $wallHydrants++;

    }

    echo "<div class=\"dataInDivs\">
    4. <strong>Spoljna hidrantska mreža:</strong><br>podzemnih hidranata- komada: _<u>" . $underground . "</u>_<br>nadzemnih hidranata komada: _<u>" . $aboveGround . "</u>_
</div>
<div class=\"dataInDivs\">
    5. <strong>Unutrašnja hidrantska mreža:</strong><br>zidnih hidranata komada: _<u>" . $wallHydrants . "</u>_
</div>";
}

function printDaLiZadovoljavaMreza($network) {
    $inOrder = "";
    $outOfOrder = "";

    if ($network['networkPressureState'] === 'satisfies')
        $inOrder = "&times";
    else if ($network['networkPressureState'] === 'dissatisfies')
        $outOfOrder = "&times";

    echo "<div class=\"dataInDivs\">
    8. <strong>Pritisak i protok vode u hidrantskoj mreži:</strong><br>ZADOVOLJAVA <div id='box'>" . $inOrder . "</div> NEZADOVOLJAVA <div id='box'>" . $outOfOrder . "</div>
</div>";
}

function printTypesOfHydrantNetwork($devices) {
    $aboveGround = "□";
    $underground = "□";
    $wallHydrants = "□";

    foreach ($devices as $device) {
        if ($device['subType'] === 'Nadzemni')
            $aboveGround = "<div id='box' style='display: inline'>&times</div>";
        else if ($device['subType'] === 'Podzemni')
            $underground = "<div id='box' style='display: inline'>&times</div>";
        else if ($device['subType'] === 'Zidni')
            $wallHydrants = "<div id='box' style='display: inline'>&times</div>";
    }

    echo "podzemni " . $underground . " nadzemni " . $aboveGround . " zidni hidranti " . $wallHydrants;
}

?>