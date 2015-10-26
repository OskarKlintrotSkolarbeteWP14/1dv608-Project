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
use exception\EmptyTimestampException;
use exception\ToLongTodoException;
use exception\DoneIsNotBooleanException;

require_once("exception/EmptyTodoException.php");
require_once("exception/EmptyTodoIDException.php");
require_once("exception/EmptyTimestampException.php");
require_once("exception/ToLongTodoException.php");
require_once("exception/DoneIsNotBooleanException.php");

class Todo
{
    private $TodoID;
    private $Todo;
    private $Done;
    private $Timestamp;

    public function __construct($todoID, $done, $todo, $timestamp){
        if(empty($todo))
            throw new EmptyTodoException();
        if(strlen($todo) > 55)
            throw new ToLongTodoException();
        if(empty($todoID))
            throw new EmptyTodoIDException();
        if(empty($timestamp))
            throw new EmptyTimestampException();
        if(!($done == true || $done == false))
            throw new DoneIsNotBooleanException();
        $this->TodoID = $todoID;
        $this->Todo = $todo;
        $this->Done = $done;
        $this->Timestamp = new \DateTime($timestamp);
        $this->Timestamp = $this->Timestamp->sub(new \DateInterval('PT3H')); // Since I can't change my db (I have on a friends server) to use UTC I simulate it here
    }

    public function getTodoID(){
        return $this->TodoID;
    }

    public function getDone(){
        return $this->Done;
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