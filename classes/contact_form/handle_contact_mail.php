<?php

require_once '../header/header.php';
require 'PHPMailer-master/PHPMailerAutoload.php';

if (isset($_POST['submitContactForm'])) {
    $mailFrom = "cobalt.storm722@gmail.com";
    $mailSub = "PBS - Pitanja kontakt forme";
    $mailMsg = $_POST['emailBody'] . "/n/nEmail: " . $_POST['email'] . "/n/nBr. Mob.:" . $_POST['phoneNumber'];

    $mail->SMTPDebug = 2;
    $mail->isSMTP();
    $mail->Host = 'localhost.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'root';
    $mail->Password = 'root';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 25;

    //502478259104-oekq3qftau7j5t1ld38srnk14jrgkpps.apps.googleusercontent.com
    //BLHkhjRnriy_Rk9srhG6ZiLv
    $mail = new PHPMailer();
    $mail->setFrom($mailFrom, 'User');
    $mail->addAddress($this->emailToSend);
    $mail->addReplyTo('cobalt.storm722@gmail.com', 'PBS');
    $mail->addCC('cobalt.storm722@gmail.com');

    //Content
    $mail->isHTML(true);
    $mail->Subject = $this->subject;
    $mail->Body    = $this->body;

    if(!$mail->send()) {
        echo "Mail Not Sent";
    }
    else
    {
        echo "Mail Sent";
    }
} else {
    header("index.php");
    die();
}

?>