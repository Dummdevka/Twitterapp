<?php

require_once __DIR__ . DS . 'Base.php';

class Account extends BaseController{
    public $username;
    public $email;
    public $id ;

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
            if(strcmp($_SERVER['REQUEST_METHOD'],'GET')===0){
                echo json_encode($data);
            }
            //Change username
        if(isset($_GET['action'])&&strcmp($_GET['action'], 'changeUsername')===0){
            $this->changeUsername($this->id);
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
    public function changeEmail(){
        
    }
    public function deleteAccount(){
        
    }
}