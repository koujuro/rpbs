<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../../header/header.php";

if (SessionUtilities::checkIsSessionSet('User')) {
    $company = new Company();
    $fileError = "";

    if (isset($_POST['saveCompanyInfo'])) {
        $uploadImage = new UploadImage();
        if (isset($_FILES['logo-img']['name'])) {
            $uploadImage->setParameters($_FILES['logo-img']['name'], $_FILES['logo-img']['tmp_name'], $_FILES['logo-img']['size']);
            if ($uploadImage->checkParameters() === "") {
                if ($uploadImage->uploadFile()) {
                    $company->updateCompanyInDB($_POST, $uploadImage->getFileName());
                }
            } else
                $fileError = $uploadImage->checkParameters();
        } else {
            $company->updateCompanyInDB($_POST, "");
        }
    }

    $company->setParametersFromDB($company->fetchCompanyFromDB());
}

/**
 * @param $company Company
 */
function printCompanyInputs($company, $fileError) {
    echo "
                <div>
                    <div class='form-group form-img-group'>
                        <label for=''>Logo:</label>


                        <div class='logo-fake-label'>upload image <label for='logo-img'>choose</label></div>
                        <input type='file' id='logo-img' name='logo-img'>
                    </div>
                    <div style='text-align: right; color: red'>$fileError</div>


                    <div class='form-group'>
                        <label for=''>Naziv firme:</label>
                        <input type='text' id='companyName' name='companyName' placeholder='" . $company->getCompanyName() . "'>
                    </div>

                    <div class='form-group'>
                        <label for=''>Ulica:</label>
                        <input type='text' id='street' name='street' placeholder='" . $company->getStreet() . "'>
                    </div>

                    <div class='form-group'>
                        <label for=''>Broj:</label>
                        <input type='text' id='number' name='number' placeholder='" . $company->getNumber() . "'>
                    </div>

                    <div class='form-group'>
                        <label for=''>Mesto:</label>
                        <input type='text' id='city' name='city' placeholder='" . $company->getCity() . "'>
                    </div>

                    <div class='form-group'>
                        <label for=''>Postanski broj:</label>
                        <input type='text' id='PAC' name='PAC' placeholder='" . $company->getPAC() . "'>
                    </div>

                    <div class='form-group'>
                        <label for=''>Telefon:</label>
                        <input type='text' id='phoneNumber' name='phoneNumber' placeholder='" . $company->getPhoneNumber() . "'>
                    </div>


                    <div class='form-group'>
                        <label for=''>Sajt:</label>
                        <input type='text' id='webSite' name='webSite' placeholder='" . $company->getWebSite() . "'>
                    </div>
                </div>
                <div>

                    <div class='form-group'>
                        <label for=''>E-mail:</label>
                        <input type='email' id='eMail' name='eMail' placeholder='" . $company->getEMail() . "'>
                    </div>

                    <div class='form-group'>
                        <label for=''>Telefon sluzbe:</label>
                        <input type='text' id='officePhoneNumber' name='officePhoneNumber' placeholder='" . $company->getOfficePhoneNumber() . "'>
                    </div>

                    <div class='form-group'>
                        <label for=''>Telefon servisa:</label>
                        <input type='text' id='servicePhoneNumber' name='servicePhoneNumber' placeholder='" . $company->getServicePhoneNumber() . "'>
                    </div>

                    <div class='form-group'>
                        <label for=''>Mob. tel.:</label>
                        <input type='text' id='mobilePhoneNumber' name='mobilePhoneNumber' placeholder='" . $company->getMobilePhoneNumber() . "'>
                    </div>

                    <div class='form-group'>
                        <label for=''>Broj potvrde ATS:</label>
                        <input type='text' id='numberATS' name='numberATS' placeholder='" . $company->getNumberATS() . "'>
                    </div>

                    <div class='form-group last-group'>
                        <label for=''>Broj resenja o utvrdjivanju ispunjenosti uslova za obavljanje poslova kontrolisanja:</label>
                        <input type='text' id='controlLicenceNumber' name='controlLicenceNumber' placeholder='" . $company->getControlLicenceNumber() . "'>
                    </div>

                </div>
    ";
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PBS - Admin panel</title>

    <!-- Styles -->
    <?=HTMLUtilities::ImportLinks('admin-panel.css')?>

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

    <div class="container">


        <div class="admin-panel">

            <h1 class="panel-heading">Naziv firme: <span>Ime firme</span></h1>


            <form action="admin_panel.php" method="post" enctype="multipart/form-data">

                <?php
                printCompanyInputs($company, $fileError);
                ?>

                <div class="button-wrapper">
                    <input type="submit" class="save" id='saveCompanyInfo' name='saveCompanyInfo' value="Sacuvaj">
                </div>

            </form>


        </div>


    </div>

</section>

</body>
</html>
