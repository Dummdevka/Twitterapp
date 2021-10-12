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
        $postData = $this->getPostData();
        
        if(!empty(trim($postData->username)) && !empty(trim($postData->email)) && !empty(trim($postData->pass))){
            if(($this->validateUsername($postData->username)===true)&&($this->validateEmail($postData->email)===true)&&($this->validatePass($postData->pass)===true)){
                $password = password_hash($postData->pass, PASSWORD_DEFAULT);
                $this->username = $postData->username;
                $this->email = $postData->email;
                $this->pass = $password;
            } else {
                $this->setStatus(422, "Invalid data, sorry :(");
                exit();
            }
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
        } else{
            $this->setStatus(422, "Invalid data, sorry :(");
            exit();
        }
    }
    public function logIn(){
        $postData = $this->getPostData();
        
            if(!empty(trim($postData->email))&&!empty(trim($postData->pass))){
                $this->email = trim($postData->email);
                $this->pass = trim($postData->pass);

                $loginData = [
                    'email'=> $this->email,
                    'pass'=> $this->pass,
                ];

                $user = $this->db_auth->log_in($loginData);
                if($user){

                }
                $this->setAccessJwt($user);
                //httpOnly cookie
                $this->setRefreshJwt($user);
                //new Account($this->db_tweets, $this->db_tweets, $user);
                exit();
            } else {
                $this->setStatus(403, 'Some fields are empty!');
                exit();
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
            return false;
        }
    }
    
    
}