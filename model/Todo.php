<?php
/**
 * Created by PhpStorm.
 * User: Oskar
 * Date: 2015-10-22
 * Time: 12:15
 */

namespace model;


class Todo
{
    private $TodoID;
    private $Todo;
    private $Timestamp;

    public function __construct($todoID, $todo, $timestamp){
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
        $this->Todo = $todo;
    }
}