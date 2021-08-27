<?php

abstract class BaseController
{
    public $db_tweets;
    public $db_auth;
    public function __construct($db_tweets, $db_auth)
    {
        $this->db_tweets = $db_tweets;
        $this->db_auth = $db_auth;
    }
}
