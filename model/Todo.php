<?php
/**
 * Created by PhpStorm.
 * User: Oskar
 * Date: 2015-10-22
 * Time: 12:15
 */

namespace model;

use exception\EmptyTodoException;
use exception\EmptyTodoIDException;

require_once("exception/EmptyTodoException.php");
require_once("exception/EmptyTodoIDException.php");
require_once("exception/EmptyTimestampException.php");

class Todo
{
    private $TodoID;
    private $Todo;
    private $Timestamp;

    public function __construct($todoID, $todo, $timestamp){
        if(empty($todo))
            throw new EmptyTodoException();
        if(empty($todoID))
            throw new EmptyTodoIDException();
        if(empty($timestamp))
            throw new EmptyTimestampException();
        $this->TodoID = $todoID;
        $this->Todo = $todo;
        $this->Timestamp = $timestamp;
    }

    public function getTodoID(){
        return $this->TodoID;
    }

    public function getTodo(){
        return $this->Todo;
    }

    public function getTimestamp(){
        return $this->Timestamp;
    }

    public function setTodo($todo){
        if(empty($todo))
            throw new EmptyTodoException();
        $this->Todo = $todo;
    }
}