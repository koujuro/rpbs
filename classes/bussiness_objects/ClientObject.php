<?php
/**
 * Created by PhpStorm.
 * User: sentinel
 * Date: 3/18/19
 * Time: 2:30 PM
 */

class ClientObject {
    private $id;
    private $clientID;
    private $streetAndNumber;
    private $city;
    private $PAC;
    private $objectName;
    private $floorsAboveGround;
    private $floorsUnderground;
    private $highestObjectAltitude;

    public function __construct() {}

    public function setParameters($data) {
        $this->id = $data['id'];
        $this->clientID = $data['clientID'];
        $this->streetAndNumber = $data['streetAndNumber'];
        $this->city = $data['city'];
        $this->PAC = $data['PAC'];
        $this->objectName = $data['objectName'];
        $this->floorsAboveGround = $data['floorsAboveGround'];
        $this->floorsUnderground = $data['floorsUnderground'];
        $this->highestObjectAltitude = $data['highestObjectAltitude'];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getClientID()
    {
        return $this->clientID;
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

    public function getObjectName()
    {
        return $this->objectName;
    }

    public function getFloorsAboveGround()
    {
        return $this->floorsAboveGround;
    }

    public function getFloorsUnderground()
    {
        return $this->floorsUnderground;
    }

    public function getHighestObjectAltitude()
    {
        return $this->highestObjectAltitude;
    }

}