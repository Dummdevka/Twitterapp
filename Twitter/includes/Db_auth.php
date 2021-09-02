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
        print_r(json_encode($userData));
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

                //Set session
                //Session::setSession($user);
                $secret_key = "D91303F61B40A52C1E8E060A93E59944CC6E3D4F8D50C6795F45DB209736E03E";
                $issuer_claim = "http://localhost"; // this can be the servername
                $audience_claim = "http://localhost";
                $issuedat_claim = time(); // issued at
                $notbefore_claim = $issuedat_claim + 10; //not before in seconds
                $expire_claim = $issuedat_claim + 360; // expire time in seconds
                $token = array(
                    "iss" => $issuer_claim,
                    "aud" => $audience_claim,
                    "iat" => $issuedat_claim,
                    "nbf" => $notbefore_claim,
                    "exp" => $expire_claim,
                    "data" => array(
                        "id" => $user['id'],
                        "username" => $user['username']
                    )
                );
                $jwt = JWT::encode($token, $secret_key);
                echo json_encode(
                    array(
                        "message" => "Successful login.",
                        "jwt" => $jwt,
                        "expireAt" => $expire_claim
                    )
                );
                //Returning user that is logged in
                //print_r(json_encode($user));
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
