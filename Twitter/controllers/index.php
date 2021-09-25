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
                        return false;
                    }
                }
            }
            print_r(json_encode($this->db_tweets->get_tweets()));
       
    }
    
    public function sendTweet()
    {
        $rawPostData = file_get_contents('php://input');
        $postData = json_decode($rawPostData);
        if ($postData->username && $postData->tweet) {
            //No whitespaces in the beginning 
            $tweet = trim($postData->tweet);
            $username = trim($postData->username);

            //Sending tweet to the db
            $this->db_tweets->insert_tweet($tweet, $username);
        } else{
            $this->setStatus(422, "Invalid tweet");
        }

    }

    public function deleteTweet()
    {
        $id = trim($_GET['id']);

        //Deleting tweet from the database
        $this->db_tweets->delete_tweet($id);
        
    }
}
