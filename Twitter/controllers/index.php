<?php
require_once __DIR__ .DS. 'Base.php';

class Index extends BaseController
{
    public function __construct($db_tweets, $db_auth)
    {
        parent::__construct($db_tweets, $db_auth);
        //If a tweet has been added
        if (isset($_GET['action']) && strcmp($_GET['action'],'add')==0) {
            //Send tweet to the db
            
            $this->sendTweet();
        }
        if (isset($_GET['action']) && strcmp($_GET['action'],'delete')==0){
            if (isset($_GET['id'])) {
                $this->deleteTweet();
            }
        }
        //Otherwise just get tweets
        $res = $this->db_tweets->get_tweets();
        echo json_encode($res);
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
            print_r($postData);
        }

    }

    public function deleteTweet()
    {
        $id = trim($_GET['id']);

        //Deleting tweet from the database
        $this->db_tweets->delete_tweet($id);
        
    }
}
