<?php

require_once __DIR__ . DS . 'Base.php';

class Account extends BaseController{
    public $username;
    public $email;
    public $id ;
    protected $pass;
    public function __construct($db_tweets, $db_auth)
    {
        parent::__construct($db_tweets, $db_auth);
        //Take refresh token and find the user by id
        if($this->checkToken()===false){
            $refresh = $_COOKIE['refresh'];
            try{
                $token = $this->decode($refresh);
                $this->id = $token->data->id;
            } catch(Exception $e){
                $this->setStatus(403, $e->getMessage());
                exit();
            }
            // echo $this->id;
            // exit();
            $data = $this->db_auth->getUser($this->id);
            // var_dump ($data['id']);
            // exit();
            $this->username = $data['username'];
            $this->email = $data['email'];
            $this->pass = $data['password'];

            if(strcmp($_SERVER['REQUEST_METHOD'],'GET')===0){
                echo json_encode($data);
            }
            //Change username
        if(isset($_GET['action'])&&strcmp($_GET['action'], 'changeUsername')===0){
            $this->changeUsername($this->id);
            exit();
        }
        if(isset($_GET['action'])&&strcmp($_GET['action'], 'changePass')===0){
            $this->changePass($this->id, $this->pass);
            exit();
        }
        //Change email
        //Delete account
        } else{
            echo json_encode($this->checkToken());
        }
        
    }
    public function changeUsername($id){
        $userId = $id;
        $rawPostData = file_get_contents('php://input');
        $postData = json_decode($rawPostData);
        if(!empty($postData->username)){
            
            $username = $postData->username;
            if($this->validateUsername($username)){
                if($this->db_auth->changeUsername($userId,$username)){
                    echo json_encode($username);
                } else{
                    return false;
                }
            }
        }else{
            $this->setStatus(404, "Invalid username");
            exit();
        }
        
    }
    public function changePass($id, $pass){
        $userId = $id;
        $userPass = $pass;

        $rawPostData = file_get_contents('php://input');
        $postData = json_decode($rawPostData);
        // var_dump($postData->new);
        // exit();
        if(!empty($postData->old) && !empty($postData->new)){
            $oldPass = $postData->old;
            $newPass = $postData->new;
            // var_dump('here');
            // exit();
            //check old password
            if(password_verify($oldPass, $userPass)){
                if($this->validatePass($newPass)){
                    $newPassHash = password_hash($newPass, PASSWORD_DEFAULT);
                    //Update pass
                    if($this->db_auth->change_pass($newPassHash, $userId)){
                        echo json_encode(true);
                    } else{
                        $this->setStatus(404, "Errors occured while changing password");
                        exit();
                    }
                }
            } else{
                $this->setStatus(405, "Wrong old pass!");
                //exit();
            }
        } else{
            $this->setStatus(404, "Some errors");
            exit();
        }
    }
    public function deleteAccount(){
        
    }
}