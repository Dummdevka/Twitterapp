<?php

class Session{
    protected $username;
    protected $email;

    public function __construct()
    {
    }

    static function setSession($sessionData){
        $username = $sessionData['username'];
        $email = $sessionData['email'];

        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
    
    }

    static function unsetSession(){
        session_unset();
        session_destroy();
    }

    //Check sessions 
    static function checkSession(){
        if(isset($_SESSION['username']) && isset($_SESSION['email'])){
            return true;
        } else {
            return false;
        }
    }
}