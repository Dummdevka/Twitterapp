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

            //Add new tweet
            if (isset($_GET['action']) && strcmp($_GET['action'],'add')==0) {
            //Always check that user is logged in
                if($this->checkToken()===false){
                //Send tweet to the db
                    $this->sendTweet();
                } else{
                    return false;
                }
            }

            //Delete tweet
            if (isset($_GET['action']) && strcmp($_GET['action'],'delete')==0){
                if (isset($_GET['id'])) {
                //Authorization check
                    if($this->checkToken()===false){
                        $this->deleteTweet();
                    } else{
                        return false;
                    }
                }
            }

            //Get tweets from database (if no special actions)
            print_r(json_encode($this->db_tweets->get_tweets()));
       
    }

    //Save uploaded image to harddrive
    public function saveImg($imageData){

        //Extension
        $ext = substr($imageData['type'], 6);

        //Unique name
        $name = time().'-'.uniqid(rand()). '.' . $ext;
        
        //Where the picture should be stored
        $dir = "/Applications/XAMPP/xamppfiles/htdocs/TwitterApp/TestAng/src/assets/img";
        $dest = $dir . DS . $name;
        $from = $imageData['tmp_name'];
        move_uploaded_file($from, $dest);

        //Save new imgName and url
        $imageData['name'] = $name;
        $imageData['tmp_name'] = $dest;
        return $imageData;
    }

    //Add new tweet
    public function sendTweet()
    {
        //Tweet is send via FormData so it is in POST
        if(!empty($_POST['tweet']&&!empty($_POST['username']))){

            //Save post data
            $tweet = trim($_POST['tweet']);
            $username = trim($_POST['username']);

            //If there is a picture attached
            if(!empty($_FILES['tweet-attachments'])){
                $imageData = $_FILES['tweet-attachments'];
                $regex = '/^image\/(jpg)|(jpeg)|(png)/';
                //Checking that file is an image
                if(preg_match($regex,$imageData['type'])){
                    //Save to harddrive
                    $imageData = $this->saveImg($imageData);
                } else {
                    $this->setStatus(415, "Invalid image format :(");
                    exit();
                }
            } else{
                //No images attached
                $imageData = [];
            }

            //Send tweet data to the database
            try{
                if($this->db_tweets->insert_tweet_data($this->id, $tweet, $username, $imageData)){
                    return true;
                } else{
                    $this->setStatus(500, "Internal error");
                    exit();
                }
            } catch (Exception $e){

                //Delete image from harddrive if needed 
                $this->deleteImage($imageData);
                $this->setStatus(500, "Server errors");
                exit();
            }
        } else {
            $this->setStatus(422, 'Some errors, try again later!');
        }
    }
    
    //If there is any pictures in parameters -> delete 
    public function deleteImage($imageData){
        if(!empty($imageData['file_name'])&&!empty($imageData['url'])){
            unlink($imageData['url']);
        }
        
    }

    //Delete tweet
    public function deleteTweet()
    {
        $id = trim($_GET['id']);

        //Deleting tweet from the database
        $res = $this->db_tweets->delete_tweet($id);
        
        //Checking if there are images attached
            if(!empty($res['url'])&&!empty($res['file_name'])){
                //Delete img from harddrive
                $this->deleteImage($res);
            } 
        } 
    }
