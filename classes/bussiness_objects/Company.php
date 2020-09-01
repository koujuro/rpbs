<?php
/**
 * Created by PhpStorm.
 * User: sentinel
 * Date: 3/18/19
 * Time: 2:29 PM
 */

class Company {
    private $id;
    private $companyName;
    private $uniqueNumber;
    private $numAllowedBarcodes;
    private $request;
    private $street;
    private $number;
    private $city;
    private $PAC;
    private $phoneNumber;
    private $webSite;
    private $eMail;
    private $officePhoneNumber;
    private $servicePhoneNumber;
    private $mobilePhoneNumber;
    private $numberATS;
    private $controlLicenceNumber;
    private $imgName;
    private $numOfUsedBarcodes;

    public function __construct() {}

    public function setParametersFromDB($company) {
        $this->id = $company['id'];
        $this->companyName = $company['companyName'];
        $this->uniqueNumber = $company['uniqueNumber'];
        $this->numAllowedBarcodes = $company['numAllowedBarcodes'];
        $this->request = $company['request'];
        $this->street = $company['street'];
        $this->number = $company['number'];
        $this->city = $company['city'];
        $this->PAC = $company['PAC'];
        $this->phoneNumber = $company['phoneNumber'];
        $this->webSite = $company['webSite'];
        $this->eMail = $company['eMail'];
        $this->officePhoneNumber = $company['officePhoneNumber'];
        $this->servicePhoneNumber = $company['servicePhoneNumber'];
        $this->mobilePhoneNumber = $company['mobilePhoneNumber'];
        $this->numberATS = $company['numberATS'];
        $this->controlLicenceNumber = $company['controlLicenceNumber'];
        $this->imgName = $company['imgName'];
    }

    public function setParametersFromPOST($data, $imgName) {
        $this->id = $this->getLastCompanyIdFromDB() + 1;
        $this->companyName = $data['companyName'];
        $this->uniqueNumber = $data['uniqueNumber'];
        $this->numAllowedBarcodes = $data['numAllowedBarcodes'];
        $this->request = 0;
        $this->street = $data['street'];
        $this->number = $data['number'];
        $this->city = $data['city'];
        $this->PAC = $data['PAC'];
        $this->phoneNumber = $data['phoneNumber'];
        $this->webSite = $data['webSite'];
        $this->eMail = $data['eMail'];
        $this->officePhoneNumber = $data['officePhoneNumber'];
        $this->servicePhoneNumber = $data['servicePhoneNumber'];
        $this->mobilePhoneNumber = $data['mobilePhoneNumber'];
        $this->numberATS = $data['numberATS'];
        $this->controlLicenceNumber = $data['controlLicenceNumber'];
        $this->imgName = $imgName;
    }

    public function fetchCompanyFromDB() {
        $currentUser = SessionUtilities::getSession('User');
        $sql = "SELECT *
            FROM companies 
            WHERE id=(SELECT companyID FROM users WHERE username='$currentUser')";

        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        }

