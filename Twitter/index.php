<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');
require_once "vendor/autoload.php";
use \Firebase\JWT\JWT;
//Errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//Defining constants
define("DS", DIRECTORY_SEPARATOR);
define("BASEDIR", __DIR__ . DS);
define("URL", 'http://localhost/twitter');
//define("TEMPS", BASEDIR. 'templates');
//define("VIEWS", BASEDIR. 'views');

//Including files
require_once BASEDIR . 'includes'.DS.'Db_tweets.php';
require_once BASEDIR . 'includes'.DS.'Db_auth.php';
//require_once BASEDIR. 'includes'.DS.'View.php';

//Routing
$routes_arr = [
    'index',
    'auth'
];

//Checking the GET 
$route = strtolower($_GET['page'] ?? 'index');

//Compare $route to the array values
if(!in_array($route, $routes_arr)){
    $route = 'index';
}

//Creating Db and Template
$db_tweets = new Db_tweets();
$db_auth = new Db_auth();
//Creating controller
$path = BASEDIR . 'controllers'.DS . $route . '.php';
//If file exists, then creating an object
if(file_exists($path)){
    $reqClass = require_once($path);
    $class = ucfirst($route);
    $controller = new $class($db_tweets, $db_auth);
} else{
    exit();
}
