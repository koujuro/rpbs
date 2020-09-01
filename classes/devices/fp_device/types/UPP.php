<?php
/**
 * Created by PhpStorm.
 * User: sentinel
 * Date: 11/1/18
 * Time: 1:30 PM
 */

class UPP extends Device {
    private $uppDictionary;

    public function __construct() {}

    public function getUppDicitonaryValue($key) {
        return $this->uppDictionary[$key];
    }

    public function getExtendedInfo() {
        $sql = "SELECT name
                FROM upp
                WHERE PPAsId=$this->id";

        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->uppDictionary = $row;
        }
    }

    public function printExtendedData() {
        echo "<tr>";
        echo "<th>Naziv</th>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>" . $this->getUppDicitonaryValue('name') . "</td>";
        echo "</tr>";
    }

    public function toHTMLExtendedInfo() {
        $htmlContent = "<div class='input-group'>
                            <label for=''>Naziv:</label><input type='text' id='name' name='name' placeholder='" . $this->uppDictionary['name'] . "'>
                        </div>";

        return $htmlContent;
    }

    public function updateExtendedInfoInDB($extendedInfo) {
        $sql = "UPDATE upp SET name='" . $extendedInfo['name'] . "'
                                       WHERE PPAsId=$this->id";

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

        $sql = "SELECT inputPressure, outputPressure
                FROM uppcontrol
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
                <th>Ulazni pritisak</th>
                <th>Izlazni pritisak</th>
              </tr>

              <tr>
              <td>" . (($controlHistory['pressureState'] === 'satisfies')?"Pritisak zadovoljava":"Pritisak ne zadovoljava") . "</td>
              <td>" . $controlHistory['noteDataPressure'] . "</td>
              <td>" . $controlHistory['inputPressure'] . "</td>
              <td>" . $controlHistory['outputPressure'] . "</td>
              </tr>";

        return $htmlContent;
    }

}