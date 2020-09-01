<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../../header/header.php";

//security ???
$profile = $_GET['profile'];
$clientId = (int)explode(":", $_GET['clientFilter'])[0];
$objectID = explode(":", $_GET['objectFilter'])[0];
SessionUtilities::createSession('MDFilter', $_GET['measuringDevices']);
SessionUtilities::createSession('NumOfObjects', $_GET['numOfObjects']);
if (isset($_GET['timeFilter']))
    SessionUtilities::createSession('TimeFilter', $_GET['timeFilter']);

/** @var OBR_38_2 $izvestaj */
$izvestaj = ReportsContext::getInstance()->pickReport('obr-38-2');
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
            #box {
                width: 10px;
                height: 10px;
                border: 1px solid black;
                margin: auto;OPIS
            }
            #title {
                text-align: center;
                border: 2px solid black;
                width: 100%;
            }
        </style>

    </head>
    <body>
    <table style="height: 150px; border: none">
        <tr>
            <td width="10%" style="border: none; text-align: right">
                <img src="../uploadImage/uploads/<?php echo $izvestaj->getCompany()['imgName'] ?>.jpg" height="150">
            </td>
            <td width="30%" style="border: none; text-align: left">
                <?php echo $izvestaj->getCompany()['companyName'] ?><br>
                <?php echo $izvestaj->getCompany()['street'] . " br. " . $izvestaj->getCompany()['number']?><br>
                <?php echo $izvestaj->getCompany()['PAC'] . " " . $izvestaj->getCompany()['city']?><br>
                <?php echo "Tel. " . $izvestaj->getCompany()['phoneNumber'] ?><br>
                <a href="https://www.varnost-fitep.rs"><?php echo $izvestaj->getCompany()['webSite'] ?></a>
            </td>
            <td width="50%">
                <div style="float: left; text-align: left">
                    SLUŽBA ZAŠTITE OD POŽARA<br>
                    Tel. servisa: <?php echo $izvestaj->getCompany()['servicePhoneNumber'] ?><br>
                    Tel. službe : <?php echo $izvestaj->getCompany()['officePhoneNumber']?><br>
                    Mob. tel: <?php echo $izvestaj->getCompany()['mobilePhoneNumber'] ?><br>
                    e-mail: <?php echo $izvestaj->getCompany()['eMail'] ?>
                </div>
                <div style="text-align: right">
                    ZNAK<br>
                    AKREDITACIJE<br><br>
                    Br.potvrde ATS : <?php echo $izvestaj->getCompany()['numberATS']?>
                </div>
            </td>
        </tr>
    </table>
    <br><br>
    <div style="width: 100%; text-align: left">
        <strong>Korisnik:</strong>
    </div>
    <div id="title">
        <strong>ISPRAVA O KONTROLISANJU INSTALACIJA HIDRANTSKE MREŽE<br>ZA GAŠENJE POŽARA
            <br><br><?php echo $izvestaj->getClient()['clientName'] ?></strong>
        <br><?php echo $izvestaj->getClient()['streetAndNumber'] ?><br>
        <?php echo $izvestaj->getClient()['PAC'] . " " . $izvestaj->getClient()['city'] ?><br>

    </div>
    <table>
        <tr>
            <th rowspan="2">VRSTA<br>ISPITIVANJA</th>
            <td>PRVO <div id="box"></div></td>
            <td>Evidencijski<br>broj isprave</td>
            <td width="35%"><?php echo $_GET['reportEvidenceNum'] ?></td>
        </tr>
        <tr>
            <td>PERIODIČNO <div id="box"></div></td>
            <td>Datum<br>isprave</td>
            <td width="35%"><?php echo $_GET['reportGenerationDate']; ?></td>
        </tr>
        <tr>
            <th colspan="4" style="text-align: left">
                A. PODACI O PRAVNOM LICU OVLAŠĆENOM ZA OBAVLJANJE POSLOVA<br>
                KONTROLISANJA INSTALACIJE HIDRANTSKE MREŽE
            </th>
        </tr>
        <tr>
            <td style="text-align: left">
                NAZIV PRAVNOG <br>LICA:
            </td>
            <td colspan="3">
                <?php echo $izvestaj->getCompany()['companyName'] ?>
            </td>
        </tr>
        <tr>
            <td rowspan="2" style="text-align: left">
                ADRESA <br>PRAVNOG LICA:
            </td>
            <td style="text-align: left">
                Mesto
            </td>
            <td colspan="2">
                <?php echo $izvestaj->getCompany()['city'] . " " . $izvestaj->getCompany()['PAC'] ?>
            </td>
        </tr>
        <tr>
            <td style="text-align: left">
                Ulica i broj
            </td>
            <td colspan="2">
                <?php echo $izvestaj->getCompany()['street'] . " " . $izvestaj->getCompany()['number'] ?>
            </td>
        </tr>
        <tr>
            <td style="text-align: left">
                Broj Rešenja o utvrđivanju ispunjenosti uslova<br>za obavljanje poslova kontrolisanja:
            </td>
            <td colspan="3" style="text-align: left">
                <?php echo $izvestaj->getCompany()['controlLicenceNumber'] ?>
            </td>
        </tr>
    </table>
    <div id="title">
        <strong>B. PODACI O KOMPETENTNIM LICIMA KOJA SU OBAVILA KONTROLISANJE</strong>
    </div>
    <table>
        <tr>
            <th width="8%">Redni<br>Br.</th>
            <th>Ime i Prezime</th>
            <th width="40%">
                BR. UVERENJA O POLOŽENOM<br>
                STRUČNOM ISPITU
            </th>
        </tr>
        <?php

        createRows($izvestaj->getOperators(), $profile);

        ?>
    </table>
    <div id="title">
        V. PODACI O UPOTREBLJENOJ OPREMI I MERNIM INSTRUMENTIMA
    </div>
    <table>
        <?php

        createVrows($izvestaj->getMeasuringDevices());

        ?>
    </table>
    <div id="title">
        G. SPISAK PROPISA NA OSNOVU KOJIH JE IZVRŠENO KONTROLISANJE<br>
        INSTALACIJE HIDRANTSKE MREŽE ZA GAŠENJE POŽARA
    </div>
    <table>
        <tr>
            <th width="8%">1.</th>
            <td style="text-align: left">
                Zakon o zaštiti od požara (’’Sl. glasnik RS’’ br 111/2009 i br.20/2015)
            </td>
        </tr>
        <tr>
            <th width="8%">2.</th>
            <td style="text-align: left">
                Pravilnik o tehničkim normativima za instalacije hidrantske mreže za gašenje požara (Sl.glasnik RS br. 3/18)
            </td>
        </tr>
        <tr>
            <th width="8%">3.</th>
            <td style="text-align: left">
                Članovi 4,11,15,16,17,18,19, 20 pravilnika o posebnim uslovima koje moraju ispunjavati pravna lica koja
                dobijaju ovlašćenje za obavljanje poslova kontrolisanja instalacija i uređaja za gašenje požara
                i instalacija posebnih sistema (’’Sl glasnik RS’’ br. 52/15)
            </td>
        </tr>
        <tr>
            <th width="8%">4.</th>
            <td style="text-align: left">
                SRPS ISO 17020
            </td>
        </tr>
    </table>
    <br><br>
    <div id="title">
        D. OPIS INSTALACIJE HIDRANTSKE MREŽE I KRATAK OPIS OBJEKTA
    </div>
    <table>
        <?php

        if ($_GET['numOfObjects'] === 'single') {
            $objects []= $izvestaj->getObject();
            createDrows($objects);
        }
        else
            createDrows($izvestaj->getObjects());
        ?>
    </table>
    <table>
        <tr>
            <td>
                <strong>OPIS INSTALACIJE HIDRANTSKE MREŽE ZA GAŠENJE POŽARA</strong><br>
                (izvori za snabdevanje vodom, stalno postrojenje za zahvatanje vodom, rezervoari za vodu, pumpna stanica,<br>
                tip i vrsta hidrantske mreže, uređaj za povišenje pritiska, povezanost sa drugim sistemima u funkciji gašenja<br>
                požara i dr.)
            </td>
        </tr>
        <tr>
            <td style="text-align: left">
                1. Izvor za snabdevanje vodom - gradska vodovodna mreža , REZERVOAR m 3<br>
                2. Tip i vrsta hidrantske mreže<br>
                mokra - suva<br>
                spoljna – unutrašnja<br>
                podzemni, nadzemni, zidni hidranti<br>
                3. Uređaj za povišenje pritiska - /<br>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td>
                <strong>OPIS INSTALACIJE HIDRANTSKE MREŽE KOJA JE PREDMET KONTROLISANJA</strong><br>
                (spoljna hidrantska mreža, unutrašnja hidrantska mreža, snabdevanje električnom energijom, pumpno
                postrojenje i dr.)
            </td>
        </tr>
        <tr>
            <td style="text-align: left">
                <?php
                printDataOfHNByGivenDevices($izvestaj->getDevices());
                ?>
            </td>
        </tr>
    </table>
    <br>
    <table>
        <tr>
            <th>
                Đ.OPIS ISPITIVANJA U ZAVISNOSTI OD VRSTE ISPITIVANJA
            </th>
        </tr>
        <tr>
            <td style="text-align: left">
                <strong>Prvo kontrolisanje instalacije hidrantske mreže obuhvata: </strong><br>
                1 ) utvrđivanje karakteristika koje instalacija hidrantske mreže treba da ima prema odobrenoj<br>
                tehničkoj dokumentaciji;<br>
                2) pregled izvedenog stanja i upoređivanje sa karakteristikama utvrđenim u prethodnoj tački;<br>
                3) pregled isprava o usaglašenosti elemenata instalacije hidrantske mreže;<br>
                4) pregled isprava o sprovedenim ispitivanjima prema posebnim propisima (npr. probe na<br>
                pritisak);<br>
                5) kontrolu stanja instalacije i stanja ispravnosti rada pojedinih elemenata sistema;<br>
                6) proveru ispravnosti međusobnih veza pojedinih elemenata instalacije (povezanost,<br>
                nepropusnost, prohodnost i dr.);<br>
                7) proveru ispravnosti glavnog i pomoćnog izvora napajanja uređaja za povećanje pritiska;<br>
                8) proveru ispravnosti rada elemenata instalacije koji su u sprezi sa drugim hidrauličnim<br>
                sistemima;<br>
                9) proveru elemenata sistema koji služe za kontrolu i upravljanje u radnom režimu uključujući i<br>
                razne blokade;<br>
                10) proveru oznaka, indikacija i signalizacije stanja instalacije uključujući i stanje kvara;<br>
                11) merenje radnih karakteristika (kapacitet, protok, pritisak i dr.);<br>
                12) proveru prateće vatrogasne opreme;<br>
                13) ocenu ispravnosti rada celokupne instalacije.<br>
                <strong>Periodično kontrolisanje instalacije hidrantske mreže obuhvata: </strong><br>
                1) pregled isprava o usaglašenosti elemenata instalacije hidrantske mreže koji su ugrađeni u postupku održavanja<br>
                instalacije;<br>
                2) pregled isprava o sprovedenim ispitivanjima prema posebnim propisima (npr. probe na pritisak) kada su u<br>
                postupku održavanja ugrađeni takvi elementi;<br>
                3) kontrolu stanja instalacije i stanja ispravnosti rada pojedinih elemenata sistema;<br>
                4) proveru ispravnosti međusobnih veza pojedinih elemenata instalacije (povezanost, nepropusnost, prohodnost i<br>
                dr.),<br>
                5) proveru ispravnosti glavnog i pomoćnog izvora napajanja uređaja za povećanje pritiska;<br>
                6) proveru ispravnosti rada elemenata instalacije koji su u sprezi sa drugim hidrauličnim sistemima;<br>
                7) proveru elemenata sistema koji služe za kontrolu i upravljanje u radnom režimu uključujući i razne blokade;<br>
                8) proveru oznaka, indikacija i signalizacije stanja instalacije uključujući i stanje kvara;<br>
                9) merenje radnih karakteristika (kapacitet, protok, pritisak i dr.);<br>
                10) proveru uticaja faktora spoljašnje sredine i načina korišćenja instalacije, kao i proveru prateće vatrogasne<br>
                opreme;<br>
                11) ocenu ispravnosti rada celokupne instalacije.<br>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <th style="text-align: left">
                E. REZULTATI PROVERE I ISPITIVANJA
            </th>
        </tr>
        <tr>
            <td height="200px">

            </td>
        </tr>
    </table>
    <table>
        <tr>
            <th>
                Red.<br>br.
            </th>
            <th>
                Naziv objekta<br>
                mesto i položaj
                <br><br><?php echo $izvestaj->getClient()['clientName'] ?>
                <br><?php echo $izvestaj->getClient()['streetAndNumber'] ?><br>
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
                Zadovoljava
            </th>
            <th>
                Nezadovoljava
            </th>
        </tr>
        <?php

        createTableRows($izvestaj->getDevices(), $izvestaj->getDevicesControls());

        ?>
    </table>
    <br>
    <table>
        <tr>
            <th style="text-align: left">
                Ž. UOČENA ODSTUPANJA KOD PRVOG ISPITIVANJA
            </th>
        </tr>
        <tr>
            <td height="50px">

            </td>
        </tr>
        <tr>
            <th style="text-align: left">
                Z. OCENA ISPRAVNOSTI
            </th>
        </tr>
        <tr>
            <td height="200px">
                <?php
                printOcenaIspravnosti($izvestaj->getClient()['id']);
                ?>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <th style="text-align: left">
                I. ZAPAŽANJA I NAPOMENE
            </th>
        </tr>
        <tr>
            <td height="150px">

            </td>
        </tr>
    </table>
    <table>
        <tr>
            <th>
                Redni<br>br.
            </th>
            <th>
                KONTROLISANJE IZVRŠILO<br>
                KOMPETENTNO LICE
            </th>
            <th>
                DATUM<br>
                KONTROLISANJA
            </th>
            <th>
                POTPIS KOMPETENTNOG LICA
            </th>
        </tr>
        <?php

        createLastTableRows($izvestaj->getOperators(), $profile);

        ?>
    </table>
    <table>
        <tr>
            <th>
                ODGOVORNO LICE U OVLAŠĆENOM PRAVNOM LICU
            </th>
            <th style="height: 20%">

            </th>
        </tr>
        <tr>
            <td height="50px">
                __________________________________________________
            </td>
            <td style="text-align: left">
                M.P.
            </td>
        </tr>
    </table>
    <br>
    <br>
    <table style="width: 90%" align="center">
        <tr>
            <td colspan="2">
                Zabranjeno preštampavanje i umnožavanje
            </td>
        </tr>
    </table>
    <table style="width: 90%; border: none" align="center">
        <tr>
            <td style="text-align: left; border: none">
                Izradio: Rukovodioc kvaliteta
            </td>
            <td style="text-align: right; border: none" height="10%">
                OBR-38/2
            </td>
        </tr>
        <tr style="border: none">
            <td style="text-align: left; border: none">
                Odobrio: Rukovodilac Kontrolnog tela
            </td>
        </tr>
    </table>
    </body>
    </html>

