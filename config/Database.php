<?php

namespace Config;

class Database
{
    private $_connection;
    private static $_instance;
    private $_host = "localhost";
    private $_username = "root";
    private $_password = "secret";
    private $_database = "babyv2";

    private function __construct()
    {
        $this->_connection = new mysqli($this->_host, $this->_username,
        $this->_password, $this->_database);

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
}