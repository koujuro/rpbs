<?php
/**
 * Created by PhpStorm.
 * User: sentinel
 * Date: 1/15/19
 * Time: 1:21 PM
 */

abstract class Report {
    /**
     * @var $object ClientObject
     */
    protected $company;
    protected $client;
    protected $object;
    protected $devices;

    public function __construct() {}

    public function fetchMainReportData($profile, $clientID, $objectID) {
        $this->fetchCompanyByProfileFromDB($profile);
        $this->fetchClientFromDB($clientID);
        $this->fetchObjectFromDB($objectID);
    }

    public function fetchMainReportDataFromSessions() {
        // Check for security?
        $profile = SessionUtilities::getSession('User');
        $clientID = null;
        $objectID = null;
        if (SessionUtilities::checkIsSessionSet('ClientFilter')) {
            $clientID = (int)explode(":", SessionUtilities::getSession('ClientFilter'))[0];
        }
        if (SessionUtilities::checkIsSessionSet('ObjectFilter')) {
            $objectID = (int)explode(":", SessionUtilities::getSession('ObjectFilter'))[0];
        }

        $this->fetchMainReportData($profile, $clientID, $objectID);
    }

    public abstract function fetchExtendedReportData();

    public function fetchCompanyByProfileFromDB($profile) {
        $sql = "SELECT * FROM companies WHERE id=(SELECT companyID
                                              FROM users
                                              WHERE username='$profile')";
        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->company = $row;
        }
    }

    public function fetchClientFromDB($clientID) {
        $sql = "SELECT id, clientName, streetAndNumber, city, PAC FROM clients WHERE id=$clientID";
        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->client = $row;
        }
    }

    public function fetchObjectFromDB($objectID) {
        $sql = "SELECT id, clientID, streetAndNumber, city, PAC, objectName, floorsAboveGround, floorsUnderground, highestObjectAltitude 
            FROM objects 
            WHERE id=$objectID";

        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->object = new CLientObject();
            $this->object->setParameters($row);
        }
    }

    public abstract function fetchDevicesFromDB($objectID);

    public function getObject() {
        return $this->object;
    }

    public function getCompany() {
        return $this->company;
    }

    public function getClient() {
        return $this->client;
    }

    public function getDevices() {
        return $this->devices;
    }

    public abstract function generateHTML();

    public abstract function updateDB($dataArray);

}