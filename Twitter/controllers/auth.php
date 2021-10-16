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

            //Validate access token
            if(isset($_GET['action'])&& strcmp($_GET['action'], 'refresh')===0){
                //Get headers from the http request
                $headers = getallheaders();

                //If there is no Access token
                if(!isset($headers['Authorization'])){
                    $this->setStatus(505, 'You are not allowed to access that page:(');
                    exit();
                }
                //Check if access token is valid
                if($this->checkToken()===false){
                    //Valid access token
                    return false;
                    exit();
                } else{
                    //Get new one
                    try{
                        $this->getNewAccess();
                    } catch(Exception $e){
                        //Errors refreshing token
                        $this->setStatus(403, $e->getMessage());
                    }
                }
            }

            //Signup
            if(isset($_GET['action'])&& strcmp($_GET['action'], 'signup')===0){
                $this->signUp();
                exit();
            }

            //Log in
            if(isset($_GET['action'])&& strcmp($_GET['action'], 'login')===0){
                $this->logIn();
                exit();
            }
        
            //Clear cookie on log out (refresh token)
            if(isset($_GET['action'])&& strcmp($_GET['action'], 'clear')===0){
                $this->clearCookie();
                exit();
            }
    }
    
    public function signUp(){

        $postData = $this->getPostData();
        
        //Validate input
        if(!empty(trim($postData->username)) && !empty(trim($postData->email)) && !empty(trim($postData->pass))){
            if($this->validateUsername($postData->username)){
                if($this->validateEmail($postData->email)){
                    if($this->validatePass($postData->pass)){
                        //Save the data from user
                        $password = password_hash($postData->pass, PASSWORD_DEFAULT);
                        
                        $this->username = $postData->username;
                        $this->email = $postData->email;
                        $this->pass = $password;

                        //This data will be sent to the DB
                        $newUserData = [
                            'username' => $this->username,
                            'email' => $this->email,
                            'pass' => $this->pass
                        ];

                        //Add new user
                        try{
                            $this->db_auth->addUser($newUserData);
                        } catch (Exception $e){
                            $this->setStatus(500, $e->getMessage());
                            exit();
                        }
                    } else{
                        $this->setStatus(422, "Invalid pass :(");
                    }
                } else{
                    $this->setStatus(422, "Invalid email :(");
                    exit();
                }
            } else{
                $this->setStatus(422, "Invalid username :(");
                exit();
            }
        //Errors getting input
        } else{
            $this->setStatus(422, "We couldn't fetch your data :(");
            exit();
        }
    }
    public function logIn(){
        $postData = $this->getPostData();
            //Validate input
            if(!empty(trim($postData->email))&&!empty(trim($postData->pass))){
                $this->email = trim($postData->email);
                $this->pass = trim($postData->pass);

                //This data will be sent to the database
                $loginData = [
                    'email'=> $this->email,
                    'pass'=> $this->pass,
                ];

                $user = $this->db_auth->log_in($loginData);
                
                //New access token
                $this->setAccessJwt($user);
                //httpOnly cookie (refresh token)
                $this->setRefreshJwt($user);
                exit();
            } else {
                $this->setStatus(403, 'Some fields are empty!');
                exit();
            }
    }
   
    //Clear refresh token in httpOnly cookies
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