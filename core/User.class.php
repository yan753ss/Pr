<?php
class User{
    public $id = 0;
    public $email;
    public $login;
    public $password;
    public $created;

    function __construct(array $params = []){
        if(!$params) return;
        $this->login = $params['login'];
        $this->password = $params['password'];
        $this->email = $params['email'] ?? '';
    }
}