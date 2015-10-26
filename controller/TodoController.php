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
        $this->view->setTodosFromDb($this->todoDAL->readTodos());

        if($this->view->userWantsToCreateTodo()){
            try {
                $this->todoDAL->createTodo($this->view->getTodoToBeCreated());
            }catch (\Exception\EmptyTodoException $e) {
                $this->view->setErrorMessageForEmptyTodo();
            }catch (\Exception\ToLongTodoException $e) {
                $this->view->setErrorMessageForToLongTodo();
            }catch (\Exception $e) {
                $this->view->setGeneralErrorMessage();
            }
        }

        if($this->view->userWantsToUpdateTodo()){
            try {
                $this->todoDAL->updateTodo($this->view->getTodoIDToBeUpdated(), $this->view->getTodoMessageToBeUpdated());
            }catch (\Exception\EmptyTodoException $e) {
                $this->view->setErrorMessageForEmptyTodo();
            }catch (\Exception\ToLongTodoException $e) {
                $this->view->setErrorMessageForToLongTodo();
            }catch (\Exception $e) {
                $this->view->setGeneralErrorMessage();
            }
        }

        if($this->view->userWantsToDeleteTodo()){
            $this->todoDAL->deleteTodo($this->view->getTodoIDToBeDeleted());
        }

        if($this->view->userWantsToToogleTodo()){
            $this->todoDAL->toggleDoneTodo($this->view->getTodoIDToBeToggled());
        }

        $this->view->setViewStraight();
    }
}