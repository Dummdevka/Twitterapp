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
        
            if(isset($_GET['action'])&& strcmp($_GET['action'], 'refresh')===0){
                //var_dump(getallheaders());
                $headers = getallheaders();
                if(!isset($headers['Authorization'])){
                    $this->setStatus(505, 'here');
                    exit();
                }
                if($this->checkToken()===false){
                    //Valid access token
                    return false;
                    exit();
                } else{
                    try{
                        $this->getNewAccess();
                    } catch(Exception $e){
                        $this->setStatus(403, $e->getMessage());
                    }
                }
            }

            if(isset($_GET['action'])&& strcmp($_GET['action'], 'signup')===0){
                $this->signUp();
                exit();
            }
            if(isset($_GET['action'])&& strcmp($_GET['action'], 'login')===0){
                $this->logIn();
                exit();
            }
        
        //Clear cookie anyway 
        if(isset($_GET['action'])&& strcmp($_GET['action'], 'clear')===0){
            $this->clearCookie();
            exit();
        }
    }
    
    public function signUp(){
        $rawPostData = file_get_contents('php://input');

        $postData = json_decode($rawPostData);
        
        if($postData->username && $postData->email && $postData->pass){
            $this->validateUsername($postData->username);
            $this->validateEmail($postData->email);
            $this->validatePass($postData->pass);
            if(!empty($this->errors)){
                //Access forbidden
                $this->setStatus(403, $this->errors);
                exit();
            }
            $newUserData = [
                'username' => $this->username,
                'email' => $this->email,
                'pass' => $this->pass
            ];
            $data = $this->db_auth->addUser($newUserData);
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
                $this->setAccessJwt($user);
                //httpOnly cookie
                $this->setRefreshJwt($user);
                new Account($this->db_tweets, $this->db_tweets, $user);
                exit();
            } else {
                $this->setStatus(403, 'Some fields are empty!');
            }
        }
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
            setcookie("refresh", $jwtRefresh, time()+3600*24, '/', '' ,false, true);
        } catch (Exception $e){
            print_r("Error:".$e->getMessage());
            exit();
        }
        //

    }
    
    public function clearCookie(){
        if(isset($_COOKIE['refresh'])){
            
            //Remove the cookie
            unset($_COOKIE['refresh']);
            setcookie('refresh', null, -1, '/');
            echo json_encode(true);
            exit();
        } else{
            return false;
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