<?php

function createRows($operators, $profile) {
    $i = 1;
    foreach ($operators as $operator) {
        echo "<tr>";
        echo "<td>";
        echo ($i++);
        echo "</td>";
        echo "<td>";
        echo $operator['fullname'] . "<div id='box'>" . (($operator['username'] === $profile) ? "&#10003;" : "") . "</div>";
        echo "</td>";
        echo "<td>";
        echo $operator['licenceNumber'];
        echo "</td>";
        echo "</tr>";
    }
}

function createVrows($measuringDevices) {
    for ($i = 0; $i < count($measuringDevices); $i++) {
        echo "
        <tr>
            <th rowspan='5'>
                Merni<br>instrument<br>broj ". ($i + 1) . "
            </th>
            <td>
                Tip i vrsta
            </td>
            <td width='50%'>" . $measuringDevices[$i]['type'] . "</td>
        </tr>
        <tr>
            <td>
                Naziv proizvođača
            </td>
            <td width='50%'>
            " . $measuringDevices[$i]['manufacturer'] . "
            </td>
        </tr>
        <tr>
            <td>
                Fabrički broj
            </td>
            <td width='50%'>
            " . $measuringDevices[$i]['fabricID'] . "
            </td>
        </tr>
        <tr>
            <td>
                Klasa tačnosti
            </td>
            <td width='50%'>
            " . $measuringDevices[$i]['accuracyClass'] . "
            </td>
        </tr>
        <tr>
            <td>
                Uverenje o etaloniranju
            </td>
            <td width='50%'>
            " . $measuringDevices[$i]['calibrationTestimonial'] . "
            </td>
        </tr>
        ";
    }
}

