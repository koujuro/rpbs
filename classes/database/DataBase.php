<?php

/**
 * Basic database_assets functions for execution and
 * selection with SQL queries.
 */
class DataBase {
    /**
     * Unique instance of this class.
     */
    private static $instance;

    /**
     * Connection variable for MySQL database_assets.
     */
    private $connection;

    /**
     * Private constructor so this class cannot be
     * instanced outside.
     */
    private function __construct() { }

    /**
     * Singleton method.
     */
    private static function Instance() {
        if (self::$instance === null)
            self::$instance = new DataBase();

        return self::$instance;
    }

    public static function executionMultiQuery($sql) {
        self::Instance();
        if (self::$instance->successfulConnection()) {
            self::$instance->executeMultiQuery($sql);
            //self::$instance->printError();
        }
        self::$instance->closeConn();
    }

    /**
     * Execution query, executes Insert, Update or Delete
     * statements.
     *
     * @param $sql
     * @return int
     */
    public static function executionQuery($sql) {
        $rowsAffected = 0;
        self::Instance();
        if (self::$instance->successfulConnection()) {
            self::$instance->executeQuery($sql);
            //self::$instance->printError();
            $rowsAffected = self::$instance->rowsAffected();
        }
        self::$instance->closeConn();
        return $rowsAffected;
    }

    /**
     * Connects and returns boolean regarding connection
     * status.
     */
    private function successfulConnection() {
        $this->connect();

        return ($this->connection->connect_error) ? false : true;
    }

    /**
     * Connects to database_assets.
     */
    private function connect() {
        $this->connection = new mysqli(
            DataBaseConfig::SERVER_NAME,
            DataBaseConfig::USERNAME,
            DataBaseConfig::PASSWORD,
            DataBaseConfig::DATABASE_NAME
        );
        // Setting charset to UTF-8
        $this->connection->set_charset("utf8");
    }

    /**
     * Executes SQL query
     *
     * @param $sql
     * @return mixed
     */
    private function executeQuery($sql) {
        return $this->connection->query($sql);
    }

    /**
     * Prints SQL error;
     */
    private function printError() {
        echo $this->connection->error;
    }

    /**
     * Closes connection to database_assets.
     */
    private function closeConn() {
        $this->connection->close();
    }

    private function rowsAffected() {
        return $this->connection->affected_rows;
    }

    private function executeMultiQuery($sql) {
        return $this->connection->multi_query($sql);
    }

    /**
     * The function will execute selection query and return result of selection.
     *
     * @param $sql
     * @return string
     */
    public static function selectionQuery($sql) {
        self::Instance();
        $result = "";

        if (self::$instance->successfulConnection()) {
            $result = self::$instance->executeQuery($sql);
            //self::$instance->printError();
        }
        self::$instance->closeConn();

        return $result;
    }

}
