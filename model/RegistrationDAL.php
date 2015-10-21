<?php
/**
 * Created by PhpStorm.
 * User: Oskar
 * Date: 2015-10-05
 * Time: 12:23
 */

namespace model;

require_once("User.php");
require_once("exception/UserAlreadyExistException.php");

use exception;

class RegistrationDAL
{
    private $database;

    public function __construct(\DatabaseConnection $databaseConnection) {
        $this->database = $databaseConnection;
    }

    public function doUserExist(User $user)
    {
        $this->database->prepare('SELECT * FROM users WHERE username = :username');
        $this->database->bindValue(':username', $user->getUsername());
        $this->database->fetchAll();

        if ($this->database->rowCount() > 0) {
            throw new exception\UserAlreadyExistException;
        }
    }

    public function SaveUser(User $user) {
        $this->doUserExist($user);

        $this->database->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
        $this->database->bindValue(':username', $user->getUsername());
        $this->database->bindValue(':password', \password_hash($user->getPassword(), PASSWORD_DEFAULT));
        $this->database->execute();

        return true;
    }

}