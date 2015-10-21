<?php
/**
 * Created by PhpStorm.
 * User: Oskar
 * Date: 2015-10-21
 * Time: 17:18
 */

namespace model;


class BaseDAL
{
    public $database;

    public function __construct($database){
        $this->database = $database;
    }

    public function getUserCredentials($username) {
        $this->database->prepare('SELECT userid, username, password FROM users WHERE username = :username');
        $this->database->bindValue(':username', $username);
        $resultFromDatabase = $this->database->fetch();
        return new User($resultFromDatabase['username'], $resultFromDatabase['password'], $resultFromDatabase['userid']);
    }
}