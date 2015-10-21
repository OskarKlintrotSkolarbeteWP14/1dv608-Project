<?php
/**
 * Created by PhpStorm.
 * User: Oskar
 * Date: 2015-10-21
 * Time: 16:58
 */

namespace model;

require_once("User.php");
require_once("BaseDAL.php");

class TodoDAL extends BaseDAL
{
    private $username;
    public $database;

    public function __construct($database, $username){
        $this->database = $database;
        $this->username = $username;
    }

    public function getTodos() {
        $userID = $this->getUserCredentials($this->username)->getUserID();

        $this->database->prepare('SELECT * FROM todos WHERE UserID = :userID');
        $this->database->bindValue(':userID', $userID);
        return $this->database->fetchAll();
    }

//    public function doUserExist(User $user)
//    {
//        $this->database->prepare('SELECT * FROM users WHERE username = :username');
//        $this->database->bindValue(':username', $user->getUsername());
//        $this->database->fetchAll();
//
//        if ($this->database->rowCount() > 0) {
//            throw new exception\UserAlreadyExistException;
//        }
//    }
}