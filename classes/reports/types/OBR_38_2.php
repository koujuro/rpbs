<?php
/**
 * Created by PhpStorm.
 * User: sentinel
 * Date: 1/16/19
 * Time: 2:35 PM
 */

class OBR_38_2 extends Report {
    /** $objects Needs to be replaced */
    private $objects;
    private $operators;
    private $measuringDevices;
    private $devicesControls;

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
        $devicesControls = [];
        $sql = "SELECT id 
                FROM sviuredjaji 
                WHERE " . $this->generateSQLForSingleOrMultiObjects() . " AND type='Hydrants'";
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
                $device = new Hidrant();
                $device->setId($row['id']);
                $device->getBasicInfo();
                $device->getExtendedInfo();
                $deviceControl['id'] = $row['id'];
                $this->fetchDevicesDataForAllDevicesControls($deviceControl);
                $this->fetchDevicesDataForHydrantControl($deviceControl);
                $devices []= $device;
                $devicesControls []= $deviceControl;
            }
        }

        $this->devices = $devices;
        $this->devicesControls = $devicesControls;
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

    public function fetchDevicesDataForAllDevicesControls(&$device) {
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

    public function fetchDevicesDataForHydrantControl(&$device) {
        $sql = "SELECT staticPressure, dynamicPressure, m3hNetwork, lsNetwork, gapeDiameter
                FROM hidranticontrol H, hidrantiuppcontrol U, sviuredjajicontrolhistory S 
                WHERE H.HiUPPId=U.id AND U.PPAControlId=S.id AND S.ppaID=" . $device['id'] . " ORDER BY S.timeControlMillis DESC LIMIT 1";

        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $device['staticPressure'] = $row['staticPressure'];
            $device['dynamicPressure'] = $row['dynamicPressure'];
            $device['m3hNetwork'] = $row['m3hNetwork'];
            $device['lsNetwork'] = $row['lsNetwork'];
            $device['gapeDiameter'] = $row['gapeDiameter'];
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
                    WHERE ppaID=" . $device->getId() . "
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

    public function getDevicesControls() {
        return $this->devicesControls;
    }

    public function generateHTML() {
        $htmlContent = "";
        $htmlContent .= $this->generateHTMLForOperators()
            . $this->generateHTMLDescriptionOfHydrantNetwork()
            . $this->generateHTMLForDevicesData()
            . $this->generateHTMLForNetworkSatisfaction();
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

    public function generateHTMLDescriptionOfHydrantNetwork() {
        $htmlContent = "<div id='inputsPreview'>
            <h3 style='text-align: center'>OPIS INSTALACIJE HIDRANTSKE MREŽE KOJA JE PREDMET KONTROLISANJA 
            (spoljna hidrantska mreža, unutrašnja hidrantska mreža, snabdevanje električnom energijom, pumpno 
            postrojenje i dr.)</h3><br>";

        $underground = 0;
        $aboveGround = 0;
        $wallHydrants = 0;

        foreach ($this->getDevices() as $device) {
            if ($device->getDeviceDictionaryValue('type') === 'Hydrants') {
                if ($device->getHidrantDictionaryValue('subType') === 'Podzemni')
                    $underground++;
                else if ($device->getHidrantDictionaryValue('subType') === 'Nadzemni')
                    $aboveGround++;
                else if ($device->getHidrantDictionaryValue('subType') === 'Zidni')
                    $wallHydrants++;
            }
        }

        $htmlContent .= "- Spoljna hidrantska mreža : <br>
                            podzemnih komada: " . $underground . "<br>
                            nadzemnih komada: " . $aboveGround . "<br>
                            - Unutrašnja hidranska mreža :<br>
                            zidnih hidranata komada: " . $wallHydrants . "<br>
                            - Napajanje pumpnog postrojenja : KPK , DEA
        ";

        echo $htmlContent . "</div><br><br>";
    }

    public function generateHTMLForDevicesData() {
        $htmlContent = "<div id='inputsPreview'>
            <h3 style='text-align: center'>Rezultati provere i ispitivanja</h3><br>";

        for ($i = 0; $i < count($this->devices); $i++) {
            $htmlContent .= "
                <input type='text' id='sviUredjajiId" . $i . "' name='sviUredjajiId" . $i . "' value='" . $this->getDevices()[$i]->getId() . "_" . $this->getDevicesControls()[$i]['timeControlMillis'] . "' style='display: none'/>
                <label for='locationPP" . $i . "'>Lokacija uredjaja</label>
                <input type='text' id='locationPP" . $i . "' name='locationPP" . $i . "' value='" . $this->getDevicesControls()[$i]['locationPP'] . "'><br>
                <label for='hMark" . $i . "'>Oznaka</label>
                <input type='text' id='hMark" . $i . "' name='hMark" . $i . "' value='" . $this->getDevices()[$i]->getHidrantDictionaryValue('hMark') . "'><br>
                <label for='subType" . $i . "'>Tip hidranta</label>
                <input type='text' id='subType" . $i . "' name='subType" . $i . "' value='" . $this->getDevices()[$i]->getHidrantDictionaryValue('subType') . "'><br>
                <label for='staticPressure" . $i . "'>Staticki pritisak(bar)</label>
                <input type='text' id='staticPressure" . $i . "' name='staticPressure" . $i . "' value='" . $this->getDevicesControls()[$i]['staticPressure'] . "'><br>
                <label for='dynamicPressure" . $i . "'>Dinamicki pritisak(bar)</label>
                <input type='text' id='dynamicPressure" . $i . "' name='dynamicPressure" . $i . "' value='" . $this->getDevicesControls()[$i]['dynamicPressure'] . "'><br>
                <label for='m3hNetwork" . $i . "'>Protok(m3/h)</label>
                <input type='text' id='m3hNetwork" . $i . "' name='m3hNetwork" . $i . "' value='" . $this->getDevicesControls()[$i]['m3hNetwork'] . "'><br>
                <label for='note" . $i . "'>Napomena</label>
                <input type='text' id='note" . $i . "' name='note" . $i . "' value='" . $this->getDevicesControls()[$i]['note'] . "'><br>
                <label for='locationPP" . $i . "'>Ispravnost</label>
                <input type='radio' name='curState" . $i . "' value='in order' " . (($this->getDevicesControls()[$i]['curState'] === 'in order')?"checked":"") . ">Ispravan
                <input type='radio' name='curState" . $i . "' value='out of order' " . (($this->getDevicesControls()[$i]['curState'] === 'out of order')?"checked":"") . ">Neispravan<br>
                <br><br>";
        }

        echo $htmlContent . "</div><br><br>";
    }

    public function generateHTMLForNetworkSatisfaction() {
        $htmlContent = "<div id='inputsPreview'>
            <h3 style='text-align: center'>Ocena ispravnosti</h3><br>";
        $network = null;

        $sql = "SELECT `noteNetworkData`, `networkPressureState` 
                FROM `mrezniprotok` 
                WHERE objectID IN (SELECT id FROM objects WHERE clientID=" . $this->getClient()['id'] . ")";

        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $network = $row;
        }

        if ($network === null) {
            $htmlContent .= "Pritisak i protok vode u hidrantskoj mrezi nisu zavedeni u bazi podataka.";
        } else if ($network['networkPressureState'] === 'dissatisfies') {
            $htmlContent .= "Pritisak i protok vode u hidrantskoj mreži  dati su tabelarno i ne zadovoljavaju propisane standarde i norme iz oblasti zaštite od požara.";
        } else if($network['networkPressureState'] === 'satisfies') {
            $htmlContent .= "Pritisak i protok vode u hidrantskoj mreži  dati su tabelarno i zadovoljavaju propisane standarde i norme iz oblasti zaštite od požara.";
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
            $sqlForHidranti = "UPDATE hidranti
                               SET hMark='" . $dataArray['hMark' . $i] . "',
                                   subType='" . $dataArray['subType' . $i] . "'
                               WHERE sviUredjajiId=" . $idAndTimeControlMillis[0];

            $sqlForSviUredjajiControl = "UPDATE sviuredjajicontrolhistory
                                        SET locationPP='" . $dataArray['locationPP' . $i] . "', 
                                            note='" . $dataArray['note' . $i] . "', 
                                            curState='" . $dataArray['curState' . $i] . "'
                                        WHERE ppaID=" . $idAndTimeControlMillis[0] . " AND timeControlMillis=" . $idAndTimeControlMillis[1];

            $sqlForHidrantiControl = "UPDATE hidranticontrol
                                      SET staticPressure=" . $dataArray['staticPressure' . $i] . ",
                                          dynamicPressure=" . $dataArray['dynamicPressure' . $i] . ",
                                          m3hNetwork=" . $dataArray['m3hNetwork' . $i] . "
                                      WHERE HiUPPId=(SELECT id
                                                     FROM hidrantiuppcontrol
                                                     WHERE PPAControlId=(SELECT id
                                                                         FROM sviuredjajicontrolhistory
                                                                         WHERE ppaID=" . $idAndTimeControlMillis[0] . " 
                                                                         AND timeControlMillis=" . $idAndTimeControlMillis[1] . "))";

            DataBase::executionQuery($sqlForHidranti);
            DataBase::executionQuery($sqlForSviUredjajiControl);
            DataBase::executionQuery($sqlForHidrantiControl);
        }
    }

}