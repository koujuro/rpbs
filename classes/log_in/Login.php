<?php

/**
 * Class RFLLogin - will login user.
 */
class Login {
    use Security;

    /**
     *  whatIsusername - string that tells you whether to login user's via email or username.
     */
    private $username;
    private $password;

    /**
     * Default RFLLogin constructor.
     */
    public function __construct() {
        $this->username = "";
        $this->password = "";
    }

    /**
     * Set parameters for login
     *
     * @param $username
     * @param $password
     */
    public function setParameters($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Check login parameters.
     *
     * @return bool
     */
    public function checkParameters() {
        try {
            $this->chekcFunctions();
            return true;
        } catch (\Exception $loginException) {
            echo $loginException->getMessage();
            return false;
        }
    }

    /**
     * Call of all check functions.
     *
     * @throws RFLAccountException
     * @throws RFLLoginException
     */
    private function chekcFunctions() {
        $this->checkusername();
        $this->checkPassword();
        $this->checkUserInDataBase();
    }

    /**
     * Check login parameter.
     *
     * @throws RFLLoginException
     */
    private function checkusername() {
        if(!$this->correctNames($this->username))
            throw new \Exception("One of credentials are not correct.");
    }

    /**
     * Check password.
     *
     * @throws RFLLoginException
     */
    private function checkPassword() {
        if(!$this->correctPasswords($this->password))
            throw new \Exception("One of credentials are not correct.");
    }

    private function checkUserInDataBase() {
        $sql = "SELECT username FROM users WHERE username='$this->username' AND password='$this->password' AND allowAccess=1";

        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows !== 1)
            throw new Exception("Username or password are not correct.");
    }

    private function checkCredentialsInDataBase($users) {
        foreach ($users as $user => $data) {
            if ($data['username'] === $this->username && $data['password'] === $this->password)
                return true;
        }
        return false;
    }

    /**
     * Login user - create session.
     */
    public function loginUser() {
        SessionUtilities::createSession('User', $this->username);
        /* Added userType */
        $this->setUsersTypeInSession();
    }

    public function setUsersTypeInSession() {
        $user = SessionUtilities::getSession('User');
        $sql = "SELECT userType FROM users WHERE username='$user' AND allowAccess=1";
        $result = DataBase::selectionQuery($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            SessionUtilities::createSession('UserType', $row['userType']);
        }
    }
}