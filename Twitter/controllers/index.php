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
            // if (isset($_GET['action']) && strcmp($_GET['action'],'saveImage')==0){
            //     var_dump($_FILES);
            //     exit();
            //         if($this->checkToken()===false){
            //             $this->saveImg();
            //             exit();
            //         } else{
            //             return false;
            //         }
                
            //}
            print_r(json_encode($this->db_tweets->get_tweets()));
       
    }
    public function saveImg($imageData){

        $ext = substr($imageData['type'], 6);
        // var_dump($ext);
        // exit();
        //Unique name
        $name = time().'-'.uniqid(rand()). '.' . $ext;
        
        //Where the picture should be stored
        $dir = "/Applications/XAMPP/xamppfiles/htdocs/TwitterApp/TestAng/src/assets/img";
        $dest = $dir . DS . $name;
        $from = $imageData['tmp_name'];
        move_uploaded_file($from, $dest);

        $imageData['name'] = $name;
        $imageData['tmp_name'] = $dest;
        return $imageData;
    }
    public function sendTweet()
    {
        if(!empty($_POST['tweet']&&!empty($_POST['username']))){

            $tweet = trim($_POST['tweet']);
            $username = trim($_POST['username']);

            //If there is a picture attached
            if(!empty($_FILES['tweet-attachments'])){
                $imageData = $_FILES['tweet-attachments'];
                $regex = '/^image\/(jpg)|(jpeg)|(png)/';
                //Checking that file is an image
                if(preg_match($regex,$imageData['type'])){
                    $imageData = $this->saveImg($imageData);
                } else {
                    $this->setStatus(415, "Invalid image format :(");
                    exit();
                }
                
            } else{
                $imageData = [];
            }
            //Send tweet data to the database
            try{
                if($this->db_tweets->insert_tweet_data($this->id, $tweet, $username, $imageData)){
                    return true;
                } else{
                    var_dump($this->db_tweets->insert_tweet_data($this->id, $tweet, $username, $imageData));
                    $this->setStatus(500, "Internal error");
                    exit();
                }
            } catch (Exception $e){
                //Delete image if needed
                $this->deleteImage($imageData);
                $this->setStatus(500, "Server errors");
                exit();
            }
        } else {
            $this->setStatus(422, 'Some errors, try again later!');
        }
    }
    
    
    public function deleteImage($imageData){
        if(!empty($imageData['file_name'])&&!empty($imageData['url'])){
            unlink($imageData['url']);
        }
        
    }
    public function deleteTweet()
    {
        $id = trim($_GET['id']);

        //Deleting tweet from the database
        $res = $this->db_tweets->delete_tweet($id);
        // var_dump($res['url']);
        // exit();
        //Checking if there are images attached
            if(!empty($res['url'])&&!empty($res['file_name'])){
                $this->deleteImage($res);
            } 
        } 
    }
