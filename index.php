<?php
    require_once 'header/header.php'
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
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="pages/login_page/landing.css">
    <link rel="stylesheet" href="pages/login_page/login_page.css">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto|Montserrat:400,700,800|Open+Sans" rel="stylesheet">
</head>
<body>

    <main id="main">
        <section id="info">
            <h1>P.B.S.</h1>
            <p class="main-text">PBS je skup usluga koje omogućavaju kreiranje elektronske baze podataka sa svim relevantnim podacima koji se odnose na protivpožarne mobilne uređaje i hidrante. Jednom kreirana baza omogućava nam brojne prednosti u poslovima kontrole i održavanja protivpožarne opreme i uređaja. Osnovni princip funkcionisanja servisa podrazumeva korišćenje mobilne aplikacije za rad na terenu, WEB aplikacije za pregled i ažuriranje podataka, a sistem se bazira na identifikaciji i markiranju svih pojedinačnih uređaja sa BAR kod nalepnicama.</p>

            <h3>Imate pitanja? Kontaktirajte nas.</h3>

            <form action="index.php" method="post">
                <p>
                    <input type="email" id="email" name="email" placeholder="Vaša email adresa">
                </p>
                <p>
                    <input type="text" id="phoneNumber" name="phoneNumber" placeholder="Br. Mob.">
                </p>
                <p>
                    <textarea id="emailBody" name="emailBody" placeholder="Vaša pitanja"></textarea>
                </p>
                <p>
                    <input type="submit" id="submitContactForm" name="submitContactForm" value="Pošalji">
                </p>
            </form>

            <div id="scroller">
                <img src="assets/img/arrow_down_blue.png" alt="">
                <img src="assets/img/arrow_down_blue.png" alt="">
                <img src="assets/img/arrow_down_blue.png" alt="">
            </div>
        </section>

        <section id="login">
            <?php
                require_once 'pages/login_page/login_page.php';
            ?>
        </section>

    </main>

</body>
</html>
