<?php
require_once __DIR__ .DS. 'Base.php';

class Auth extends BaseController{
    protected $username;
    protected $email;
    protected $pass;
    public $errors = [];

    public function __construct($db_tweets, $db_auth)
    {
        parent::__construct($db_tweets, $db_auth);
        if(isset($_GET['action'])&& strcmp($_GET['action'], 'signup')===0){
            $this->signUp();
        }
        if(isset($_GET['action'])&& strcmp($_GET['action'], 'login')===0){
            $this->logIn();
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
                print_r(json_encode($this->errors));
                exit();
            }
            $newUserData = [
                'username' => $this->username,
                'email' => $this->email,
                'pass' => $this->pass
            ];
            $this->db_auth->addUser($newUserData);
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

                $this->db_auth->log_in($loginData);
            } else {
                $this->errors[] = 'Some fields are empty!';
            }
        } else {
            $this->errors[] = 'Some troubles from the server side!';
        }
        if(!empty($this->errors)){
            print_r($this->errors);
            exit();
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