/**
 * @param $object ClientObject
 */
function createDrows($objects) {
    $i = 0;
    foreach ($objects as $object) {
        echo "
        <tr style='border: solid black 1px'>
        <th rowspan=\"6\" width=\"20%\">
            Opis objekta<br>broj " . (++$i) . "
        </th>
        <td width=\"30%\" rowspan=\"2\">
            Adresa objekta:
        </td>
        <td>
            Ulica i broj
        </td>
        <td>
            Mesto
        </td>
    </tr>
    <tr>
        <td>
            " . $object->getStreetAndNumber() . "
        </td>
        <td>
            " . $object->getCity() . "
        </td>
    </tr>
    <tr>
        <td width=\"30%\">
            Naziv i namena
            objekta:
        </td>
        <td colspan=\"2\">
            " . $object->getObjectName() . "
        </td>
    </tr>
    <tr>
        <td rowspan=\"2\">
            Najviša visinska
            kota [m]
        </td>
        <td colspan=\"2\" style=\"text-align: left\">
            Spratnost:         " . ((int)$object->getFloorsAboveGround() + (int)$object->getFloorsUnderground()) . "
        </td>
    </tr>
    <tr>
        <td>
            Br. podzemnih etaža
        </td>
        <td>
            Br. nadzemnih etaža
        </td>
    </tr>
    <tr>
        <td>
            " . $object->getHighestObjectAltitude() . "
        </td>
        <td>
            " . (int)$object->getFloorsUnderground() . "
        </td>
        <td>
            " . (int)$object->getFloorsAboveGround() . "
        </td>
    </tr>
    ";
    }
}

