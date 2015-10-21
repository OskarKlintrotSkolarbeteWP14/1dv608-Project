<?php
/**
 * Created by PhpStorm.
 * User: Oskar
 * Date: 2015-10-07
 * Time: 17:37
 */

namespace model;


class LoginDAL
{
    private $database;

    public function __construct($database){
        $this->database = $database;
    }

    public function getUserCredentials($username) {
        $this->database->prepare('SELECT username, password FROM users WHERE username = :username');
        $this->database->bindValue(':username', $username);
        $resultFromDatabase = $this->database->fetch();
        return new User($resultFromDatabase['username'], $resultFromDatabase['password']);
    }
}