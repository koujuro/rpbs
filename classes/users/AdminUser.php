<?php
/**
 * Created by PhpStorm.
 * User: sentinel
 * Date: 3/18/19
 * Time: 3:27 PM
 */

class AdminUser extends User {
    public function setParametersFromPOST($data, $companyID) {
        $this->id = $this->getLastUserIdFromDB() + 1;
        $this->companyID = $companyID;
        $this->fullname = $data['newUserFirstName'] . " " . $data['newUserLastName'];
        $this->username = $data['newUserUsername'];
        $this->password = $data['newUserPassword'];
        $this->userType = 'admin';
        $this->millisCreated = time() * 1000;
        $this->licenceNumber = $data['newUserLicenceNumber'];
        $this->allowAccess = 1;
    }
}