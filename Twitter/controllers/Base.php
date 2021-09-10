<?php
use Firebase\JWT\JWT;
abstract class BaseController
{
    public $db_tweets;
    public $db_auth;
    public $user;
    public $id;
    protected $refresh = "iW0TdKav8l8GSEVg5FrL47A22qDqtUQy";
    protected $key = "D91303F61B40A52C1E8E060A93E59944CC6E3D4F8D50C6795F45DB209736E03E";
    public function __construct($db_tweets, $db_auth)
    {
        $this->db_tweets = $db_tweets;
        $this->db_auth = $db_auth;
    }
    public function checkToken(){
        $headers = apache_request_headers();
        if(!isset($headers['Authorization'])){
            //http_response_code(403);
            print_r(json_encode("JWT token is missing, please log in!"));
            return false;
        } else{
            try{
                $token = $headers['Authorization'];
                $jwt = str_replace('Bearer ', '', $token);
                // print_r($jwt);
                $decoded = JWT::decode($jwt, $this->key, array('HS256'));
                $this->user = $decoded->data->username;
                $this->id = $decoded->data->id;
                return true;

            } catch( Exception $e){
                //Checking if the token is expired
                if($e->getMessage() === "Expired token"){
                    //Somehow refresh it
                    if(!isset($_COOKIE['refresh'])){
                        //Log out and delete access token
                        return $_COOKIE;
                        exit();
                    } else{
                        return false;
                        exit();
                        // $refresh = $_COOKIE['refresh'];
                        // //Packing all the data for a new access token
                        // $user = [
                        //     'user'=>$this->user,
                        //     'id'=> $this->id
                        // ];
                        // //Make requesto to get a new acces token(send username and id)
                        // return $user;
                        // //Receive the token and store it again
                    }
                    
                } else{
                    return "Error:" . $e->getMessage();
                }
            }
            

            //Validate the token
            //Return true if its valid
        }
    }
}
