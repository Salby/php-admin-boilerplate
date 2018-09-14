<?php
spl_autoload_register(function ($class) {
    include COREROOT . '/classes/' . strtolower($class). '.php';
});