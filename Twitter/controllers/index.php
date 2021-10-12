<?php
require_once __DIR__ .DS. 'Base.php';
require_once __DIR__ .DS. 'auth.php';
require_once "vendor/firebase/php-jwt/src/ExpiredException.php";
use Firebase\JWT\JWT;
class Index extends BaseController
{
    public function __construct($db_tweets, $db_auth)
    {
        
        parent::__construct($db_tweets, $db_auth);

        
            if (isset($_GET['action']) && strcmp($_GET['action'],'add')==0) {
                //Send tweet to the db
                if($this->checkToken()===false){
                    $this->sendTweet();
                } else{
                    return false;
                }
            }
            if (isset($_GET['action']) && strcmp($_GET['action'],'delete')==0){
                if (isset($_GET['id'])) {
                    if($this->checkToken()===false){
                        $this->deleteTweet();
                    } else{
                        //var_dump($this->checkToken());
                        return false;
                    }
                }
            }
            if (isset($_GET['action']) && strcmp($_GET['action'],'saveImage')==0){
                var_dump($_FILES);
                exit();
                    if($this->checkToken()===false){
                        $this->saveImg();
                        exit();
                    } else{
                        return false;
                    }
                
            }
            //var_dump($_FILES);
            print_r(json_encode($this->db_tweets->get_tweets()));
       
    }
    public function saveImg(){
        
       var_dump($_FILES);
    }
    public function sendTweet()
    {
        if(!empty($_POST['tweet']&&!empty($_POST['username']))){

            $tweet = trim($_POST['tweet']);
            $username = trim($_POST['username']);

            //If there is a picture attached
            if(!empty($_FILES['tweet-attachments'])){
                //Save that picture
                $imageData = $_FILES['tweet-attachments'];
                $name = trim($imageData['name']);
                //$altered_name = $
                //var_dump($altered_name . '  ' . $name);
                
                $dir = "/Applications/XAMPP/xamppfiles/htdocs/TwitterApp/TestAng/src/assets/img";
                $dest = $dir . DS . $name;
                $from = $imageData['tmp_name'];
                move_uploaded_file($from, $dest);

                $imageData['tmp_name'] = $dest;
            } else{
                $imageData = [];
            }
            //Send tweet data to the database
            $this->db_tweets->insert_tweet($this->id, $tweet, $username, $imageData);
        } else {
            $this->setStatus(422, 'Some errors, try again later!');
        }
    }
    
    

    public function deleteTweet()
    {
        $id = trim($_GET['id']);

        //Deleting tweet from the database
        $this->db_tweets->delete_tweet($id);
        
    }
}
