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


        //check that there is a valid refresh token
        if($this->checkToken()===false){
            //Grab it
            $refresh = $_COOKIE['refresh'];
            try{
                //Decode and take User id from it
                $token = $this->decode($refresh);
                $this->id = $token->data->uniqid;
            } catch(Exception $e){
                //Error 403, invalid token
                $this->setStatus(403, $e->getMessage());
                exit();
            }
            //Get user data from DB by id
            $data = $this->db_auth->getUser($this->id);
            
            //Save the data
            $this->username = $data['username'];
            $this->email = $data['email'];
            $this->pass = $data['password'];

            //Just pass the data to display it
            if(strcmp($_SERVER['REQUEST_METHOD'],'GET')===0){
                echo json_encode($data);
            }
        //Change username
        if(isset($_GET['action'])&&strcmp($_GET['action'], 'changeUsername')===0){
            $this->changeUsername($this->id);
            exit();
        }
        //Change pass
        if(isset($_GET['action'])&&strcmp($_GET['action'], 'changePass')===0){
            $this->changePass($this->id, $this->pass);
            exit();
        }

        //Delete account
        if(isset($_GET['action'])&&strcmp($_GET['action'], 'deleteUser')===0){
            $this->deleteUser($this->id);
            exit();
        }
        } else{
            //There is no access token
            echo json_encode($this->checkToken());
        }
        
    }
    public function changeUsername($id){
        //Get post data from user
        if(!empty($this->getPostData())){
                $postData = $this->getPostData();
                $username = $postData->username;

                //This data will be passed to the refresh token for future use
                $refreshData = [
                    //Hash id (not the primary key)
                    'uniqid' => $id,
                    'username' => $username
                ];

                //Check that username is valid
                if($this->validateUsername($username)){
                    //Change the data in DB
                    if($this->db_auth->changeUsername($id,$username)){
                        //Refresh data in the refresh token
                        $this->setRefreshJwt($refreshData);
                        //Return data
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
        //Grab user data
        $postData = $this->getPostData();
        if($postData){
            $oldPass = $postData->old;
            $newPass = $postData->new;
            //Compare passwords
            if(password_verify($oldPass, $userPass)){
                if($this->validatePass($newPass)){
                    //Hash the new pass
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
                //Wrong old pass
                $this->setStatus(405, "Wrong old pass!");
                exit();
            }
        }else{
            $this->setStatus(404, "Some errors");
            exit();
        }
    }

    public function deleteUser($id){
        //Delete user from DB
        if($this->db_auth->delete_user($id)){
            echo json_encode(true);
        } else{
            $this->setStatus(404, "Unable to delete this profile");
            exit();
        }
    }
}