<?php
/**
 * Created by PhpStorm.
 * User: sentinel
 * Date: 11/1/18
 * Time: 1:30 PM
 */

class PPAparat extends Device {
    private $ppaparatDictionary;

    public function __construct() {}

    public function getPpaparatDictionaryValue($key) {
        return $this->ppaparatDictionary[$key];
    }

    public function getExtendedInfo() {
        $sql = "SELECT fabricId, manufacturerData, creationYear, subType
                FROM ppaparati
                WHERE sviuredjajiId=$this->id";

        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->ppaparatDictionary = $row;
        }
    }

    public function printExtendedData() {
        echo "<tr>";
        echo "<th>Podtip</th>
        <th>Fabricki ID</th>
        <th>Podaci proizvodjaca</th>
        <th>Godina proizvodnje</th>";
        echo "</tr>";

        echo "<tr>";

        echo "<td>" . $this->getPpaparatDictionaryValue('subType') . "</td>";
        echo "<td>" . $this->getPpaparatDictionaryValue('fabricId') . "</td>";
        echo "<td>" . $this->getPpaparatDictionaryValue('manufacturerData') . "</td>";
        echo "<td>" . $this->getPpaparatDictionaryValue('creationYear') . "</td>";

        echo "</tr>";
    }

    public function toHTMLExtendedInfo() {
        $htmlContent = "<div class='input-group'>
                            <label for=''>Podtip:</label><input type='text' id='subType' name='subType' placeholder='" . $this->ppaparatDictionary['subType'] . "'>
                        </div>
                        <div class='input-group'>
                            <label for=''>Fabricki ID:</label><input type='text' id='fabricId' name='fabricId' placeholder='" . $this->ppaparatDictionary['fabricId'] . "'>
                        </div>
                        <div class='input-group'>
                            <label for=''>Podaci proizvodjaca:</label><input type='text' id='manufacturerData' name='manufacturerData' placeholder='" . $this->ppaparatDictionary['manufacturerData'] . "'>
                        </div>
                        <div class='input-group'>
                            <label for=''>Godina proizvodnje:</label><input type='text' id='creationYear' name='creationYear' placeholder='" . $this->ppaparatDictionary['creationYear'] . "'>
                        </div>
                        ";

        return $htmlContent;
    }

    public function updateExtendedInfoInDB($extendedInfo) {
        $sql = "UPDATE ppaparati SET fabricId='" . $extendedInfo['fabricId'] . "',
                                       manufacturerData='" . $extendedInfo['manufacturerData'] . "',
                                       creationYear=" . $extendedInfo['creationYear'] . ",
                                       subType='" . $extendedInfo['subType'] . "'
                                       WHERE sviuredjajiId=$this->id";

        DataBase::executionQuery($sql);
    }

    public function fetchAllExtendedControlHistory() {
        $controlHistories = [];
        $sql = "SELECT lastHVP, malfunctionType
                FROM ppaparaticontrol
                WHERE sviUredjajiControlId IN (SELECT id
                                            FROM sviuredjajicontrolhistory
                                            WHERE ppaID=$this->id)";

        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $controlHistories []= $row;

        return $controlHistories;
    }

    public function printExtendedControlHistory($controlHistory) {
        $htmlContent = "<tr class=\"headings\">
                <th>lastHVP</th>
                <th>malfunctionType</th>
              </tr>

              <tr>
              <td>" . $controlHistory['lastHVP'] . "</td>
              <td>" . $controlHistory['malfunctionType'] . "</td>
              </tr>";

        return $htmlContent;
    }

}