<?php
 include_once __DIR__ . DS . 'Db.php';

class Db_tweets extends Db{
    
    public function __construct()
    {
        parent::__construct();
    }
    public function get_tweets(){
        // $sql = "SELECT * FROM tweets ORDER BY id DESC ";
        $sql = "SELECT tweets.id, url, username, tweet, userid, time FROM attachments
        RIGHT OUTER JOIN tweets
        ON attachments.tweet_id = tweets.id
        ORDER  BY tweets.id DESC;";

        $pdo = $this->connect();

        //Execute the query
        $stmt=$pdo->prepare($sql);
        $stmt->execute(); 

        //Fetch the result
        $res = $stmt->fetchAll();
        //Return tweets to the frontend
        return $res;
    }

    //Add new tweets
    public function insert_tweet($uniqid, $tweet, $username, $image = []){
        
        
        $sql = "INSERT INTO tweets (userid, username,tweet) VALUES (:userid, :username,:tweet)";
        //$getIdSql = "SELECT LAST_INSERT_ID()";
        $pdo = $this->connect();
        $stmt=$pdo->prepare($sql);

        $stmt->execute([':userid'=>$uniqid,':tweet' => $tweet, ':username' => $username]);

        //Checking if there are pictures attached
        if(!empty($image)){
            $lastTweetId = $pdo->lastInsertId();
            $this->insert_attachments($lastTweetId, $image);
        }
        
    }
    public function insert_attachments($id, $imageInfo){
        $url = $imageInfo['tmp_name'];
        $sql = "INSERT INTO attachments (tweet_id, url) VALUES (:id, :url)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([':url'=>$url, ':id'=>$id]);
    }
    //Delete tweets

    public function delete_tweet($id){
        $sql = "DELETE FROM tweets WHERE id=:id";
        $pdo = $this->connect();

        $stmt=$pdo->prepare($sql);
        $stmt->execute([':id'=> $id]);
    }

}