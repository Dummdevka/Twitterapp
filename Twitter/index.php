<?php
header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Methods, Access-Control-Allow-Headers, Access-Control-Allow-Origin, Authorization');
//header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Credentials: true');
//header ('Accept:multipart/form-data');
require_once "vendor/autoload.php";
use \Firebase\JWT\JWT;
//Errors
//ini_set('session.cookie_httponly', 1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//Handling pre-flight requests
if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
    header('Access-Control-Allow-Origin: http://localhost:4200');
    http_response_code(200);
    exit();
}

//Defining constants
define("DS", DIRECTORY_SEPARATOR);
define("BASEDIR", __DIR__ . DS);
//define("BASEDIRUPLOAD", __DIR__ . DS);
define("URL", 'http://localhost/twitter');
chmod('/Applications/XAMPP/xamppfiles/temp/',0777);
//chmod('/Applications/XAMPP/xamppfiles/htdocs/TwitterApp/uploadedFiles/',0777);

//define("TEMPS", BASEDIR. 'templates');
//define("VIEWS", BASEDIR. 'views');

//Including files
require_once BASEDIR . 'includes'.DS.'Db_tweets.php';
require_once BASEDIR . 'includes'.DS.'Db_auth.php';
//require_once BASEDIR. 'includes'.DS.'View.php';

//Routing
$routes_arr = [
    'index',
    'auth',
    'account'
];

//Checking the GET 
$route = strtolower($_GET['page'] ?? 'index');
//var_dump($_SERVER);
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
