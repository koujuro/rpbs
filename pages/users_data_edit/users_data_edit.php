<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../../header/header.php";


if (SessionUtilities::checkIsSessionSet('User')) {
    if (isset($_POST['newUserSubmit'])) {
        $fullName = $_POST['newUserFirstName'] . " " . $_POST['newUserLastName'];
        $licenceNumber = $_POST['newUserLicenceNumber'];
        $username = $_POST['newUserUsername'];
        $password = $_POST['newUserPassword'];
        $userType = $_POST['newUserType'];
        $currentUser = SessionUtilities::getSession('User');
        $sql = "SELECT companyID FROM users WHERE username='$currentUser'";

        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $companyID = $row['companyID'];
            $sql = "INSERT INTO users (`companyID`, `fullname`, `username`, `password`, `userType`, `millisCreated`, `licenceNumber`, `allowAccess`) 
                    VALUES ($companyID, '" . $fullName . "', '" . $username . "', '" . $password . "', '" . $userType . "', '" . time() * 1000 . "', '" . $licenceNumber . "', 1)";

            DataBase::executionQuery($sql);
        }
    }
    if (isset($_POST['editUserSubmit']) && ($_POST['editUserId'] !== "")) {
        $userId = $_POST['editUserId'];
        $fullName = $_POST['editFirstName'] . " " . $_POST['editLastName'];
        $licenceNumber = $_POST['editLicenceNumber'];
        $username = $_POST['editUsername'];
        $password = $_POST['editPassword'];
        $userType = $_POST['editUserType'];
        $sql = "UPDATE users SET username='$username', password='$password', fullName='$fullName', licenceNumber='$licenceNumber', userType='$userType' WHERE id=$userId";

        DataBase::executionQuery($sql);
    }
    if (isset($_POST['deleteUserSubmit']) && ($_POST['editUserId'] !== "")) {
        $userId = $_POST['editUserId'];
        $sql = "UPDATE users SET allowAccess=0 WHERE id=$userId";

        DataBase::executionQuery($sql);
    }
    $users = fetchUsersFromDB();
} else {
    header("location: ../../index.php");
}

function fetchUsersFromDB() {
    $users = [];
    $currentUser = SessionUtilities::getSession('User');
    $sql = "SELECT id, username
            FROM users
            WHERE allowAccess=1 AND companyID IN (SELECT companyID
                                FROM users
                                WHERE username='$currentUser')";

    $result = DataBase::selectionQuery($sql);
    if ($result->num_rows > 0)
        while ($row = $result->fetch_assoc())
            $users []= $row;

    return $users;
}

function printUsers($users) {
    foreach ($users as $user) {
        echo "<div class=\"type filter-option\" id=\"" . $user['id'] . "\" onclick=\"filterOptionsClick(this);sendChosenUser(this.id)\" data-type-id=\"".$user['id']."\" data-search-term=\"" . $user['username'] . "\"> <span class=\"option-id\">".$user['id']."</span> ".$user['username']."</div>";
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
    <title>PBS - Podaci o korisnicima</title>

    <!-- Styles -->
    <?=HTMLUtilities::ImportLinks("../../css/shared/measuring-devices.css", "users_data_edit.css");?>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto|Montserrat|Open+Sans" rel="stylesheet">

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
    <script src="../../js/jq_functions.js"></script>
    <script src="../../js/scripts.js"></script>
    <script src="../../handlers/users_data_edit/users_data_edit_ajax.js"></script>

</head>
<body onload="showAccountName()">

<section id="wrapper">

    <?php
    require_once "../../header/nav_menu.php";
    ?>

    <div class="container">

        <div class="devices-grid operators">

            <div class="new-device-entry">
                <h1 class="panel-heading">Unos operatera</h1>

                <form class="devices-form" action="users_data_edit.php" method="post">
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

                    <div class="form-group">
                        <label for="">Tip operatera</label>
                        <input type="radio" name="newUserType" value="controller" checked>Kontrolor<br>
                        <input type="radio" name="newUserType" value="servicer">Serviser<br>
                    </div>


                    <div class="form-group submit">
                        <input type="submit" id="newUserSubmit" name="newUserSubmit" value="Unesi">
                    </div>
                </form>
            </div>


            <div class="change-device">
                <h1 class="panel-heading">Izmene podataka o operateru</h1>

                <div class="panel-body">
                    <div class="types-page">
                        <h3 class="filter-heading">Operater</h3>

                        <div class="filter-body">
                            <div class="filter-search" name="init">
                                <input type="text" class="search" placeholder="Pretraga">
                                <button><img src="../../assets/img/search-icon.png" alt=""></button>
                            </div>

                            <?php
                            printUsers($users);
                            ?>
                        </div>

                    </div>

                    <form name="editUserForm" action='users_data_edit.php' method='post' class="devices-form">
                        <input type="hidden" id="editUserId" name="editUserId" value="4">
                        <div class="form-group">
                            <label for="">Ime</label>
                            <input type="text" id='editFirstName' name='editFirstName'>
                        </div>

                        <div class="form-group">
                            <label for="">Prezime</label>
                            <input type="text" id='editLastName' name='editLastName'>
                        </div>

                        <div class="form-group">
                            <label for="">Broj licence</label>
                            <input type="text" id='editLicenceNumber' name='editLicenceNumber'>
                        </div>

                        <div class="form-group">
                            <label for="">Username</label>
                            <input type="text" id='editUsername' name='editUsername'>
                        </div>

                        <div class="form-group">
                            <label for="">Lozinka</label>
                            <input type="password" id='editPassword' name='editPassword'>
                        </div>

                        <div class="form-group">
                            <label for="">Ponovi lozinku</label>
                            <input type="password" id='editRepeatPassword' name='editRepeatPassword'>
                        </div>

                        <div class="form-group">
                            <label for="">Tip operatera</label>
                            <input type="radio" name="editUserType" value="controller" checked>Kontrolor<br>
                            <input type="radio" name="editUserType" value="servicer">Serviser<br>
                        </div>


                        <div class="form-group submit">
                            <input type="submit" name="deleteUserSubmit" class="del" value="Obrisi">
                            <input type="submit" name="editUserSubmit" value="Sacuvaj">
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

        option.classList.add('active');

        // Menjanje klasa




        // Simuliranje inputa

        // let deviceId = parseInt(option.getAttribute('data-type-id')),
        //     deviceDropdown = document.querySelector('#deviceDropdown'),
        //     event = new Event('change');
        //
        // deviceDropdown.value = deviceId;
        // deviceDropdown.dispatchEvent(event);

        // Simuliranje inputa
    }
</script>

</body>
</html>


