<?php

require_once __DIR__ . "/../../header/header.php";

if (SessionUtilities::checkIsSessionSet('User')) {
    header("location: pages/dashboard/dashboard.php");
    die();
}

?>

<script src='handlers/log_in/login_ajax.js'></script>


<div id="loginForm">
    <input class="loginInput" type="text" placeholder="Korisničko ime:" autocomplete="off" name='userNameLogin' id='userNameLogin'>
    <input class="loginInput" type="password" placeholder="Lozinka:" autocomplete="off" name='passwordLogin' id='passwordLogin'>
    <button class="loginSubmit" onclick='sendDataForLogin("userNameLogin;passwordLogin")'>Uloguj se</button>
</div>


