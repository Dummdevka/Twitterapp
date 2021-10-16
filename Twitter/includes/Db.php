<?php

abstract class Db{
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
    }
   
    //Updates data based on uniqId
    public function update(array $queryData){
        $table = $queryData['table'];
        $field = $queryData['field'];
        $val = $queryData['val'];
        $field2 = $queryData['field2'];
        $val2 = $queryData['val2'];
        try{
            $sql = "UPDATE $table SET $field=:$field WHERE $field2=:field2";
            $data = [":$field"=>$val,
            ":field2"=>$val2];

            $stmt = $this->connect()->prepare($sql);
            $stmt->execute($data);
            return true;
        } catch(Exception $e){
            return $e->getMessage();
        }
    }
        public function setStatus($status, $message){
            http_response_code($status);
            echo json_encode($message);
        }

}