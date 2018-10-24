<?php
/*
 * DB Configuration with credentials
 * Should be ignored from any Git services
 */
class db_conf extends db {
    function __construct() {
        $this->dbhost = "host";
        $this->dbuser = "user";
        $this->dbpassword = "password";
        $this->dbname = "dbname";
        $db = parent::connect();
    }
}