        return $row;
    }

    private function getLastCompanyIdFromDB() {
        $sql = "SELECT id FROM companies ORDER BY id DESC LIMIT 1";
        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['id'];
        }
        return null;
    }

    public function insertNewCompanyIntoDB() {
        $sql = "INSERT INTO `companies`(`id`, 
                                    `companyName`, `uniqueNumber`, `numAllowedBarcodes`, 
                                    `request`, `street`, `number`, 
                                    `city`, `PAC`, `phoneNumber`, 
                                    `webSite`, `eMail`, `officePhoneNumber`, 
                                    `servicePhoneNumber`, `mobilePhoneNumber`, 
                                    `numberATS`, `controlLicenceNumber`, `imgName`)
            VALUES (" . $this->id . ",'".$this->companyName."'," . $this->uniqueNumber . ",
                    " . $this->numAllowedBarcodes . "," . 0 . ",'" . $this->street . "',
                    " . $this->number . ",'" . $this->city . "'," . $this->PAC . ",
                    '" . $this->phoneNumber . "','" . $this->webSite . "',
                    '" . $this->eMail . "','" . $this->officePhoneNumber . "',
                    '" . $this->servicePhoneNumber . "','" . $this->mobilePhoneNumber . "',
                    " . $this->numberATS . "," . $this->controlLicenceNumber . ",
                    '" . $this->imgName . "')";

        DataBase::executionQuery($sql);
    }

    public function updateCompanyInDB($data, $imgName) {
        if ($imgName === "")
            $imgName = $this->getImgNameFromDB($data['companyName']);
        $currentUser = SessionUtilities::getSession('User');
        $sql = "UPDATE `companies` SET `companyName`='" . $data['companyName'] . "',
                                  `street`='" . $data['street'] . "',
                                  `number`=" . $data['number'] . ",
                                  `city`='" . $data['city'] . "',
                                  `PAC`='" . $data['PAC'] . "',
                                  `phoneNumber`='" . $data['phoneNumber'] . "',
                                  `webSite`='" . $data['webSite'] . "',
                                  `eMail`='" . $data['eMail'] . "',
                                  `officePhoneNumber`='" . $data['officePhoneNumber'] . "',
                                  `servicePhoneNumber`='" . $data['servicePhoneNumber'] . "',
                                  `mobilePhoneNumber`='" . $data['mobilePhoneNumber'] . "',
                                  `numberATS`=" . $data['numberATS'] . ",
                                  `controlLicenceNumber`=" . $data['controlLicenceNumber'] . ",
                                  `imgName`='$imgName' 
                                  WHERE id=(SELECT companyID FROM users WHERE username='$currentUser')";

        DataBase::executionQuery($sql);
    }

    private function getImgNameFromDB($companyName) {
        $sql = "SELECT imgName FROM companies WHERE companyName='$companyName'";
        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo $row['imgName'] . "<br>";
            return $row['imgName'];
        }
        return null;
    }

    public static function fetchAllCompaniesFromDB() {
        $companies = [];
        $sql = "SELECT id, companyName, numAllowedBarcodes, street, number, city
                FROM companies";

        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $company = new Company();
                $company->setBasicParametersFromDB($row);
                $company->getNumOfUsedBarcodesFromDB();
                $companies []= $company;
            }
        }

        return $companies;
    }

    public function setBasicParametersFromDB($company) {
        $this->id = $company['id'];
        $this->companyName = $company['companyName'];
        $this->numAllowedBarcodes = $company['numAllowedBarcodes'];
        $this->street = $company['street'];
        $this->number = $company['number'];
        $this->city = $company['city'];
    }

    public function getNumOfUsedBarcodesFromDB() {
        $sql = "SELECT COUNT(*) as numOfUsedBarcodes
                FROM sviuredjaji
                WHERE objectID IN (SELECT id
                                   FROM objects
                                   WHERE clientID IN (SELECT id
                                                      FROM clients
                                                      WHERE companyID=" . $this->id . "))";
        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->numOfUsedBarcodes = (int)$row['numOfUsedBarcodes'];
        }
    }

    public function printBasicCompanyInfoInTable() {
        echo "<tr>
                <td>" . $this->companyName . "</td>
                <td>" . (($this->street !== "" && $this->city !== "")?$this->street . " " . $this->number . ", " . $this->city:" - ") . "</td>
                <td>" . $this->numAllowedBarcodes . "</td>
                <td>" . $this->numOfUsedBarcodes . "</td>
              </tr>";
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCompanyName()
    {
        return $this->companyName;
    }

    public function getUniqueNumber()
    {
        return $this->uniqueNumber;
    }

    public function getNumAllowedBarcodes()
    {
        return $this->numAllowedBarcodes;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getStreet()
    {
        return $this->street;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function getPAC()
    {
        return $this->PAC;
    }

    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function getWebSite()
    {
        return $this->webSite;
    }

    public function getEMail()
    {
        return $this->eMail;
    }

    public function getOfficePhoneNumber()
    {
        return $this->officePhoneNumber;
    }

    public function getServicePhoneNumber()
    {
        return $this->servicePhoneNumber;
    }

    public function getMobilePhoneNumber()
    {
        return $this->mobilePhoneNumber;
    }

    public function getNumberATS()
    {
        return $this->numberATS;
    }

    public function getControlLicenceNumber()
    {
        return $this->controlLicenceNumber;
    }

    public function getImgName()
    {
        return $this->imgName;
    }

    public function getNumOfUsedBarcodes()
    {
        return $this->numOfUsedBarcodes;
    }


}