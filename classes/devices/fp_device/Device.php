<?php
/**
 * Created by PhpStorm.
 * User: sentinel
 * Date: 11/1/18
 * Time: 12:57 PM
 */

abstract class Device {
    protected $id;
    protected $deviceDictionary;

    /**
     * Device constructor.
     * @param $id
     * @param $barcodeID
     * @param $objectID
     * @param $dateTimeMillis
     */
    public function __construct() { }

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function getDeviceDictionaryValue($key) {
        return $this->deviceDictionary[$key];
    }

    public static function getDeviceTypeById($id) {
        $sql = "SELECT type FROM sviuredjaji WHERE id=$id";
        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['type'];
        }
    }

    public static function getBarcodeIDById($id) {
        $sql = "SELECT allowedBarcodes as barcodeID FROM barcodes WHERE id=(SELECT barcodeID
                                                              FROM sviuredjaji
                                                              WHERE id=$id)";
        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return (int)$row['barcodeID'];
        }
        return null;
    }

    public static function fetchCompleteDeviceObject($id, $type) {
        $device = DevicesContext::getInstance()->pickDevice($type);
        $device->setId($id);
        $device->getBasicInfo();
        $device->getExtendedInfo();

        return $device;
    }

    public function getBasicInfo() {
        $sql = "SELECT B.allowedBarcodes as barcodeID, S.objectID as objectID, S.type as type, S.creationTimeMillis as creationTimeMillis, S.lastControlMillis as lastControlMillis 
                FROM sviuredjaji S, barcodes B 
                WHERE S.barcodeID=B.id AND S.id=$this->id";

        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->deviceDictionary = $row;
        }
    }

    public abstract function getExtendedInfo();

    public function printBasicData() {
        echo "<tr>";

        echo "<td>" . $this->getId() . "</td>";
        echo "<td>" . $this->getDeviceDictionaryValue('barcodeID') . "</td>";
        echo "<td>" . $this->getDeviceDictionaryValue('objectID') . "</td>";
        echo "<td>" . $this->getDeviceDictionaryValue('type') . "</td>";
        echo "<td>" . date("D d M Y H:i:s", (int)($this->getDeviceDictionaryValue('creationTimeMillis') / 1000)) . "</td>";
        echo "<td>" . date("D d M Y H:i:s", (int)($this->getDeviceDictionaryValue('lastControlMillis') / 1000)) . "</td>";

        echo "</tr>";
    }

    public abstract function printExtendedData();

    public function toHTMLBasicInfo() {
        $htmlContent = "<div>
                            <div class='input-group'>
                                <input type='hidden' id='editDeviceId' name='editDeviceId' value='" . $this->id . "'>
                            </div>
                            <div class='input-group'>
                                <label for=''>Barkod:</label><input type='text'  id='barcodeID' name='barcodeID' placeholder='" . $this->deviceDictionary['barcodeID'] . "'>
                            </div>
                            <div class='input-group'>
                                <label for=''>Tip:</label><em>" . $this->deviceDictionary['type'] . "</em>
                            </div>
                            <div class='input-group'>
                                <label for=''>Vreme instalacije:</label><input type='text' id='creationTimeMillis' name='creationTimeMillis' placeholder='" . date("D d M Y H:i:s", (int)($this->deviceDictionary['creationTimeMillis']/1000)) . "'>
                            </div>
                            <div class='input-group'>
                                <label for=''>Vreme poslednje kontrole:</label><input type='text' id='lastControlMillis' name='lastControlMillis' placeholder='" . date("D d M Y H:i:s", (int)($this->deviceDictionary['lastControlMillis']/1000)) . "'>
                            </div>
                        ";
        return $htmlContent;
    }

    public abstract function toHTMLExtendedInfo();

    public function updateBasicInfoInDB($basicInfo) {
        $sql = "UPDATE sviuredjaji SET objectID=$basicInfo[2],
                                       creationTimeMillis=$basicInfo[3],
                                       lastControlMillis=$basicInfo[4]
                                   WHERE id=$basicInfo[0]";

        DataBase::executionQuery($sql);

        $sql = "UPDATE barcodes SET allowedBarcodes=$basicInfo[1]
                                WHERE id=(SELECT barcodeID FROM sviuredjaji WHERE id=$basicInfo[0])";

        DataBase::executionQuery($sql);
    }

    public abstract function updateExtendedInfoInDB($extendedInfo);

    public function fetchAllBasicControlHistory() {
        $controlHistories = [];
        $sql = "SELECT operatorID, longitude, latitude, timeControlMillis, note, imgPath, locationPP, curState
                FROM sviuredjajicontrolhistory
                WHERE ppaID=$this->id";

        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0)
            while ($row = $result->fetch_assoc())
                $controlHistories []= $row;

        return $controlHistories;
    }

    public abstract function fetchAllExtendedControlHistory();

    public abstract function printExtendedControlHistory($controlHistory);

}