<?php

abstract class SessionUtilities {
    /**
     * Create session for certain serialized data.
     *
     * @param $name
     * @param $data
     */
    public static function createSession($name, $data) {
        $_SESSION[$name] = serialize($data);
    }

    /**
     * return unserialized session.
     *
     * @param $name
     * @return mixed
     */
    public static function getSession($name) {
        return unserialize($_SESSION["$name"]);
    }

    /**
     * Check if session with certain name is created.
     *
     * @param $name
     * @return bool
     */
    public static function checkIsSessionSet($name) {
        return isset($_SESSION["$name"]) ? true : false;
    }


    /**
     * Unset certain session.
     *
     * @param $name
     */
    public static function unsetSession($name) {
        unset($_SESSION["$name"]);
    }

    /**
     * Unset all sessions that are created on site.
     */
    public static function unsetAllSessionOnSite() {
        session_unset();
        session_destroy();
    }
}