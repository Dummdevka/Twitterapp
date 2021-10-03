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
                $this->id = $token->data->uniqid;
            } catch(Exception $e){
                $this->setStatus(403, $e->getMessage());
                exit();
            }
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
        if(isset($_GET['action'])&&strcmp($_GET['action'], 'deleteUser')===0){
            $this->deleteUser($this->id);
            exit();
        }
        } else{
            echo json_encode($this->checkToken());
        }
        
    }
    public function changeUsername($id){
        //Get post data
        if(!empty($this->getPostData())){
                $postData = $this->getPostData();
                $username = $postData->username;
                $refreshData = [
                    'uniqid' => $id,
                    'username' => $username
                ];
                if($this->validateUsername($username)){
                    if($this->db_auth->changeUsername($id,$username)){
                        //Refresh data in the refresh token
                        $this->setRefreshJwt($refreshData);
                        echo json_encode($username);
                    } else{
                        return false;
                    }
                } else {
                    echo json_encode("invalid");
                }
            }
        }
        
    public function changePass($id, $pass){
        $userId = $id;
        $userPass = $pass;
        $postData = $this->getPostData();
        if($postData){
            $oldPass = $postData->old;
            $newPass = $postData->new;
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
            }else{
                    $this->setStatus(405, "Wrong old pass!");
                    exit();
                }
        }else{
            $this->setStatus(404, "Some errors");
            exit();
        }
    }
    public function deleteUser($id){
        if($this->db_auth->delete_user($id)){
            echo json_encode(true);
        } else{
            $this->setStatus(404, "Unable to delete this profile");
            exit();
        }
    }
}