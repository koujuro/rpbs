<?php
/**
 * Created by PhpStorm.
 * User: sentinel
 * Date: 3/18/19
 * Time: 3:19 PM
 */

abstract class User {
    protected $id;
    protected $companyID;
    protected $fullname;
    protected $username;
    protected $password;
    protected $userType;
    protected $millisCreated;
    protected $licenceNumber;
    protected $allowAccess;

    public function __construct() {}

    public function setParametersFromDB($user) {
        $this->id = $user['id'];
        $this->companyID = $user['companyID'];
        $this->fullname = $user['fullname'];
        $this->username = $user['username'];
        $this->password = $user['password'];
        $this->userType = $user['userType'];
        $this->millisCreated = $user['millisCreated'];
        $this->licenceNumber = $user['licenceNumber'];
        $this->allowAccess = $user['allowAccess'];
    }

    public abstract function setParametersFromPOST($data, $companyID);

    protected function getLastUserIdFromDB() {
        $sql = "SELECT id FROM users ORDER BY id DESC LIMIT 1";
        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['id'];
        }
        return null;
    }

    public function insertNewUserIntoDB() {
        $sql = "INSERT INTO `users`(`id`, `companyID`, `fullname`, 
                                    `username`, `password`, `userType`, 
                                    `millisCreated`, `licenceNumber`, `allowAccess`) 
                VALUES (" . $this->id . "," . $this->companyID . ",'" . $this->fullname . "',
                '" . $this->username . "','" . $this->password . "','" . $this->userType . "',
                '" . $this->millisCreated . "','" . $this->licenceNumber . "', " . $this->allowAccess . ")";

        DataBase::executionQuery($sql);
    }
}