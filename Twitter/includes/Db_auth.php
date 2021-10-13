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

    //This function checks if username is unique
    public function uniqUsername($username){
        $sql = "SELECT * FROM users WHERE username=:username";

        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([':username'=>$username]);

        if($stmt->rowCount()>0){
            return false;
        } else{
            return true;
        }
    }

    //This function gets user by uniqID 
    public function getUser($id){
        $sql = "SELECT * FROM users WHERE uniqid=:id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([':id'=>$id]);
        $res = $stmt->fetch();
        if($res===0){
            return false;
        }
        return($res);
        
    }

    //This function checks if the user exists based on username and email (log in)
    public function userExists($data){
        $userExists = "SELECT * FROM users WHERE username=:username OR email=:email";
        $pdo = $this->connect();
        $stmt = $pdo->prepare($userExists);
        $stmt->execute($data);
        $res = $stmt->rowCount();
        if ($res > 0) {
            return true;
            exit();
        }
        else{
            return false;
        }
    }

    //This function sends data to the database to add new user
    public function addUser($userData)
    {
        $email = $userData['email'];
        $pass = $userData['pass'];
        $username = $userData['username'];
        $uniqId = uniqid();
        //Check if the user exists
        $data = [':username' => $username, ':email' => $email];
        if($this->userExists($data)){
            $this->setStatus(403, "User already exists!");
            exit();
        }
        // Inserting a new user
        $sql = "INSERT INTO users (uniqid, username, email, password) VALUES (:uniqid, :username, :email, :pass)";
        $pdo = $this->connect();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':uniqid'=>$uniqId, ':username' => $username, ':email' => $email, ':pass' => $pass]);
        print_r(json_encode( $userData));
        
        
    }

    //This function loggs user in
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
                $this->setStatus(422, 'Invalid pass, sorry :(');
                exit();
            }
        } else {
            $this->setStatus(422, 'This email does not exist, sorry:(');
            exit();
        }
    }

    //This function updates username based on uniqID 
    public function changeUsername($id, $username){
        $queryData1 = [
            'table' => 'users',
            'field' => 'username',
            'val' => $username,
            'field2'=>'uniqid',
            'val2' => $id,
        ];
        $queryData2 = [
            'table' => 'tweets',
            'field' => 'username',
            'val' => $username,
            'field2'=>'userid',
            'val2' => $id,
        ];
        
        //Change username in all the tweets
        if($this->uniqUsername($username)){
            if($this->update($queryData1)&&$this->update($queryData2)){
                return true;
        } else {
            return false;
        }
    } 
}

    //This func changes password
    public function change_pass($newPass, $id){
        $queryData = [
            'table' => 'users',
            'field' => 'password',
            'val' => $newPass,
            'field2' => 'uniqid',
            'val2' => $id,
        ];
        if(strlen($newPass)!==0){
            if($this->update($queryData)){
                return true;
            } 
        }
    }

    //This func deletes the user
    public function delete_user($id){
        try{
            $sql = "DELETE from users WHERE id=:id";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([':id'=>$id]);
            return true;
        } catch(Exception $e){
            return false;
        }
        
    }
}
