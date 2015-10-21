<?php
/**
 * Created by PhpStorm.
 * User: Oskar
 * Date: 2015-10-21
 * Time: 15:36
 */

namespace controller;

require_once("view/TodoView.php");

use view;

class TodoController
{
    private $view;

    public function __construct(view\TodoView $todoView){
        $this->view = $todoView;
    }

    public function doTodo(){

    }
}