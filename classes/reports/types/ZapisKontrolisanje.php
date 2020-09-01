<?php
/**
 * Created by PhpStorm.
 * User: sentinel
 * Date: 1/15/19
 * Time: 4:20 PM
 */

class ZapisKontrolisanje extends Report {
    private $operator;
    private $network;

    public function __construct() {}

    public function fetchExtendedReportData() {
        $this->fetchDevicesFromDB($this->getObject()->getId());
        $this->fetchOperatorFromDB(SessionUtilities::getSession('User'));
        $this->fetchNetworkFromDB($this->getObject()->getId());
    }

    public function fetchDevicesFromDB($objectID) {
        $devices = [];
        $sql = "SELECT sviUredjajiId, hMark, subType
                FROM hidranti
                WHERE sviUredjajiId IN (SELECT id
                                        FROM sviuredjaji
                                        WHERE objectID=$objectID AND type='Hydrants')";

        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $devices []= $row;
            }
        }

        $this->devices = $this->fetchSviUredjajiControlHistory($devices, $objectID);
        $this->devices = $this->fetchHidrantControlHistory($this->devices);
    }

    private function fetchSviUredjajiControlHistory($devices, $objectID) {
        $sqlForIds = "SELECT id
                      FROM sviuredjaji
                      WHERE objectID=$objectID AND type='Hydrants'";
        $resultForIds = DataBase::selectionQuery($sqlForIds);
        if ($resultForIds->num_rows > 0) {
            for ($i = 0; $i < $resultForIds->num_rows; $i++) {
                $rowId = $resultForIds->fetch_assoc();
                $sqlForControl = "SELECT note, locationPP, curState, timeControlMillis
                                  FROM sviuredjajicontrolhistory
                                  WHERE ppaID=" . $rowId['id'] . "
                                  ORDER BY timeControlMillis DESC LIMIT 1";
                $result = DataBase::selectionQuery($sqlForControl);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $devices[$i]['note'] = $row['note'];
                    $devices[$i]['locationPP'] = $row['locationPP'];
                    $devices[$i]['curState'] = $row['curState'];
                    $devices[$i]['timeControlMillis'] = $row['timeControlMillis'];
                }
            }
        }

        return $devices;
    }

    private function fetchHidrantControlHistory($devices) {
        for ($i = 0; $i < count($devices); $i++) {
            $sql = "SELECT staticPressure, dynamicPressure, m3hNetwork, lsNetwork, gapeDiameter
                    FROM hidranticontrol H, hidrantiuppcontrol U, sviuredjajicontrolhistory S 
                    WHERE H.HiUPPId=U.id AND U.PPAControlId=S.id AND S.ppaID=" . $devices[$i]['sviUredjajiId'] . " ORDER BY S.timeControlMillis DESC LIMIT 1";

            $result = DataBase::selectionQuery($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $devices[$i]['staticPressure'] = $row['staticPressure'];
                $devices[$i]['dynamicPressure'] = $row['dynamicPressure'];
                $devices[$i]['m3hNetwork'] = $row['m3hNetwork'];
                $devices[$i]['lsNetwork'] = $row['lsNetwork'];
                $devices[$i]['gapeDiameter'] = $row['gapeDiameter'];
            }
        }

        return $devices;
    }

    public function fetchOperatorFromDB($profile) {
        $sql = "SELECT fullname FROM users WHERE username='$profile'";
        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->operator = $row;
        }
    }

    public function fetchNetworkFromDB($objectID) {
        $sql = "SELECT id, operatorID, timeMillis, longitude, latitude, lsNetwork, m3hNetwork, gapeDiameter, noteNetworkData, networkPressureState, staticNetworkPressure, dynamicNetworkPressure 
                FROM mrezniprotok 
                WHERE objectID=$objectID";

        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->network = $row;
        }
    }

    public function getOperator() {
        return $this->operator;
    }

    public function getNetwork() {
        return $this->network;
    }

    public function generateHTML() {
        $htmlContent = "";
        $htmlContent .= $this->generateHTMLForDevicesData()
                        . $this->generateHTMLForTypeOfHydrantNetwork()
                        . $this->generateHTMLForInnerAndOutterHydrantNetwork()
                        . $this->generateHTMLForNetworkSatisfaction();

        echo $htmlContent;
    }

    public function generateHTMLForDevicesData() {
        $htmlContent = "<div id='inputsPreview'>
            <h3 style='text-align: center'>Ocena ispravnosti instalacije hidrantske mreže, spisak hidrantskih ormara koji 
            su predmet kontrolisanja i vrednosti dobijene kontrolisanjem</h3><br>";

        for ($i = 0; $i < count($this->devices); $i++) {
            $htmlContent .= "
                <input type='text' id='sviUredjajiId" . $i . "' name='sviUredjajiId" . $i . "' value='" . $this->getDevices()[$i]['sviUredjajiId'] . "_" . $this->getDevices()[$i]['timeControlMillis'] . "' style='display: none'/>
                <label for='locationPP" . $i . "'>Lokacija uredjaja</label>
                <input type='text' id='locationPP" . $i . "' name='locationPP" . $i . "' value='" . $this->getDevices()[$i]['locationPP'] . "'><br>
                <label for='hMark" . $i . "'>Oznaka hidranta</label>
                <input type='text' id='hMark" . $i . "' name='hMark" . $i . "' value='" . $this->getDevices()[$i]['hMark'] . "'><br>
                <label for='subType" . $i . "'>Tip hidranta</label>
                <input type='text' id='subType" . $i . "' name='subType" . $i . "' value='" . $this->getDevices()[$i]['subType'] . "'><br>
                <label for='staticPressure" . $i . "'>Staticki pritisak(bar)</label>
                <input type='text' id='staticPressure" . $i . "' name='staticPressure" . $i . "' value='" . $this->getDevices()[$i]['staticPressure'] . "'><br>
                <label for='dynamicPressure" . $i . "'>Dinamicki pritisak(bar)</label>
                <input type='text' id='dynamicPressure" . $i . "' name='dynamicPressure" . $i . "' value='" . $this->getDevices()[$i]['dynamicPressure'] . "'><br>
                <label for='m3hNetwork" . $i . "'>Protok(m3/h)</label>
                <input type='text' id='m3hNetwork" . $i . "' name='m3hNetwork" . $i . "' value='" . $this->getDevices()[$i]['m3hNetwork'] . "'><br>
                <label for='note" . $i . "'>Napomena</label>
                <input type='text' id='note" . $i . "' name='note" . $i . "' value='" . $this->getDevices()[$i]['note'] . "'><br>
                <label for='locationPP" . $i . "'>Ispravnost</label>
                <input type='radio' name='curState" . $i . "' value='in order' " . (($this->getDevices()[$i]['curState'] === 'in order')?"checked":"") . ">Ispravan
                <input type='radio' name='curState" . $i . "' value='out of order' " . (($this->getDevices()[$i]['curState'] === 'out of order')?"checked":"") . ">Neispravan<br>
                <br><br>";
        }

        $htmlContent .= "</div><br>";
        echo $htmlContent;
    }

    public function generateHTMLForTypeOfHydrantNetwork() {
        $aboveGround = "□";
        $underground = "□";
        $wallHydrants = "□";
        $checkmark = "<div id='box' style='display: inline'>&#10003;</div>";

        foreach ($this->getDevices() as $device) {
            if ($device['subType'] === 'Nadzemni')
                $aboveGround = $checkmark;
            else if ($device['subType'] === 'Podzemni')
                $underground = $checkmark;
            else if ($device['subType'] === 'Zidni')
                $wallHydrants = $checkmark;
        }

        echo "<div><h3 style='text-align: center'>Tip i vrsta hidrantske mreže:</h3><br>
                mokra □ suva □<br>
                spoljna □ unutrašnja □<br>
                podzemni " . $underground . " nadzemni " . $aboveGround . " zidni hidranti " . $wallHydrants . "
              </div><br><br>";
    }

    public function generateHTMLForInnerAndOutterHydrantNetwork() {
        $aboveGround = 0;
        $underground = 0;
        $wallHydrants = 0;

        foreach ($this->getDevices() as $device) {
            if ($device['subType'] === 'Podzemni')
                $underground++;
            else if ($device['subType'] === 'Nadzemni')
                $aboveGround++;
            else if ($device['subType'] === 'Zidni')
                $wallHydrants++;

        }

        echo "<div><h3 style='text-align: center'>Broj uredjaja u spoljasnjoj i unutrasnjoj hidrantskoj mrezi</h3>
              <div>
                <strong><em>Spoljna hidrantska mreža:</em></strong><br>
                podzemnih hidranata - komada: " . $underground . "<br>nadzemnih hidranata komada: " . $aboveGround . "
              </div>
              <div>
                <strong><em>Unutrašnja hidrantska mreža:</em></strong><br>
                zidnih hidranata - komada: " . $wallHydrants . "
              </div>
              </div><br><br>";
    }

    public function generateHTMLForNetworkSatisfaction() {
        $satisfiesChecked = "";
        $dissatisfiesChecked = "";

        if ($this->getNetwork()['networkPressureState'] === 'satisfies')
            $satisfiesChecked = "checked";
        else if ($this->getNetwork()['networkPressureState'] === 'dissatisfies')
            $dissatisfiesChecked = "checked";

        echo "<div>
              <h3 style='text-align: center'>Pritisak i protok vode u hidrantskoj mreži:</h3><br>
              <input type='radio' name='networkPressureState' value='satisfies' " . $satisfiesChecked . "> ZADOVOLJAVA
              <input type='radio' name='networkPressureState' value='dissatisfies' " . $dissatisfiesChecked . "> NE ZADOVOLJAVA
              </div><br><br><br>";
    }

    public function updateDB($dataArray) {
        $this->updateDBHydrantDevices($dataArray);
        $this->updateDBNetwork($dataArray);
    }

    public function updateDBHydrantDevices($dataArray) {
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

    public function updateDBNetwork($dataArray) {
        if (isset($dataArray['networkPressureState'])) {
            $sqlForNetwork = "UPDATE mrezniprotok
                          SET networkPressureState='" . $dataArray['networkPressureState'] . "'
                          WHERE id=" . $this->getNetwork()['id'];

            DataBase::executionQuery($sqlForNetwork);
        }
    }

}