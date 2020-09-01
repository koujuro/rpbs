<?php
/**
 * Created by PhpStorm.
 * User: sentinel
 * Date: 2/7/19
 * Time: 11:04 PM
 */

class OBR_R08_35_1 extends Report{
    private $objects;
    private $operators;
    private $measuringDevices;

    public function __construct() {}

    public function fetchExtendedReportData() {
        $this->fetchObjectsBySameAddress();
        $this->fetchDevicesFromDB($this->getObject()->getId());
        $this->fetchOperatorsFromDB();
        if (!SessionUtilities::checkIsSessionSet('MDFilter')) {
            $this->measuringDevices = [];
        } else if (SessionUtilities::getSession('MDFilter') !== "") {
            $MDIDs = SessionUtilities::getSession('MDFilter');
            $this->fetchMeasuringDevicesFromDB(explode(";", $MDIDs));
        } else {
            $this->measuringDevices = [];
        }
    }

    public function fetchDevicesFromDB($objectID) {
        $devices = [];
        $sql = "SELECT id
                FROM sviuredjaji
                WHERE " . $this->generateSQLForSingleOrMultiObjects() . " AND type NOT IN ('Hydrants', 'UPP')";
        if (SessionUtilities::checkIsSessionSet('TimeFilter')) {
            $timeFilter = SessionUtilities::getSession('TimeFilter');
            $timeFilter = explode(":", $timeFilter);
            $timeFilter[0] = (int)$timeFilter[0];
            $timeFilter[1] = (int)$timeFilter[1] + 86400000;
            $sql .= " AND (lastControlMillis BETWEEN " . $timeFilter[0] . " AND  " . $timeFilter[1] . ")";
        }
        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $device['id'] = $row['id'];
                $this->fetchDevicePPAparatiData($device);
                $this->fetchDeviceSviUredjajiControl($device);
                $devices []= $device;
            }
        }

        $this->devices = $devices;
    }

    public function generateSQLForSingleOrMultiObjects(){
        if (count($this->getObjects()) > 0) {
            $sql = "objectID IN (";
            foreach ($this->getObjects() as $object) {
                $sql .= $object->getId() . ",";
            }
            $sql = substr($sql, 0, -1) . ")";
        } else
            $sql = "objectID=" . $this->getObject()->getId();

        return $sql;
    }

    private function fetchDevicePPAparatiData(&$device) {
        $sql = "SELECT fabricId, manufacturerData, creationYear, subType
                FROM ppaparati
                WHERE sviUredjajiId=" . $device['id'];

        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $device['fabricId'] = $row['fabricId'];
            $device['manufacturerData'] = $row['manufacturerData'];
            $device['creationYear'] = $row['creationYear'];
            $device['subType'] = $row['subType'];
        }
    }

    private function fetchDeviceSviUredjajiControl(&$device) {
        $sql = "SELECT note, locationPP, curState, timeControlMillis
                                  FROM sviuredjajicontrolhistory
                                  WHERE ppaID=" . $device['id'] . "
                                  ORDER BY timeControlMillis DESC LIMIT 1";
        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $device['note'] = $row['note'];
            $device['locationPP'] = $row['locationPP'];
            $device['curState'] = $row['curState'];
            $device['timeControlMillis'] = $row['timeControlMillis'];
        }
    }

    public function fetchOperatorsFromDB() {
        $operators = [];
        $operatorsIDs = $this->fetchOperatorsIDs();
        foreach ($operatorsIDs as $operatorID) {
            $sql = "SELECT id, username, fullname, licenceNumber 
                    FROM users 
                    WHERE id=" . $operatorID;
            $result = DataBase::selectionQuery($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc())
                    $operators [] = $row;
            }
        }
        $this->operators = $operators;
    }

    public function fetchOperatorsIDs() {
        $operatorsIDs = [];
        foreach ($this->getDevices() as $device) {
            $sql = "SELECT operatorID 
                    FROM sviuredjajicontrolhistory 
                    WHERE ppaID=" . $device['id'] . "
                    ORDER BY timeControlMillis DESC LIMIT 1";
            $result = DataBase::selectionQuery($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $operatorsIDs []= $row['operatorID'];
            }
        }
        $operatorsIDs = array_unique($operatorsIDs);
        return $operatorsIDs;
    }

    public function fetchMeasuringDevicesFromDB($MDIDs) {
        $MDs = [];
        $sql = "SELECT `type`, `manufacturer`, `fabricID`, `accuracyClass`, `calibrationTestimonial`, `companyID` 
                FROM `measuringdevices` 
                WHERE id IN(";

        foreach ($MDIDs as $id) {
            $sql .= $id . ",";
        }
        $sql = substr($sql, 0, -1) . ")";

        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc())
                $MDs []= $row;
        }

        $this->measuringDevices = $MDs;
    }

    public function fetchObjectsBySameAddress() {
        $objects = [];
        if (SessionUtilities::checkIsSessionSet('NumOfObjects')) {
            if (SessionUtilities::getSession('NumOfObjects') === "multi") {
                $streetAndNumber = $this->object->getStreetAndNumber();
                $city = $this->object->getCity();
                $clientID = $this->object->getClientID();

                $sql = "SELECT * FROM `objects` WHERE clientID=$clientID AND streetAndNumber='$streetAndNumber' AND city='$city'";
                $result = DataBase::selectionQuery($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $object = new ClientObject();
                        $object->setParameters($row);
                        $objects []= $object;
                    }
                }
            }
        }

        $this->objects = $objects;
    }

    public function getObjects() {
        return $this->objects;
    }

    public function getOperators() {
        return $this->operators;
    }

    public function getMeasuringDevices() {
        return $this->measuringDevices;
    }

    public function generateHTML() {
        $htmlContent = "";
        $htmlContent .= $this->generateHTMLForOperators()
            . $this->generateHTMLForDevicesData();
        echo $htmlContent;
    }

    public function generateHTMLForOperators() {
        $htmlContent = "<div id='inputsPreview'>
            <h3 style='text-align: center'>Podaci o kompetentnim licima koja su obavila kontrolisanje</h3><br>";

        for ($i = 0; $i < count($this->operators); $i++) {
            $htmlContent .= "
            <div class='deviceInputs'>
                <input type='text' id='operatorsId" . $i . "' name='operatorsId" . $i . "' value='" . $this->getOperators()[$i]['id'] . "' style='display: none'/>
                <div class='input-group'>
                    <label for='fullname" . $i . "'>Ime i prezime</label>
                    <input type='text' id='fullname" . $i . "' name='fullname" . $i . "' value='" . $this->getOperators()[$i]['fullname'] . "'>
                </div>
                <div class='input-group'>
                    <label for='licenceNumber" . $i . "'>Broj licence &nbsp;&nbsp;&nbsp;</label>
                    <input type='text' id='licenceNumber" . $i . "' name='licenceNumber" . $i . "' value='" . $this->getOperators()[$i]['licenceNumber'] . "'>    
                </div>
            </div>";
        }

        echo $htmlContent . "</div><br>";
    }

    public function generateHTMLForDevicesData() {
        $htmlContent = "<div id='inputsPreview'>
            <h3 style='text-align: center'>Rezultati provere i ispitivanja</h3><br>";

        for ($i = 0; $i < count($this->devices); $i++) {
            $htmlContent .= "
            <div class='deviceInputs'>
                <input type='text' id='sviUredjajiId" . $i . "' name='sviUredjajiId" . $i . "' value='" . $this->getDevices()[$i]['id'] . "_" . $this->getDevices()[$i]['timeControlMillis'] . "' style='display: none'/>
                <div class='input-group'>
                    <label for='locationPP" . $i . "'>Lokacija uredjaja</label>
                    <input type='text' id='locationPP" . $i . "' name='locationPP" . $i . "' value='" . $this->getDevices()[$i]['locationPP'] . "'>
                </div>
                <div class='input-group'>
                    <label for='note" . $i . "'>Napomena</label>
                    <input type='text' id='note" . $i . "' name='note" . $i . "' value='" . $this->getDevices()[$i]['note'] . "'>
                </div>
                <div class='input-group'>
                    <label for='subType" . $i . "'>Tip</label>
                    <input type='text' id='subType" . $i . "' name='subType" . $i . "' value='" . $this->getDevices()[$i]['subType'] . "'>
                </div>
                <div class='input-group'>
                    <label for='manufacturerData" . $i . "'>Podaci o proizvodjaču</label>
                    <input type='text' id='manufacturerData" . $i . "' name='manufacturerData" . $i . "' value='" . $this->getDevices()[$i]['manufacturerData'] . "'>
                </div>
                <div class='input-group'>
                    <label for='fabricId" . $i . "'>Fabrički broj</label>
                    <input type='text' id='fabricId" . $i . "' name='fabricId" . $i . "' value='" . $this->getDevices()[$i]['fabricId'] . "'>
                </div>
                <div class='input-group'>
                    <label for='creationYear" . $i . "'>Godina proizvodnje</label>
                    <input type='text' id='creationYear" . $i . "' name='creationYear" . $i . "' value='" . $this->getDevices()[$i]['creationYear'] . "'>
                </div>
                <div class='input-group'>
                    <label for='locationPP" . $i . "'>Ispravnost</label>
                    <div class='radios'>
                        <input type='radio' name='curState" . $i . "' value='in order' " . (($this->getDevices()[$i]['curState'] === 'in order')?"checked":"") . ">Ispravan
                        <input type='radio' name='curState" . $i . "' value='out of order' " . (($this->getDevices()[$i]['curState'] === 'out of order')?"checked":"") . ">Neispravan 
                    </div>
                </div>
            </div>";
        }

        echo $htmlContent . "</div><br><br>";
    }

    public function updateDB($dataArray) {
        $this->updateDBForOperators($dataArray);
        $this->updateDBForDevicesData($dataArray);
    }

    public function updateDBForOperators($dataArray) {
        for ($i = 0; $i < count($this->operators); $i++) {
            $sql = "UPDATE users 
                    SET fullname='" . $dataArray['fullname' . $i] . "', 
                        licenceNumber='" . $dataArray['licenceNumber' . $i] . "' 
                    WHERE id=" . $dataArray['operatorsId' . $i];

            DataBase::executionQuery($sql);
        }
    }

    public function updateDBForDevicesData($dataArray) {
        for ($i = 0; $i < count($this->getDevices()); $i++) {
            $idAndTimeControlMillis = explode("_", $dataArray['sviUredjajiId' . $i]);
            $sqlForPPAparati = "UPDATE ppaparati
                               SET fabricId='" . $dataArray['fabricId' . $i] . "',
                                   manufacturerData='" . $dataArray['manufacturerData' . $i] . "',
                                   creationYear=" . $dataArray['creationYear' . $i] . ",
                                   subType='" . $dataArray['subType' . $i] . "'
                               WHERE sviUredjajiId=" . $idAndTimeControlMillis[0];

            $sqlForSviUredjajiControl = "UPDATE sviuredjajicontrolhistory
                                        SET locationPP='" . $dataArray['locationPP' . $i] . "', 
                                            note='" . $dataArray['note' . $i] . "', 
                                            curState='" . $dataArray['curState' . $i] . "'
                                        WHERE ppaID=" . $idAndTimeControlMillis[0] . " AND timeControlMillis=" . $idAndTimeControlMillis[1];

            DataBase::executionQuery($sqlForPPAparati);
            DataBase::executionQuery($sqlForSviUredjajiControl);
        }
    }

}