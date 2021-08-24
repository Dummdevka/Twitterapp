<?php


class Db{
    protected $host;
    protected $user;
    protected $pass;
    protected $dbName;

    public function __construct()
    {   
        //When the class gets called, it opens the config file

        $config = BASEDIR .DS. 'includes'.DS.'config.php';
        if(!file_exists($config)){
            echo $config;
            echo "No config";
            exit();
        } else{

            //Getting data from the config
            $dbData = require ($config);

            $this->host = $dbData['host'];
            $this->user = $dbData['user'];
            $this->pass = $dbData['pass'];
            $this->dbName = $dbData['dbName'];

            
        }
    }

    public function connect(){
        try{
            //Connecting
            $opt = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            );
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbName;
            $pdo = new PDO($dsn, $this->user, $this->pass, $opt);
            //The actual connection
            return $pdo;

        } catch(Exception $e){
            echo "No db connection:" . $e->getCode();
            exit();
        }
    //Show tweets
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

    public function insert_tweet($tweet, $username){
        $sql = "INSERT INTO tweets (username,tweet) VALUES (:username,:tweet)";
        $pdo = $this->connect();

        $stmt=$pdo->prepare($sql);
        $stmt->execute([':tweet' => $tweet, ':username' => $username]);
    }

    //Delete tweets

    public function delete_tweet($id){
        $sql = "DELETE FROM tweets WHERE id=:id";
        $pdo = $this->connect();

        $stmt=$pdo->prepare($sql);
        $stmt->execute([':id'=> $id]);
    }

}