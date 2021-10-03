<?php
 include_once __DIR__ . DS . 'Db.php';

class Db_tweets extends Db{
    
    public function __construct()
    {
        parent::__construct();
    }
    public function get_tweets(){
        $sql = "SELECT * FROM tweets ORDER BY id DESC ";
        $pdo = $this->connect();

        //Execute the query
        $stmt=$pdo->prepare($sql);
        $stmt->execute(); 

        //Fetch the result
        $res = $stmt->fetchAll();

        return $res;
    }

    //Add new tweets
    public function insert_tweet($uniqid, $tweet, $username){
        // var_dump($uniqid);
        // exit();
        //$username = $this->getId($username);
        $sql = "INSERT INTO tweets (userid, username,tweet) VALUES (:userid, :username,:tweet)";
        $pdo = $this->connect();

        $stmt=$pdo->prepare($sql);

        $stmt->execute([':userid'=>$uniqid,':tweet' => $tweet, ':username' => $username]);
    }

    //Delete tweets

    public function delete_tweet($id){
        $sql = "DELETE FROM tweets WHERE id=:id";
        $pdo = $this->connect();

        $stmt=$pdo->prepare($sql);
        $stmt->execute([':id'=> $id]);
    }

}