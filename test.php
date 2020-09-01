<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "header/header.php";

// =================================================
//             Testing Reports Classes
// =================================================
/**
 * @var $zapisAparati ZapisAparati
 * @var $zapisKontrolisanje ZapisKontrolisanje
 * @var $obr_38_2 OBR_38_2
 */
//$zapisAparati = ReportsContext::getInstance()->pickReport('zapis-aparati');
//$zapisAparati->fetchMainReportData('deki', 5, 135);
//
//$objectID = $zapisAparati->getObject()['id'];
//
//$zapisAparati->fetchDevicesFromDB($objectID);
//$zapisAparati->fetchLocationsAndNotesFromDB($objectID);
//$zapisAparati->fetchOperatorFromDB('deki');

SessionUtilities::createSession('User', 'dragan');
SessionUtilities::createSession('MDFilter', "4;5");

//$zapisKontrolisanje = ReportsContext::getInstance()->pickReport('zapis-kontrolisanje');
//$zapisKontrolisanje->fetchMainReportData('deki', 4, 120);
//$zapisKontrolisanje->fetchExtendedReportData();

$obr_38_2 = ReportsContext::getInstance()->pickReport('obr-38-2');
$obr_38_2->fetchMainReportData('dragan', 4, 9);
$obr_38_2->fetchExtendedReportData();
//
//$MDIDs = explode(";", "4;5");
//
//$obr_38_2->fetchDevicesFromDB(5);
//$obr_38_2->fetchMeasuringDevicesFromDB($MDIDs);
//$obr_38_2->fetchOperatorsFromDB(3);
//$obr_38_2->fetchObjectsByClientIDInfo(5);

//var_dump($zapisAparati->getDevices());
//var_dump($zapisAparati-


// =================================================


var_dump($obr_38_2->getDevices());

