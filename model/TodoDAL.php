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

    public function readTodos() {
        $userID = $this->getUserCredentials($this->username)->getUserID();

        $this->database->prepare('SELECT * FROM todos WHERE UserID = :userID');
        $this->database->bindValue(':userID', $userID);
        $resultFromDatabase = $this->database->fetchAll();

        if(!empty($resultFromDatabase)) {
            foreach ($resultFromDatabase as $todo) {
                $todos[] = new Todo($todo["TodoID"], $todo["Done"], $todo["Todo"], $todo["Timestamp"]);
            }
            return $todos;
        } else
            return "";
    }

    public function readTodo($todoID) {
        $userID = $this->getUserCredentials($this->username)->getUserID();

        $this->database->prepare('SELECT * FROM todos WHERE UserID = :userID AND TodoID = :todoID');
        $this->database->bindValue(':userID', $userID);
        $this->database->bindValue(':todoID', $todoID);
        $resultFromDatabase = $this->database->fetch();

        if(!empty($resultFromDatabase)) {
            return new Todo($resultFromDatabase["TodoID"], $resultFromDatabase["Done"],
                $resultFromDatabase["Todo"], $resultFromDatabase["Timestamp"]);
        } else
            return "";
    }

    public function createTodo($todo) {
        $userID = $this->getUserCredentials($this->username)->getUserID();

        $this->database->prepare('INSERT INTO todos (UserID, Todo) VALUES (:userID, :todo)');
        $this->database->bindValue(':userID', $userID);
        $this->database->bindValue(':todo', $todo);
        $this->database->execute();

        return true;
    }

    public function deleteTodo($todoID) {
        $userID = $this->getUserCredentials($this->username)->getUserID();

        $this->database->prepare('DELETE FROM todos WHERE UserID = :userID AND TodoID = :todoID');
        $this->database->bindValue(':userID', $userID);
        $this->database->bindValue(':todoID', $todoID);
        $this->database->execute();

        return true;
    }

    public function toggleDoneTodo($todoID) {
        $userID = $this->getUserCredentials($this->username)->getUserID();
        $todo = $this->readTodo($todoID);
        $currentStatus = $todo->getDone();

        $this->database->prepare('UPDATE todos SET Done = :done WHERE UserID = :userID AND TodoID = :todoID');
        $this->database->bindValue(':userID', $userID);
        $this->database->bindValue(':todoID', $todoID);
        $this->database->bindValue(':done', !$currentStatus);
        $this->database->execute();

        return true;
    }

    public function updateTodo($todoID, $message) {
        $userID = $this->getUserCredentials($this->username)->getUserID();
//        $todo = $this->readTodo($todoID);
//        $todoID = $todo->getTodoID();

        $this->database->prepare('UPDATE todos SET Todo = :todo WHERE UserID = :userID AND TodoID = :todoID');
        $this->database->bindValue(':userID', $userID);
        $this->database->bindValue(':todoID', $todoID);
        $this->database->bindValue(':todo', $message);
        $this->database->execute();

        return true;
    }

}