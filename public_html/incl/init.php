<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define document root
define("DOCROOT", filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING));

// Define core root
define("COREPATH", substr(DOCROOT, 0, strrpos(DOCROOT, "/")) . "/core");

// Define vendor root.
define("VENDORPATH", substr(DOCROOT, 0, strrpos(DOCROOT, '/')) . '/vendor');

// Class autoloader
require_once(COREPATH . '/classes/auto_loader.php');

// Packages autoload.
require_once(VENDORPATH . '/autoload.php');

// Initialize database
$db = new db_conf();

// Initialize WIP CRUD package.
$crud = new \salby\cruddery\cruddery($db);

// Initialize auth (login not required).
$auth = new auth(false);
$auth -> authenticate();
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $auth -> logout();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}