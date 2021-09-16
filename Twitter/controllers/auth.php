<?php
require_once __DIR__ .DS. 'Base.php';
require_once BASEDIR . 'includes' . DS . 'Session.php';
require_once __DIR__ . DS . 'account.php';
use Firebase\JWT\JWT;
class Auth extends BaseController{
    protected $username;
    protected $email;
    protected $pass;
    public $errors;

    public function __construct($db_tweets, $db_auth)
    {
        parent::__construct($db_tweets, $db_auth);
        if(isset($_GET['action'])&& strcmp($_GET['action'], 'signup')===0){
            $this->signUp();
            exit();
        }
        if(isset($_GET['action'])&& strcmp($_GET['action'], 'login')===0){
            $this->logIn();
            exit();
        }
        if(isset($_GET['action'])&& strcmp($_GET['action'], 'refresh')===0){
            if($this->checkToken()===false){
            $this->getNewAccess();
            exit();
            } else {
                print_r(json_encode(false));
            }
        }
        if(isset($_GET['action'])&& strcmp($_GET['action'], 'clear')===0){
            $this->clearCookie();
            exit();
        }
    }
    public function setStatus($message){
        http_response_code(422);
        print_r(json_encode($message));
    }
    public function signUp(){
        $rawPostData = file_get_contents('php://input');

        $postData = json_decode($rawPostData);
        
        if($postData->username && $postData->email && $postData->pass){
            $this->validateUsername($postData->username);
            $this->validateEmail($postData->email);
            $this->validatePass($postData->pass);
            if(!empty($this->errors)){
                $this->setStatus($this->errors);
                exit();
            }
            $newUserData = [
                'username' => $this->username,
                'email' => $this->email,
                'pass' => $this->pass
            ];
            $data = $this->db_auth->addUser($newUserData);
            //Create tokens and send them back
        }
    }
    public function logIn(){
        $rawPostData = file_get_contents('php://input');
        $postData = json_decode($rawPostData);
        if($postData->email && $postData->pass){
            if(!empty(trim($postData->email))&&!empty(trim($postData->pass))){
                $this->email = trim($postData->email);
                $this->pass = trim($postData->pass);

                $loginData = [
                    'email'=> $this->email,
                    'pass'=> $this->pass,
                ];

                $user = $this->db_auth->log_in($loginData);
                //LocalStorage
                // print_r($user);
                // exit();
                $this->setAccessJwt($user);
                //httpOnly cookie
                $this->setRefreshJwt($user);
                new Account($this->db_tweets, $this->db_tweets, $user);
                exit();
            } else {
                $this->setStatus('Some fields are empty!');
            }
        }
    }
    public function setAccessJwt(array $user){
        $issuer_claim = "http://localhost"; // this can be the servername
        $audience_claim = "http://localhost";
        $issuedat_claim = time(); // issued at
        $notbefore_claim = $issuedat_claim + 1; //not before in seconds
        $expire_claim = $issuedat_claim + 10; // expire time in seconds
        $access_token = array(
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "iat" => $issuedat_claim,
            "nbf" => $notbefore_claim,
            "exp" => $expire_claim,
            "data" => array(
                "id" => $user['id'],
                "username" => $user['username']
            )
        );
        $jwt = JWT::encode($access_token, $this->key);
        echo json_encode(
            array(
                "message" => "Successful login.",
                "jwt" => $jwt,
                "expire_at" => $expire_claim,
            )
        );
    }
    public function setRefreshJwt(array $user){
        $issuer_claim = "http://localhost"; // this can be the servername
        $audience_claim = "http://localhost";
        $issuedat_claim = time(); // issued at
        $notbefore_claim = $issuedat_claim + 0; //not before in seconds
        $refresh_token = array(
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "iat" => $issuedat_claim,
            "nbf" => $notbefore_claim,
            "data" => [
                "username" => $user['username'],
                "id"=>$user['id']
            ]
        );
        try{
            //Creating a refresh token
            $jwtRefresh = JWT::encode($refresh_token, $this->refresh);
            //Storing it to the httpOnly
            setcookie("refresh", $jwtRefresh, time()+3600, '/', '' ,false, true);
        } catch (Exception $e){
            print_r("Error:".$e->getMessage());
            exit();
        }
        //

    }
    public function getNewAccess(){
        if(isset($_COOKIE['refresh'])&& (!empty(trim($_COOKIE['refresh'])))){
            $refresh_token = $_COOKIE['refresh'];

        } else{
            exit();
        }
        
            try{
                //print_r(json_encode("here"));
                $decoded = JWT::decode($refresh_token, $this->refresh, array('HS256'));
                $user = (array) $decoded->data;
                $this->setAccessJwt($user);                
            } catch( Exception $e){
                print_r(json_encode($e->getMessage()));
            }
    }
    public function clearCookie(){
        if(isset($_COOKIE['refresh'])){
            
            //Remove the cookie
            unset($_COOKIE['refresh']);
            setcookie('refresh', null, -1, '/');
            echo json_encode(true);
            exit();
        } else{
            echo json_encode(false);
        }
    }
    public function validateUsername($data){
        $username = trim($data);
        if(strlen($username)>5&&strlen($username)<25){
            $this->username = $username;
        } else{
            $this->errors[]='Invalid username!';
        }
    }
    public function validateEmail($data){
        $email = trim($data);
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            $this->email = $email;
        } else {
            $this->errors[] = 'Invalid e-mail!';
        }
    }
    public function validatePass($data){
        $pass = trim($data);
        if(preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,20}$/', $pass)){
            $password = password_hash($pass, PASSWORD_DEFAULT);
            $this->pass = $password;
        } else {
            $this->errors[]='Invalid password!';
        }
    }
}