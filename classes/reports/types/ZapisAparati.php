<?php
/**
 * Created by PhpStorm.
 * User: sentinel
 * Date: 1/15/19
 * Time: 2:20 PM
 */

class ZapisAparati extends Report {
    private $operator;
    private $locationsAndNotes;

    public function __construct() {}

    public function fetchExtendedReportData() {
        $this->fetchDevicesFromDB($this->getObject()->getId());
        $this->fetchOperatorFromDB(SessionUtilities::getSession('User'));
        $this->fetchLocationsAndNotesFromDB($this->getObject()->getId());
    }

    public function fetchDevicesFromDB($objectID) {
        $devices = [];
        $sql = "SELECT id, type
                FROM sviuredjaji 
                WHERE objectID=$objectID AND type NOT IN ('Hydrants','UPP')";

        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $device = new PPAparat();
                $device->setId($row['id']);
                $device->getBasicInfo();
                $device->getExtendedInfo();
                $devices []= $device;
            }
        }

        $this->devices = $devices;
    }

    public function fetchOperatorFromDB($profile) {
        $sql = "SELECT fullname FROM users WHERE username='$profile'";
        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->operator = $row;
        }
    }

    public function fetchLocationsAndNotesFromDB($objectID) {
        $locationsAndNotes = [];
        $sql = "SELECT id
                FROM sviuredjaji 
                WHERE type NOT IN ('Hydrants','UPP') AND objectID=$objectID";

        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $secondSql = "SELECT ppaID, locationPP, note, curState, timeControlMillis
                              FROM sviuredjajicontrolhistory
                              WHERE ppaID=" . $row['id'] . "
                              ORDER BY timeControlMillis DESC LIMIT 1";

                $secondResult = DataBase::selectionQuery($secondSql);
                if ($secondResult->num_rows > 0) {
                    $row2 = $secondResult->fetch_assoc();
                    $locationsAndNotes []= $row2;
                }
            }
        }

        $this->locationsAndNotes = $locationsAndNotes;
    }

    public function getOperator() {
        return $this->operator;
    }

    public function getLocationsAndNotes() {
        return $this->locationsAndNotes;
    }

    public function generateHTML() {
        $htmlContent = "";
        $htmlContent .= $this->generateHTMLSpisakUredjaja();

        echo $htmlContent;
    }

    public function generateHTMLSpisakUredjaja() {
        $htmlContent = "<div id='inputsPreview'>
            <h3 style='text-align: center'>Spisak uredjaja koji su predmet kontrolisanja sa podacima i ocenom ispravnost</h3><br>";

        for ($i = 0; $i < count($this->devices); $i++) {
            $htmlContent .= "
            <div class='deviceInputs'>
                <input type='text' id='ppaID" . $i . "' name='ppaID" . $i . "' value='" . $this->locationsAndNotes[$i]['ppaID'] . "_" . $this->locationsAndNotes[$i]['timeControlMillis'] . "' style='display: none'/>
                <div class='input-group'>
                    <label for='locationPP" . $i . "'>Lokacija uredjaja</label>
                    <input type='text' id='locationPP" . $i . "' name='locationPP" . $i . "' value='" . $this->locationsAndNotes[$i]['locationPP'] . "'>
                </div>
                <div class='input-group'>
                    <label for='subType" . $i . "'>Tip Uredjaja</label>
                    <input type='text' id='subType" . $i . "' name='subType" . $i . "' value='" . $this->devices[$i]->getPpaparatDictionaryValue('subType'). "'>
                </div>
                <div class='input-group'>
                    <label for='fabricId" . $i . "'>Fabricki broj</label>
                    <input type='text' id='fabricId" . $i . "' name='fabricId" . $i . "' value='" . $this->devices[$i]->getPpaparatDictionaryValue('fabricId'). "'>
                </div>
                <div class='input-group'>
                    <label for='creationYear" . $i . "'>Godina proizvodnje</label>
                    <input type='text' id='creationYear" . $i . "' name='creationYear" . $i . "' value='" . $this->devices[$i]->getPpaparatDictionaryValue('creationYear'). "'>
                </div>
                <div class='input-group'>
                    <label for='manufacturerData" . $i . "'>Proizvodjac</label>
                    <input type='text' id='manufacturerData" . $i . "' name='manufacturerData" . $i . "' value='" . $this->devices[$i]->getPpaparatDictionaryValue('manufacturerData'). "'>
                </div>
                <div class='input-group'>
                    <label for='locationPP" . $i . "'>Ispravnost</label>
                    <div class='nested-radios'>
                        <input type='radio' name='curState" . $i . "' value='in order' " . (($this->locationsAndNotes[$i]['curState'] === 'in order')?"checked":"") . ">Ispravan
                        <input type='radio' name='curState" . $i . "' value='out of order' " . (($this->locationsAndNotes[$i]['curState'] === 'out of order')?"checked":"") . ">Neispravan<br>
                    </div>
                </div>
                <div class='input-group'>
                    <label for='note" . $i . "'>Napomena</label>
                    <input type='text' id='note" . $i . "' name='note" . $i . "' value='" . $this->locationsAndNotes[$i]['note'] . "'>
                </div>
            </div>";
        }

        $htmlContent .= "</div><br>";
        echo $htmlContent;
    }

    public function updateDB($dataArray) {
        $this->updateDBSpisakUredjaja($dataArray);
    }

    public function updateDBSpisakUredjaja($dataArray) {
        for ($i = 0; $i < count($this->getDevices()); $i++) {
            $idAndTimeControlMillis = explode("_", $dataArray['ppaID' . $i]);
            $sqlForSviUredjajiControlHistory = "UPDATE sviuredjajicontrolhistory
            SET locationPP='" . $dataArray['locationPP' . $i] . "', 
                note='" . $dataArray['note' . $i] . "', 
                curState='" . $dataArray['curState' . $i] . "'
            WHERE ppaID=" . $idAndTimeControlMillis[0] . " AND timeControlMillis=" . $idAndTimeControlMillis[1];
            $sqlForPpaparati = "UPDATE ppaparati
                                SET fabricId='" . $dataArray['fabricId' . $i] . "',
                                    manufacturerData='" . $dataArray['manufacturerData' . $i] . "',
                                    creationYear=" . $dataArray['creationYear' . $i] . ",
                                    subType='" . $dataArray['subType' . $i] . "'
                                WHERE sviUredjajiId=" . $idAndTimeControlMillis[0];

            DataBase::executionQuery($sqlForSviUredjajiControlHistory);
            DataBase::executionQuery($sqlForPpaparati);
        }
    }

}