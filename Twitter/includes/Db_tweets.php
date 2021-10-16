<?php
 include_once __DIR__ . DS . 'Db.php';

class Db_tweets extends Db{
    
    public function __construct()
    {
        parent::__construct();
    }
    public function get_tweets(){
        // $sql = "SELECT * FROM tweets ORDER BY id DESC ";
        $sql = "SELECT tweets.id, file_name, username, tweet, userid, time FROM attachments
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
    public function insert_tweet($pdo, $data){
        $sql = "INSERT INTO tweets (userid, username, tweet) VALUES (:userid, :username, :tweet)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);

        return $pdo->lastInsertId();
    }
    public function insert_image($pdo, $data){
        $sql = "INSERT INTO attachments (tweet_id, file_name, url) VALUES (:tweetid, :filename, :url)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
    }
    //Add new tweets
    public function insert_tweet_data($uniqid, $tweet, $username, $image = []){
        $pdo = $this->connect();
        $tweet_data = [
            ':userid'=>$uniqid,
            ':username'=>$username,
            ':tweet'=>$tweet
        ];
        //If there is an image
        if(!empty($image)){
            try{
                $pdo->beginTransaction();

                //Upload tweet
                $tweetid = $this->insert_tweet($pdo, $tweet_data);

                $image_data = [
                    ':tweetid'=>$tweetid,
                    ':filename'=>$image['name'],
                    ':url'=>$image['tmp_name']
                ];
                //Upload image
                $this->insert_image($pdo, $image_data);
                $pdo->commit();
                return true;
            } catch (Exception $e){
                $pdo->rollBack();
                return false;
            }
            
        } else {
            try{
                $this->insert_tweet($pdo, $tweet_data);
                return true;
            } catch(Exception $e){
                return false;
            }
        }
    }
        
    public function insert_attachments($id, $imageInfo){
        $data = [
            ':url'=>$imageInfo['tmp_name'],
            ':name'=>$imageInfo['name'],
            ':id'=>$id,
        ];
        
        $sql = "INSERT INTO attachments (tweet_id,file_name, url) VALUES (:id, :name, :url)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute($data);
    }
    public function getAttachments($id){
        $sql = "SELECT url, file_name FROM attachments  WHERE tweet_id=:id";
        $stmt= $this->connect()->prepare($sql);
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch();
    }
    //Delete tweets
    public function delete_tweet($id){
        $imageData = $this->getAttachments($id);
        $sql = "DELETE FROM tweets WHERE id=:id
        ";
        $pdo = $this->connect();

        $stmt=$pdo->prepare($sql);
        $stmt->execute([':id'=> $id]);
        return $imageData;
    }

}