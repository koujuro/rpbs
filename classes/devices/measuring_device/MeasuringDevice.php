<?php
/**
 * Created by PhpStorm.
 * User: sentinel
 * Date: 3/25/19
 * Time: 1:45 PM
 */

class MeasuringDevice {
    private $id;
    private $type;
    private $manufacturer;
    private $fabricID;
    private $accuracyClass;
    private $calibrationTestimonial;
    private $companyID;

    public function __construct() {}

    public function setParameters($data) {
        $this->id = $data['id'];
        $this->type = $data['type'];
        $this->manufacturer = $data['manufacturer'];
        $this->fabricID = $data['fabricID'];
        $this->accuracyClass = $data['accuracyClass'];
        $this->calibrationTestimonial = $data['calibrationTestimonial'];
        $this->companyID = $data['companyID'];
    }

    public function setParametersForNewMD($data) {
        $this->parseDataKeysInPOST($data);
        $this->id = $this->getLastMDIdFromDatabase();
        $this->type = $data['type'];
        $this->manufacturer = $data['manufacturer'];
        $this->fabricID = $data['fabricID'];
        $this->accuracyClass = $data['accuracyClass'];
        $this->calibrationTestimonial = $data['calibrationTestimonial'];
        $this->companyID = $this->getCompanyIdByUserFromSession();
    }

    public function setParametersFromPOST($data) {
        $this->parseDataKeysInPOST($data);
        $this->id = $data['id'];
        $this->type = $data['type'];
        $this->manufacturer = $data['manufacturer'];
        $this->fabricID = $data['fabricID'];
        $this->accuracyClass = $data['accuracyClass'];
        $this->calibrationTestimonial = $data['calibrationTestimonial'];
        $this->companyID = $this->getCompanyIdByUserFromSession();
    }

    private function getLastMDIdFromDatabase() {
        $sql = "SELECT id FROM measuringdevices ORDER BY id DESC LIMIT 1";
        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['id'];
        }
        return null;
    }

    public function parseDataKeysInPOST(&$post) {
        foreach ($post as $key => $value) {
            if (strpos($key, "_") !== false) {
                unset($post[$key]);
                $key = explode("_", $key)[1];
                $post[$key] = $value;
            }
        }
    }

    public function getCompanyIdByUserFromSession() {
        $currentUser = SessionUtilities::getSession('User');
        $sql = "SELECT companyID FROM users WHERE username='$currentUser'";
        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['companyID'];
        }
        return null;
    }

    public static function fetchAllMDsFromDatabase() {
        $devices = [];
        $sql = "SELECT * FROM measuringdevices WHERE companyID = (SELECT companyID 
                                                                  FROM users
                                                                  WHERE username='" . SessionUtilities::getSession('User') . "')";

        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $device = new MeasuringDevice();
                $device->setParameters($row);
                $devices []= $device;
            }
        }

        return $devices;
    }

    public function insertNewMDIntoDatabase() {
        $sql = "INSERT INTO `measuringdevices`(`type`, 
                                              `manufacturer`, 
                                              `fabricID`, 
                                              `accuracyClass`, 
                                              `calibrationTestimonial`, 
                                              `companyID`) 
                    VALUES ('$this->type',
                            '$this->manufacturer',
                            '$this->fabricID',
                            '$this->accuracyClass',
                            '$this->calibrationTestimonial',
                            '$this->companyID')";

        DataBase::executionQuery($sql);
    }

    public function updateMDInDatabase() {
        $sql = "UPDATE measuringdevices 
                SET type='$this->type',
                    manufacturer='$this->manufacturer',
                    fabricID='$this->fabricID',
                    accuracyClass='$this->accuracyClass',
                    calibrationTestimonial='$this->calibrationTestimonial' 
                WHERE id=" . $this->id;

        DataBase::executionQuery($sql);
    }

    public function deleteMDInDatabase() {
        $sql = "DELETE FROM measuringdevices WHERE id=" . $this->id;

        DataBase::executionQuery($sql);
    }

    public function generateHTMLListOfDevices($measuringDevices) {
        $i = 1;
        foreach ($measuringDevices as $measuringDevice) {
            echo "<div class=\"type filter-option\" id=\"" . $measuringDevice->getId() . "\" onclick=\"filterOptionsClick(this);sendChosenMD(this.id)\" data-type-id=\"".$measuringDevice->getId()."\"> <span class=\"option-id\">".$measuringDevice->getId()."</span> Instrument ".($i++)."</div>";
        }
    }

    public function getId() {
        return $this->id;
    }

}