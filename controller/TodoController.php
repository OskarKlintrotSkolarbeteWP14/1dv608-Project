<?php
/**
 * Created by PhpStorm.
 * User: Oskar
 * Date: 2015-10-21
 * Time: 15:36
 */

namespace controller;

require_once("view/TodoView.php");
require_once("model/TodoDAL.php");

use view;
use model;

class TodoController
{
    private $view;
    private $todoDAL;

    public function __construct(view\TodoView $todoView, model\TodoDAL $todoDAL){
        $this->view = $todoView;
        $this->todoDAL = $todoDAL;
    }

    public function doTodo(){
        var_dump($this->todoDAL->getTodos());
    }
}