<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../../header/header.php";

if (SessionUtilities::checkIsSessionSet('User')) {
    $fileError = "";
    if (isset($_POST['saveCompanyInfo'])) {
        $uploadImage = new UploadImage();
        $uploadImage->setParameters($_FILES['logo-img']['name'], $_FILES['logo-img']['tmp_name'], $_FILES['logo-img']['size']);
        if ($uploadImage->checkParameters() === "") {
            if ($uploadImage->uploadFile()) {
                $newCompany = new Company();
                $newCompany->setParametersFromPOST($_POST, $uploadImage->getFileName());
                $newCompany->insertNewCompanyIntoDB();
                $newUser = new AdminUser();
                $newUser->setParametersFromPOST($_POST, $newCompany->getId());
                $newUser->insertNewUserIntoDB();
            }
        } else {
            $fileError = $uploadImage->checkParameters();
        }
    }
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
    <title>PBS</title>

    <!-- Styles -->
    <link href="../admin_panel/admin-panel.css" rel="stylesheet">
    <link href="../../css/common.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto|Montserrat|Open+Sans" rel="stylesheet">

    <!-- Scripts -->
    <script src="js/sa_scripts.js"></script>
</head>
<body onload="showAccountName()">

<section id="wrapper">

    <?php
    require_once "../../header/nav_menu.php";
    ?>

    <div class="container">

        <div class="admin-panel">

            <h1 class="panel-heading">Unesite podatke za novu firmu:</h1>

            <form action="creating_company.php" method="post" enctype="multipart/form-data">
                <div>
                    <div class='form-group form-img-group'>
                        <label for=''>Logo:</label>

                        <div class='logo-fake-label'>upload image <label for='logo-img'>choose</label></div>
                        <input type='file' id='logo-img' name='logo-img'>
                    </div>
                    <div style='text-align: right; color: red'><?php echo $fileError; ?></div>

                    <div class='form-group'>
                        <label for=''>Naziv firme:</label>
                        <input type='text' id='companyName' name='companyName'>
                    </div>

                    <div class='form-group'>
                        <label for=''>Jedinstveni broj firme:</label>
                        <input type='text' id='uniqueNumber' name='uniqueNumber'>
                    </div>

                    <div class='form-group'>
                        <label for=''>Ulica:</label>
                        <input type='text' id='street' name='street'>
                    </div>

                    <div class='form-group'>
                        <label for=''>Broj:</label>
                        <input type='text' id='number' name='number'>
                    </div>

                    <div class='form-group'>
                        <label for=''>Mesto:</label>
                        <input type='text' id='city' name='city'>
                    </div>

                    <div class='form-group'>
                        <label for=''>Postanski broj:</label>
                        <input type='text' id='PAC' name='PAC'>
                    </div>

                    <div class='form-group'>
                        <label for=''>Telefon:</label>
                        <input type='text' id='phoneNumber' name='phoneNumber'>
                    </div>


                    <div class='form-group'>
                        <label for=''>Sajt:</label>
                        <input type='text' id='webSite' name='webSite'>
                    </div>
                </div>
                <div>
                    <div class='form-group'>
                        <label for=''>E-mail:</label>
                        <input type='email' id='eMail' name='eMail'>
                    </div>

                    <div class='form-group'>
                        <label for=''>Telefon sluzbe:</label>
                        <input type='text' id='officePhoneNumber' name='officePhoneNumber'>
                    </div>

                    <div class='form-group'>
                        <label for=''>Telefon servisa:</label>
                        <input type='text' id='servicePhoneNumber' name='servicePhoneNumber'>
                    </div>

                    <div class='form-group'>
                        <label for=''>Mob. tel.:</label>
                        <input type='text' id='mobilePhoneNumber' name='mobilePhoneNumber'>
                    </div>

                    <div class='form-group'>
                        <label for=''>Broj potvrde ATS:</label>
                        <input type='text' id='numberATS' name='numberATS'>
                    </div>

                    <div class='form-group last-group'>
                        <label for=''>Broj resenja o utvrdjivanju ispunjenosti uslova za obavljanje poslova kontrolisanja:</label>
                        <input type='text' id='controlLicenceNumber' name='controlLicenceNumber'>
                    </div>

                    <div class='form-group'>
                        <label for=''>Broj dozvoljenih barkodova:</label>
                        <input type='text' id='numAllowedBarcodes' name='numAllowedBarcodes'>
                    </div>
                </div>

                <h1 class="panel-heading">Unesite podatke za Admina firme:</h1>
                <br>
                <div>
                    <div class="form-group">
                        <label for="">Ime</label>
                        <input type="text" id="newUserFirstName" name="newUserFirstName">
                    </div>

                    <div class="form-group">
                        <label for="">Prezime</label>
                        <input type="text" id="newUserLastName" name="newUserLastName">
                    </div>

                    <div class="form-group">
                        <label for="">Broj licence</label>
                        <input type="text" id="newUserLicenceNumber" name="newUserLicenceNumber">
                    </div>

                    <div class="form-group">
                        <label for="">Username</label>
                        <input type="text" id="newUserUsername" name="newUserUsername">
                    </div>

                    <div class="form-group">
                        <label for="">Lozinka</label>
                        <input type="password" id="newUserPassword" name="newUserPassword">
                    </div>

                    <div class="form-group">
                        <label for="">Ponovi lozinku</label>
                        <input type="password" id="newUserRepeatPassword" name="newUserRepeatPassword">
                    </div>
                </div>

                <div class="button-wrapper">
                    <input type="submit" class="save" id='saveCompanyInfo' name='saveCompanyInfo' value="Sacuvaj">
                </div>

            </form>
        </div>

    </div>

</section>

</body>
</html>
