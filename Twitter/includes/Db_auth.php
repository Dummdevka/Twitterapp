<?php
include_once __DIR__ . DS . 'Session.php';
include_once __DIR__ . DS . 'Db.php';

use Firebase\JWT\JWT;

class Db_auth extends Db
{
    public function __construct()
    {
        parent::__construct();
    }

    public function addUser($userData)
    {
        try{
            $email = $userData['email'];
        $pass = $userData['pass'];
        $username = $userData['username'];
        //Check if the user exists
        $userExists = "SELECT * FROM users WHERE username=:username OR email=:email";
        $pdo = $this->connect();
        $stmt = $pdo->prepare($userExists);
        $stmt->execute([':username' => $username, ':email' => $email]);
        $res = $stmt->rowCount();
        if ($res > 0) {
            http_response_code(422);
            print_r(json_encode("User exists"));
            exit();
        }

        // Inserting a new user
        $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :pass)";
        $pdo = $this->connect();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username' => $username, ':email' => $email, ':pass' => $pass]);
        print_r(json_encode( $userData));
        } catch( Exception $e){
            print_r(json_encode($e->getMessage()));
            exit();
        }
        
    }

    public function log_in($userData)
    {
        $email = $userData['email'];
        $pass = $userData['pass'];


        $sql = "SELECT * FROM users WHERE email=:email";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([':email' => $email]);
        //If the user exists
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            if (password_verify($pass, $user['password'])) {
                return $user;
                exit();
            } else {
                http_response_code(422);
                print_r(json_encode("Invalid pass"));
            }
        } else {
            http_response_code(422);
            print_r(json_encode("Invalid email"));
        }
    }
}
