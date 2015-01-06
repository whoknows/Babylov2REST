<?php

namespace Config;

class Database
{
    private $_connection;
    private static $_instance;
    private $_host = DB_HOST;
    private $_username = DB_USER;
    private $_password = DB_PASSWORD;
    private $_database = DB_DATABASE;

    private function __construct()
    {
        $this->_connection = new \PDO('mysql:host='.$this->_host.';dbname='.$this->_database.';', $this->_username, $this->_password);

        if(mysqli_connect_error()) {
            trigger_error("Failed to conencto to MySQL: " . mysql_connect_error(), E_USER_ERROR);
        }
    }

    private function __clone() { }

    public static function getInstance()
    {
        if(!self::$_instance) { // If no instance then make one
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getConnection() {
        return $this->_connection;
    }
}