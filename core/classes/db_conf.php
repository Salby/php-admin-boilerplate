<?php
/*
 * DB Configuration with credentials
 * Should be ignored from any Git services
 */
class db_conf extends db {
    function __construct() {
        $this->dbhost = "localhost";
        $this->dbuser = "root";
        $this->dbpassword = "";
        $this->dbname = "cms_test";
        $db = parent::connect();
    }
}