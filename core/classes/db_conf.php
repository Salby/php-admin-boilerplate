<?php
/*
 * DB Configuration with credentials
 * Should be ignored from any Git services
 */
class db_conf extends db {
    function __construct() {
        $this->dbhost = "sql.itcn.dk:3306";
        $this->dbuser = "sand4782.SKOLE";
        $this->dbpassword = "7gAF41fgW1";
        $this->dbname = "sand47823.SKOLE";
        $db = parent::connect();
    }
}