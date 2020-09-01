<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../../header/header.php";

//security ???
$profile = $_GET['profile'];
SessionUtilities::createSession('User', $profile);
$clientId = explode(":", $_GET['clientFilter'])[0];
$objectID = explode(":", $_GET['objectFilter'])[0];

/** @var ZapisAparati $izvestaj */
$izvestaj = ReportsContext::getInstance()->pickReport('zapis-aparati');
$izvestaj->fetchMainReportData($profile, $clientId, $objectID);
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
            #mp {
                padding-left: 50%;
                float: left;
            }
            #signature {
                padding-right:0.5em;
                text-align: right;
            }
        </style>

    </head>
    <body>
    <table>
        <tr>
            <td id="title">
                <strong>RADNI NALOG - ZAPIS O KONTROLISANJU</strong><br>MOBILNIH UREĐAJA ZA GAŠENJE POČETNIH POŽARA
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
                echo $izvestaj->getClient()['streetAndNumber'];
                ?>
            </td>
        </tr>
        <tr>
            <td>
                Adresa objekta
            </td>
            <td>
                <?php
                echo $izvestaj->getObject()->getStreetAndNumber();
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
            <td>
                Datum kontrolisanja
            </td>
            <td>
                <?php
                echo $_GET['reportGenerationDate'];
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border: 2px solid black">
                <strong>Spisak uredjaja koji su predmet kontrolisanja sa podacima i ocenom ispravnost</strong>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <th>
                Red.<br>br.
            </th>
            <th>
                Lokacija uredjaja
            </th>
            <th>
                Tip<br>uredjaja
            </th>
            <th>
                Fabrički<br>broj
            </th>
            <th>
                Godina<br>proizvodnje
            </th>
            <th>
                Proizvođač
            </th>
            <th>
                Ispravno
            </th>
            <th>
                Neispravno
            </th>
            <th>
                Napomena
            </th>
        </tr>
        <?php

        createRows($izvestaj->getDevices(), $izvestaj->getLocationsAndNotes());

        ?>
    </table>
    <table>
        <tr>
            <th>
                <div id="mp">
                    M.P.
                </div>
                <div id="signature">
                    Potpis kompetentnog lica koje je izvršilo kontrolisanje:_____________________________
                </div>
            </th>
        </tr>
    </table>
    </body>
    </html>


<?php

function createRows($devices, $locationsAndNotes) {
    for ($i = 0; $i < count($devices); $i++) {
        $inOrder = "";
        $outOfOrder = "";
        echo "<tr>";
        echo "<td>";
        echo $i + 1;
        echo "</td>";

        echo "<td>";
        echo $locationsAndNotes[$i]['locationPP'];
        echo "</td>";

        echo "<td>";
        echo $devices[$i]->getPpaparatDictionaryValue('subType');
        echo "</td>";

        echo "<td>";
        echo $devices[$i]->getPpaparatDictionaryValue('fabricId');
        echo "</td>";

        echo "<td>";
        echo $devices[$i]->getPpaparatDictionaryValue('creationYear');
        echo "</td>";

        echo "<td>";
        echo $devices[$i]->getPpaparatDictionaryValue('manufacturerData');
        echo "</td>";

        if ($locationsAndNotes[$i]['curState'] === 'in order')
            $inOrder = "&times";
        else
            $outOfOrder = "&times";

        echo "<td>";
        echo "<div id='box'>" . $inOrder . "</div>";
        echo "</td>";

        echo "<td>";
        echo "<div id='box'>" . $outOfOrder . "</div>";
        echo "</td>";

        echo "<td>";
        echo $locationsAndNotes[$i]['note'];
        echo "</td>";
        echo "</tr>";
    }
}

?>