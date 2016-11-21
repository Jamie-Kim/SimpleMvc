<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING | E_DEPRECATED));
//get user path
require './paths.php';

//custom settings
require CONFIG . DS . 'CustomSettings.php';

//settings
require CONFIG . DS . 'AppSettings.php';

//get core path
require CORE . DS . 'core_main.php';

//start the application main
require APP . DS . 'app_main.php';