<?php
use Firebase\JWT\JWT;
abstract class BaseController
{
    public $db_tweets;
    public $db_auth;
    public $user;
    public $id;
    public $auth = false;
    protected $refresh = "iW0TdKav8l8GSEVg5FrL47A22qDqtUQy";
    protected $key = "D91303F61B40A52C1E8E060A93E59944CC6E3D4F8D50C6795F45DB209736E03E";
    public function __construct($db_tweets, $db_auth)
    {
        $this->db_tweets = $db_tweets;
        $this->db_auth = $db_auth;
    }
    public function setStatus($status, $message){
        http_response_code($status);
        print_r(json_encode($message));
    }
    public function setAccessJwt(array $user){
        $issuer_claim = "http://localhost"; // this can be the servername
        $audience_claim = "http://localhost";
        $issuedat_claim = time(); // issued at
        //$notbefore_claim = $issuedat_claim + 2; //not before in seconds
        $expire_claim = $issuedat_claim + 20; // expire time in seconds
        $access_token = array(
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "iat" => $issuedat_claim,
            //"nbf" => $notbefore_claim,
            "exp" => $expire_claim,
            "data" => array(
                "id" => $user['id'],
                "username" => $user['username']
            )
        );
        $jwt = JWT::encode($access_token, $this->key);
        echo json_encode(
            array(
                "message" => "Successful login.",
                "jwt" => $jwt,
                "expire_at" => $expire_claim,
            ));
    }
    public function getNewAccess(){
        if(isset($_COOKIE['refresh'])){
            $refresh_token = $_COOKIE['refresh'];

        } else{
            //If there is no refresh token then the page can not be accessed
            $this->setStatus(403, 'No refresh');
            exit();
        }
        
            try{
                $decoded = JWT::decode($refresh_token, $this->refresh, array('HS256'));
                $user = (array) $decoded->data;
                //Saving new token
                $this->setAccessJwt($user);
                
                // echo json_encode($response);
                // exit();               
                // //If adding tweet we dont need any response
                
            } catch( Exception $e){
                //Invalid refresh token
                $this->setStatus(404, "Please log in again");
                print_r(json_encode($e->getMessage()));
            }
    }
    public function checkToken(){
        $headers = getallheaders();

        if(!isset($headers['Authorization'])){
            //Not authorized users can not access 
            //var_dump($_SERVER['REQUEST_METHOD']);
            $this->setStatus(403, "No authorization token");
            exit();
        }
        elseif(!isset($_COOKIE['refresh'])){
            $this->setStatus(403, "No refresh token");
            exit();
        } else{
            try{
                //Verify token
                $token = $headers['Authorization'];
                $jwt = str_replace('Bearer ', '', $token);
                $decoded = JWT::decode($jwt, $this->key, array('HS256'));
                $this->user = $decoded->data->username;
                $this->id = $decoded->data->id;
                //If it is valid then return false(valid token)
                return false;
                exit();

            } catch( Exception $e){
                //Checking if the token is expired
                if($e->getMessage() === "Expired token"){
                    //If the token is expired then it gets refreshed, we need to post the tweet anyway
                    if(strcmp($_SERVER['REQUEST_METHOD'],'POST')===0){
                    return false;
                }
                //If refreshing the token
                if(strcmp($_SERVER['REQUEST_METHOD'],'GET')===0){
                    $this->getNewAccess();
                }
                    exit();
                    
                } else{
                    //If there is another error then log out and print it
                    var_dump($e->getMessage());
                    $this->setStatus(404, $token);
                }
            }
        }
    }
}
