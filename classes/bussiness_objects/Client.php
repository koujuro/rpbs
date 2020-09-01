<?php
/**
 * Created by PhpStorm.
 * User: sentinel
 * Date: 3/18/19
 * Time: 2:30 PM
 */

class Client {
    private $id;
    private $companyID;
    private $clientName;
    private $streetAndNumber;
    private $city;
    private $PAC;

    public function __construct() {}

    public function setParametersFromDB($data) {
        $this->id = $data['id'];
        $this->companyID = $data['companyID'];
        $this->clientName = $data['clientName'];
        $this->streetAndNumber = $data['streetAndNumber'];
        $this->city = $data['city'];
        $this->PAC = $data['PAC'];
    }

    public function setParametersForNewClient($data) {
        $this->id = $this->getLastClientIdFromDB();
        $this->companyID = $data['companyID'];
        $this->clientName = $data['clientName'];
        $this->streetAndNumber = $data['streetAndNumber'];
        $this->city = $data['city'];
        $this->PAC = $data['PAC'];
    }

    private function getLastClientIdFromDB() {
        $sql = "SELECT id FROM clients ORDER BY id DESC LIMIT 1";
        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['id'];
        }
        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCompanyID()
    {
        return $this->companyID;
    }

    public function getClientName()
    {
        return $this->clientName;
    }

    public function getStreetAndNumber()
    {
        return $this->streetAndNumber;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function getPAC()
    {
        return $this->PAC;
    }

}