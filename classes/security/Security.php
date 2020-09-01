<?php

/**
 * The trait RFLSecurity will deal with all security issues on the site.
 */
trait Security {
    /**
     * Password token will be added to user's password for better security in our data base.
     */
    public static $passwordToken = "}6RY(Aqz";

    /**
     * The function will hash password with md5 hash method.
     *
     * @param $password
     */
    private function cryptPassword(&$password) {
        $password = md5($password . self::$passwordToken);
    }

    private function cryptPasswordWithLibsodium($password) {
        $secret_key = RFLUtility::getSecretKey();
        $nonce = RFLUtility::getNonce();
        return sodium_crypto_secretbox($password, $nonce, $secret_key);
    }

    public static function decryptPasswordWithLibsodium($password) {
        $secret_key = RFLUtility::getSecretKey();
        $nonce = RFLUtility::getNonce();
        return sodium_crypto_secretbox_open($password, $nonce, $secret_key);
    }

    /**
     * The function will check if names are correct inputted.
     *
     * @param $name
     * @return false|int
     */
    private function correctNames($name) {
        $this->securityConversion($name);
        return preg_match("/^[\p{L}:,.!#\"\'%()-=+_\n ]+$/u", $name);
    }

    private function correctUsername($name) {
        $this->securityConversion($name);
        return preg_match("/^[\p{L}:,.!#\"\'%()-=+_\n ]+$/u", $name);
    }

    /**
     * The function will check if emails are correct inputted.
     *
     * @param $email
     * @return mixed
     */
    private function correctEmail($email) {
        $this->securityConversion($email);
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    //TODO: PROMENITI NA minimum 8 karaktera!!!
    /**
     * The function will check if passwords are correct inputted.
     *
     * @param $password
     * @return false|int
     */
    private function correctPasswords($password) {
        $this->securityConversion($password);
        return preg_match("/^[0-9A-Za-z!@#$%]{2,32}$/", $password);
    }

    /**
     * The function will check if messages are correct inputted.
     *
     * @param $message
     * @return false|int
     */
    private function correctMessage($message) {
        $this->securityConversion($message);
        if($message === "") return true;
        return preg_match("/^[\p{L}:,.!#\"\'%()-=+?\n ]+$/u", $message);
    }

    private function correctCaptionText($message) {
        $this->securityConversion($message);
        if($message === "") return true;
        return preg_match("/^[\p{L}:,.!#\"\'%()-=+\n ]+$/u", $message);
    }

    /**
     * Call security conversions on parameter.
     *
     * @param $parameter - data for security conversion
     */
    private function securityConversion(&$parameter) {
        $parameter = $this->securityParseData($parameter);
    }

    /**
     * The function will process passed data.
     * trim - This function returns a string with whitespace stripped from the beginning and end of string.
     * stripslashes - Un-quotes a quoted string.
     * htmlspecialchars - Certain characters have special significance in HTML, and should be represented by HTML entities
     * if they are to preserve their meanings. This function returns a string with these conversions made.
     *
     * @param $data
     * @return string
     */
    private function securityParseData($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    /**
     * The function will create confirmation code, hash him and return to caller.
     *
     * @return string
     */
    private function createConfirmationCode() {
        $randomNumber1 = rand(23456789, 98765432);
        $randomNumber2 = rand(23456789, 98765432);
        return md5($randomNumber1.$randomNumber2);
    }
}