<?php
include_once __DIR__. DS . 'Session.php';
include_once __DIR__ . DS . 'Db.php';
class Db_auth extends Db{
    public function __construct()
    {
        parent::__construct();
    }

    public function addUser($userData){
        $data = $userData;
        $email = $data['email'];
        $pass = $data['pass'];
        $username = $data['username'];
        //Check if the user exists
        $userExists = "SELECT * FROM users WHERE username=:username OR email=:email";
        $pdo = $this->connect();
        $stmt = $pdo->prepare($userExists);
        $stmt->execute([':username'=>$username, ':email'=>$email]);
        $res = $stmt->rowCount();
        if($res>0){
            $this->errors['exists']=true;
            print_r(json_encode($this->errors));
            exit();
        }

        // Inserting a new user
        $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :pass)";
        $pdo = $this->connect();
        $stmt = $pdo->prepare($sql);
        // print_r($username);
        // exit();
        $stmt->execute([':username'=>$username, ':email'=>$email, ':pass'=>$pass]);    
    }

    public function log_in($userData){
        $email = $userData['email'];
        $pass = $userData['pass'];

        
        $sql = "SELECT * FROM users WHERE email=:email";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([':email' => $email]);
        //If the user exists
        if($stmt->rowCount()>0){
            $user=$stmt->fetch();
            if(password_verify($pass,$user['password'])){

                //Set session
                Session::setSession($user);

                //Returning user that is logged in
            } else{
                print_r(json_encode("Invalid password!"));
            }
        } else{
            print_r(json_encode('Invalid email!'));
        }
    }
}