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

/** @var OBR_38_2 $izvestaj */
$izvestaj = ReportsContext::getInstance()->pickReport('obr-r08-35-1');
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
                <!--                Br.potvrde ATS : --><?php //echo $izvestaj->getCompany()['numberATS']?>
            </div>
        </td>
    </tr>
</table>
<br><br>
<div style="width: 100%; text-align: left">
    <strong>Korisnik:</strong>
</div>
<div id="title">
    <strong>ISPRAVA O KONTROLISANJU MOBILNIH UREĐAJA ZA GAŠENJE<br>POŽARA
        <br><br><br><br>

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
        <td width="35%"><?php echo $_GET['reportGenerationDate'] ?></td>
    </tr>
    <tr>
        <th colspan="4" style="text-align: left">
            A. PODACI O PRAVNOM LICU OVLAŠĆENOM ZA OBAVLJANJE POSLOVA<br>
            KONTROLISANJA MOBILNIH UREĐAJA ZA GAŠENJE POŽARA
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
<div id="title" style="text-align: left">
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
<div id="title" style="text-align: left">
    V. PODACI O UPOTREBLJENOJ OPREMI I MERNIM INSTRUMENTIMA
</div>
<table>
    <?php

    createVrows($izvestaj->getMeasuringDevices());

    ?>
</table>
<div id="title" style="text-align: left">
    G. SPISAK PROPISA NA OSNOVU KOJIH JE IZVRŠENO KONTROLISANJE<br>
    MOBILNIH UREĐAJA ZA GAŠENJE POŽARA
</div>
<table>
    <tr>
        <th width="8%">1.</th>
        <td style="text-align: left">
            Zakon o  zaštiti od požara ("Sl. glasnik RS" br 111/2009 i br.20/2015)
        </td>
    </tr>
    <tr>
        <th width="8%">2.</th>
        <td style="text-align: left">
            Članovi 5,21,22,23,24,25 pravilnika o posebnim uslovima koje moraju ispunjavati pravna lica koja dobijaju
            ovlašćenje za obavljanje poslova kontrolisanja instalacija i uređaja za gašenje požara i instalacija
            posebnih sistema (’’Sl glasnik RS’’ br. 52/15)
        </td>
    </tr>
    <tr>
        <th width="8%">3.</th>
        <td style="text-align: left">
            Pravilnik o tehničkim i drugim zahtevima za ručne i prevozne aparate za gašenje požara
            Službeni glasnik Republike Srbije".Broj 01-9623/09-4od  28. avgusta 2009. godine
        </td>
    </tr>
</table>
<br><br>

<table>
    <tr>
        <th style="text-align: left">
            D.VRSTA ISPITIVANJA I REZULTATI
        </th>
    </tr>
    <tr>
        <td style="text-align: left; font-weight: normal">
            <br>
            <u><strong>VRSTA ISPITIVANJA:</strong> Periodično ispitivanje uređaja za gašenje požara<br>
                <strong>REZULTATI:</strong>  mobilni uređaji za gašenje požara su  ispravni</u><br><br>
            <u><strong>Periodično ispitivanje mobilnih uređaja za gašenje požara pod stalnim pritiskom
                    obuhvata:</u> &#10003;</strong><br>
            1) utvrđivanje činjenice da li je istekao rok za kontrolno ispitivanje uređaja;<br>
            2) vizuelni pregled opšteg stanja uređaja u odnosu na koroziju i fizička oštećenja;<br>
            3) vizuelni pregled stanja i kompletnosti svih delova uređaja;<br>
            4) vizuelni pregled natpisa i uputstva za korišćenje uređaja;<br>
            5) kontrolu radnog pritiska u aparatu (osim CO2 aparata);<br>
            6) kontrolu ispravnosti manometra;<br>
            7) proveru osigurača i plombe;<br>
            8) vizuelni pregled stanja spojne cevi i mlaznice;<br>
            9) merenje mase sredstva za gašenje;<br>
            10) proveru stanja praha u pogledu rastresitosti, pojave grudvica i stranih tela kod mobilnih uređaja za gašenje
            požara sa prahom.<br>
            <u><strong>Periodično ispitivanje mobilnih uređaja za gašenje požara koji sadrži bočicu sa pogonskim gasom obuhvata:</u> &#10003;</strong><br>
            1) utvrđivanje činjenice da li je istekao rok za kontrolno ispitivanje uređaja;<br>
            2) vizuelni pregled opšteg stanja uređaja u odnosu na koroziju i fizička oštećenja;<br>
            3) vizuelni pregled stanja i kompletnosti svih delova uređaja;<br>
            4) vizuelni pregled natpisa i uputstva za korišćenje uređaja;<br>
            5) proveru osigurača i plombe;<br>
            6) vizuelni pregled stanja spojne cevi i mlaznice;<br>
            7) proveru stanja sredstva za gašenje u pogledu rastresitosti, pojave grudvica i stranih tela kod mobilnih uređaja za
            gašenje požara;<br>
            8) proveru rada mehanizma za aktiviranje i svih zaptivki;<br>
            9) proveru potisne i pobudne cevi;<br>
            10) proveru mase pogonskog gasa u bočici.<br>
            <u><strong>Kontrolno ispitivanje mobilnih uređaja za gašenje požara, pored radnji predviđenih periodičnim
                    pregledom, obuhvata i:</u> </strong><br>
            1) hidraulično ispitivanje čvrstoće i nepropusnosti suda aparata;<br>
            2) proveru pritiska otvaranja ventila sigurnosti osim kod rasprskavajućih membrana.<br>
            <br>
        </td>
    </tr>
</table>
<br>
<table>
    <tr>
        <td style="text-align: left" colspan="4">KORISNIK - OPIS OBJEKTA</td>
    </tr>
    <?php

    if ($_GET['numOfObjects'] === 'single') {
        $objects []= $izvestaj->getObject();
        createDrows($objects);
    }
    else
        createDrows($izvestaj->getObjects());

    ?>
</table>
<br>
<table>
    <tr>
        <th rowspan="2">
            Red.<br>br.
        </th>
        <th rowspan="2">
            MESTO I POLOZAJ
        </th>
        <th rowspan="2">
            NAPOMENA
        </th>
        <th rowspan="2">
            TIP
        </th>
        <th rowspan="2">
            PODACI O<br>PROIZVOĐAČU
        </th>
        <th rowspan="2">
            FABR. BROJ /<br>GODIŠTE
        </th>
        <th colspan="2">
            ISPRAVNO
        </th>
    </tr>
    <tr>
        <th>
            DA
        </th>
        <th>
            NE
        </th>
    </tr>
    <?php

    createTableRows($izvestaj->getDevices());

    ?>
</table>
<br>
<table>
    <tr>
        <th style="text-align: left">
            E. ZAPAŽANJA I NAPOMENE
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
            OBR R08-35/1
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
    foreach ($objects as $object) {
        echo "
        <tr>
        
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
            Spratnost:
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

function createTableRows($devices) {
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
        echo $devices[$i]['note'];
        echo "</td>";

        echo "<td>";
        echo $devices[$i]['subType'];
        echo "</td>";

        echo "<td>";
        echo $devices[$i]['manufacturerData'];
        echo "</td>";

        echo "<td>";
        echo $devices[$i]['fabricId'] . "/" . $devices[$i]['creationYear'];
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

function createLastTableRows($operators, $profile) {
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
        if ($operator['username'] === $profile) {
            echo date("d.m.Y");
        }
        echo "</td>";
        echo "<td>";
        echo "</td>";
        echo "</tr>";
    }
}

?>
