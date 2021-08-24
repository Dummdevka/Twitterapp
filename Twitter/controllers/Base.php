<?php

abstract class BaseController
{
    //public $template;
    public $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        //$this->template = $view;
    }
}
