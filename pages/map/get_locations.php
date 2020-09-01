<?php

require_once "../../header/header.php";

$response = [];
$currentUser = SessionUtilities::getSession('User');

$sqlForIdAndLastControl = "SELECT S.id, S.lastControlMillis
                           FROM sviuredjaji S, objects O, clients C, barcodes B
                           WHERE S.objectID=O.id AND O.clientID=C.id AND S.barcodeID=B.id AND C.companyID = (SELECT companyID
                                                                                      FROM users 
                                                                                      WHERE username='$currentUser')";

$sqlFilter = "";
if (SessionUtilities::checkIsSessionSet('ClientFilter')) {
    $clientFilter = explode(":", SessionUtilities::getSession('ClientFilter'));
    $sqlFilter .= " AND C.id=" . $clientFilter[0];
}
if (SessionUtilities::checkIsSessionSet('ObjectFilter')) {
    $objectFilter = explode(":", SessionUtilities::getSession('ObjectFilter'));
    $sqlFilter .= " AND O.id=" . $objectFilter[0];
}
if (SessionUtilities::checkIsSessionSet('TypeFilter')) {
    $typeFilter = SessionUtilities::getSession('TypeFilter');
    $sqlFilter .= " AND S.type='" . $typeFilter . "'";
}
if (SessionUtilities::checkIsSessionSet('TimeFilter')) {
    $timeFilter = SessionUtilities::getSession('TimeFilter');
    $timeFilter = explode(":", $timeFilter);
    $timeFilter[0] = (int)$timeFilter[0];
    $timeFilter[1] = (int)$timeFilter[1] + 86400000;
    $sqlFilter .= " AND (S.creationTimeMillis BETWEEN " . $timeFilter[0] . " AND  " . $timeFilter[1] . ")";
}
$sqlForIdAndLastControl .= $sqlFilter;

$resultForIdAndLastControl = DataBase::selectionQuery($sqlForIdAndLastControl);
if ($resultForIdAndLastControl->num_rows > 0) {
    while ($rowForIdAndLastControl = $resultForIdAndLastControl->fetch_assoc()) {
        $sqlLocation = "SELECT longitude, latitude FROM sviuredjajicontrolhistory WHERE ppaID=" . $rowForIdAndLastControl['id'] . " AND timeControlMillis=" . $rowForIdAndLastControl['lastControlMillis'] . "";
        $resultLocation = DataBase::selectionQuery($sqlLocation);
        $rowLocation = $resultLocation->fetch_assoc();
        $response [] = ['longitude' => $rowLocation['longitude'], 'latitude' => $rowLocation['latitude']];
    }
}

$jsonResponse = json_encode($response);
echo $jsonResponse;

