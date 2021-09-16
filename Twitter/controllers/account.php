<?php

require_once __DIR__ . DS . 'Base.php';

class Account extends BaseController{
    public $username;
    public $email;
    public $id;

    public function __construct($db_tweets, $db_auth, array $user)
    {
        parent::__construct($db_tweets, $db_auth);
        $this->username = $user['username'];
        $this->email = $user['email'];
        $this->id = $user['id'];
        //Change username
        //Change email
        //Delete account
    }
    public function changeUsername(){

    }
    public function changeEmail(){
        
    }
    public function deleteAccount(){
        
    }
}