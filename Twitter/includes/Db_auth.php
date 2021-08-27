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
}