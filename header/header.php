<?php

/**
 * This file should be included inside every php page.
 * It helps to import all constants and classes that
 * are needed for further application flow.
 */

/**
 * Enables showing all errors and warnings.
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: text/html; charset=utf-8');

date_default_timezone_set('Europe/Belgrade');

set_time_limit(0);

session_start();

/**
 * This BASE_PATH is used in every class that needs another
 * class included inside it. This variable defines root
 * directory on page where class is used so current class
 * can include another class without errors.
 */
define('BASE_PATH', realpath(dirname(__FILE__)));

/**
 * Dependencies
 */
require_once BASE_PATH . '/../classes/uploadImage/UploadImage.php';
require_once BASE_PATH . '/../classes/uploadImage/RFLFileUploadException.php';

/* Security */
require_once BASE_PATH . "/../classes/security/Security.php";

/* Database */
require_once BASE_PATH . "/../classes/database/DataBase.php";
require_once BASE_PATH . "/../classes/database/DataBaseConfig.php";

/* Login and Account */
require_once BASE_PATH . "/../classes/log_in/Login.php";

/* Devices */
require_once "types_of_headers/devices_header.php";

/* Reports */
require_once "types_of_headers/reports_header.php";

/* Bussiness objects */
require_once BASE_PATH . '/../classes/bussiness_objects/Company.php';
//require_once BASE_PATH . '/../bussiness_objects/Client.php';
require_once BASE_PATH . '/../classes/bussiness_objects/ClientObject.php';

/* Users */
require_once BASE_PATH . "/../classes/users/User.php";
require_once BASE_PATH . "/../classes/users/AdminUser.php";

/* Utility */
require_once BASE_PATH . "/../classes/html_utilities/HTMLUtilities.php";

/**
 * Custom
 */
require_once BASE_PATH . '/../classes/session/SessionUtilities.php';
require_once BASE_PATH . '/../classes/html_utilities/HTMLUtilities.php';

