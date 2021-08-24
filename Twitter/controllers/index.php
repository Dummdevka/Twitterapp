<?php
require_once __DIR__ .DS. 'Base.php';

class Index extends BaseController
{
    public function __construct($pdo)
    {
        parent::__construct($pdo);
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
        $res = $this->db->get_tweets();
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
            $this->db->insert_tweet($tweet, $username);
        } else{
            print_r($postData);
        }

    }

    public function deleteTweet()
    {
        $id = trim($_GET['id']);

        //Deleting tweet from the database
        $this->db->delete_tweet($id);
        
    }
}
