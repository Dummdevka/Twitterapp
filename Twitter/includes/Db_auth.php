<?php

include_once __DIR__ . DS . 'Db.php';
class Db_auth extends Db{
    public function __construct()
    {
        parent::__construct();
    }

    public function addUser($userData){

        $username = $userData['username'];
        $email = $userData['email'];
        $pass = $userData['pass'];
        $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :pass)";
        $pdo = $this->connect();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username'=>$username, ':email'=>$email, ':pass'=>$pass]);
    }
    public function log_in($userData){
        $email = $userData['email'];
        $pass = $userData['pass'];

        
        $sql = "SELECT * FROM users WHERE email=:email";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([':email' => $email]);
        if($stmt->rowCount()>0){
            $user=$stmt->fetch();
            if(password_verify($pass,$user['password'])){
                print_r(json_encode("Logged in"));
            } else{
                print_r(json_encode("Invalid password!"));
            }
        } else{
            print_r(json_encode('Invalid email!'));
        }
    }
}