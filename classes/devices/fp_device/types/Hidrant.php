<?php
/**
 * Created by PhpStorm.
 * User: sentinel
 * Date: 11/1/18
 * Time: 1:30 PM
 */

class Hidrant extends Device {
    private $hidrantDictionary;

    public function __construct() {}

    public function getHidrantDictionaryValue($key) {
        return $this->hidrantDictionary[$key];
    }

    public function getExtendedInfo() {
        $sql = "SELECT hMark, subType
                FROM hidranti
                WHERE sviuredjajiId=$this->id";

        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->hidrantDictionary = $row;
        }

    }

    public function printExtendedData() {
        echo "<tr>";
        echo "<th>Podtip</th>
        <th>Oznaka hidranta</th>";
        echo "</tr>";

        echo "<tr>";

        echo "<td>" . $this->getHidrantDictionaryValue('subType') . "</td>";
        echo "<td>" . $this->getHidrantDictionaryValue('hMark') . "</td>";

        echo "</tr>";
    }

    public function toHTMLExtendedInfo() {
        $htmlContent = "<div class='input-group'>
                            <label for=''>Podtip:</label><input type='text' id='subType' name='subType' placeholder='" . $this->hidrantDictionary['subType'] . "'>
                        </div>
                        <div class='input-group'>
                            <label for=''>H oznaka:</label><input type='text' id='hMark' name='hMark' placeholder='" . $this->hidrantDictionary['hMark'] . "'>
                        </div>                         
                        ";

        return $htmlContent;
    }

    public function updateExtendedInfoInDB($extendedInfo) {
        $sql = "UPDATE hidranti SET hMark='" . $extendedInfo['hMark'] . "',
                                    subType='" . $extendedInfo['subType'] . "'
                                    WHERE sviuredjajiId=$this->id";

        DataBase::executionQuery($sql);
    }

    public function fetchAllExtendedControlHistory() {
        $controlHistories = [];
        $separatedControlHistories = [];

        $sql = "SELECT pressureState, noteDataPressure
                FROM hidrantiuppcontrol
                WHERE PPAControlId IN (SELECT id
                                    FROM sviuredjajicontrolhistory
                                    WHERE ppaID=$this->id )";
        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $controlHistories []= $row;

        $sql = "SELECT staticPressure, dynamicPressure, m3hNetwork, lsNetwork, gapeDiameter
                FROM hidranticontrol
                WHERE HiUPPId IN (SELECT id
                               FROM hidrantiuppcontrol
                               WHERE PPAControlId IN (SELECT id
                                                   FROM sviuredjajicontrolhistory
                                                   WHERE ppaID=$this->id ))";
        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $separatedControlHistories []= $row;

        return $this->mergeSharedAndSeparatedControlHistories($controlHistories, $separatedControlHistories);
    }

    private function mergeSharedAndSeparatedControlHistories($controlHistories, $separatedControlHistories) {
        for ($i = 0; $i < count($controlHistories); $i++) {
            foreach ($separatedControlHistories[$i] as $key => $value) {
                $controlHistories[$i][$key] = $value;
            }
        }

        return $controlHistories;
    }

    public function printExtendedControlHistory($controlHistory) {
        $htmlContent = "<tr class=\"headings\">
                <th>Stanje pritiska</th>
                <th>Beleska o pritisku</th>
                <th>Staticki pritisak</th>
                <th>Dinamicki pritisak</th>
                <th>Mreza m3h</th>
                <th>Mreza ls</th>
                <th>Precnik usnika</th>
              </tr>

              <tr>
              <td>" . (($controlHistory['pressureState'] === 'satisfies')?"Pritisak zadovoljava":"Pritisak ne zadovoljava") . "</td>
              <td>" . $controlHistory['noteDataPressure'] . "</td>
              <td>" . $controlHistory['staticPressure'] . "</td>
              <td>" . $controlHistory['dynamicPressure'] . "</td>
              <td>" . $controlHistory['m3hNetwork'] . "</td>
              <td>" . $controlHistory['lsNetwork'] . "</td>
              <td>" . $controlHistory['gapeDiameter'] . "</td>
              </tr>";

        return $htmlContent;
    }

}