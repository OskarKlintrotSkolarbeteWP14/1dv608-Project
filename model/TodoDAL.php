<?php
/**
 * Created by PhpStorm.
 * User: Oskar
 * Date: 2015-10-21
 * Time: 16:58
 */

namespace model;

require_once("User.php");
require_once("Todo.php");
require_once("BaseDAL.php");

class TodoDAL extends BaseDAL
{
    private $username;

    public function __construct($database, $username){
        parent::__construct($database);
        $this->username = $username;
    }

    public function getTodos() {
        $userID = $this->getUserCredentials($this->username)->getUserID();

        $this->database->prepare('SELECT * FROM todos WHERE UserID = :userID');
        $this->database->bindValue(':userID', $userID);
        $resultFromDatabase = $this->database->fetchAll();

        if(!empty($resultFromDatabase)) {
            foreach ($resultFromDatabase as $todo) {
                $todos[] = new Todo($todo["TodoID"], $todo["Todo"], $todo["Timestamp"]);
            }
            return $todos;
        } else
            return "";
    }

    public function saveTodo($todo) {
        $userID = $this->getUserCredentials($this->username)->getUserID();

        $this->database->prepare('INSERT INTO todos (UserID, Todo) VALUES (:userID, :todo)');
        $this->database->bindValue(':userID', $userID);
        $this->database->bindValue(':todo', $todo);
        $this->database->execute();

        return true;
    }

}