function printDataOfHNByGivenDevices($devices) {
    $underground = 0;
    $aboveGround = 0;
    $wallHydrants = 0;

    foreach ($devices as $device) {
        if ($device->getDeviceDictionaryValue('type') === 'Hydrants') {
            if ($device->getHidrantDictionaryValue('subType') === 'Podzemni')
                $underground++;
            else if ($device->getHidrantDictionaryValue('subType') === 'Nadzemni')
                $aboveGround++;
            else if ($device->getHidrantDictionaryValue('subType') === 'Zidni')
                $wallHydrants++;
        }
    }

    echo "
    - Spoljna hidrantska mreža : <br>
                podzemnih komada: " . $underground . "<br>
                nadzemnih komada: " . $aboveGround . "<br>
                - Unutrašnja hidranska mreža :<br>
                zidnih hidranata komada: " . $wallHydrants . "<br>
                - Napajanje pumpnog postrojenja : KPK , DEA
    ";
}

function createTableRows($devices, $devicesControls) {
    for ($i = 0; $i < count($devices); $i++) {
        $inOrder = "";
        $outOfOrder = "";

        echo "<tr>";
        echo "<td>";
        echo $i + 1;
        echo "</td>";

        echo "<td>";
        echo getObjectNameOfDevice($devices[$i]->getId()) . ", " . $devicesControls[$i]['locationPP'];
        echo "</td>";

        echo "<td>";
        echo $devices[$i]->getHidrantDictionaryValue('hMark');
        echo "</td>";

        echo "<td>";
        echo $devices[$i]->getHidrantDictionaryValue('subType');
        echo "</td>";

        echo "<td>";
        echo $devicesControls[$i]['staticPressure'];
        echo "</td>";

        echo "<td>";
        echo $devicesControls[$i]['dynamicPressure'];
        echo "</td>";

        echo "<td>";
        echo $devicesControls[$i]['m3hNetwork'];
        echo "</td>";

        echo "<td>";
        echo "</td>";

        echo "<td>";
        echo $devicesControls[$i]['note'];
        echo "</td>";

        if ($devicesControls[$i]['curState'] === 'in order')
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

function printOcenaIspravnosti($clientID) {
    $network = getNetworkData($clientID);

    if ($network['networkPressureState'] === 'satisfies') {
        echo "Pritisak i protok vode u hidrantskoj mreži  dati su tabelarno i zadovoljavaju propisane standarde i norme iz oblasti zaštite od požara.";
    } else if ($network['networkPressureState'] === 'dissatisfies') {
        echo "Pritisak i protok vode u hidrantskoj mreži  dati su tabelarno i ne zadovoljavaju propisane standarde i norme iz oblasti zaštite od požara.";
    } else {
        echo "Pritisak i protok vode u hidrantskoj mrezi nisu zavedeni u bazi podataka.";
    }

}

function getNetworkData($clientID) {
    $sql = "SELECT `noteNetworkData`, `networkPressureState` FROM `mrezniprotok` WHERE objectID IN (SELECT id FROM objects WHERE clientID=$clientID)";

    $result = DataBase::selectionQuery($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row;
    }
}

function getObjectNameOfDevice($deviceID) {
    $sql = "SELECT objectName FROM objects WHERE id = (SELECT objectID FROM sviuredjaji WHERE id=$deviceID)";
    $result = DataBase::selectionQuery($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['objectName'];
    }
}

function createLastTableRows($operators, $profile) {
    $i = 1;
    if (SessionUtilities::checkIsSessionSet('TimeFilter')) {
        $fromDate = date("d.m.Y", (int)((int)(explode(":", SessionUtilities::getSession('TimeFilter'))[0]) / 1000));
        $toDate = date("d.m.Y", (int)((int)(explode(":", SessionUtilities::getSession('TimeFilter'))[1]) / 1000));
    }
    foreach ($operators as $operator) {
        echo "<tr>";
        echo "<td>";
        echo ($i++);
        echo "</td>";

        echo "<td>";
        echo $operator['fullname'] . "<div id='box'>" . (($operator['username'] === $profile) ? "&#10003;" : "") . "</div>";
        echo "</td>";

        echo "<td>";
        if (SessionUtilities::checkIsSessionSet('TimeFilter')) {
            $fromDate = date("d.m.Y", (int)((int)(explode(":", SessionUtilities::getSession('TimeFilter'))[0]) / 1000));
            $toDate = date("d.m.Y", (int)((int)(explode(":", SessionUtilities::getSession('TimeFilter'))[1]) / 1000));
            echo $fromDate . " - " . $toDate;
        }
        echo "</td>";
        echo "<td>";
        echo "</td>";
        echo "</tr>";
    }
}

?>