<?php
/**
 * Created by PhpStorm.
 * User: Oskar
 * Date: 2015-10-21
 * Time: 15:40
 */

namespace view;

use exception\EmptyTodoException;
use exception\ToLongTodoException;

require_once("iLayoutView.php");
require_once("PRG.php");
require_once("model/Todo.php");
require_once("exception/EmptyTodoException.php");
require_once("exception/ToLongTodoException.php");

class TodoView extends PRG implements iLayoutView
{
	private static $createTodo = "TodoView::Add";
	private static $newTodo = "TodoView::New";
	private static $updateTodo = "TodoView::Edit";
	private static $updatedTodo = "TodoView::UpdatedTodo";
	private static $saveUpdatedTodo = "TodoView::Save";
	private static $cancelUpdatedTodo = "TodoView::Cancel";
	private static $doneTodo = "TodoView::Done";
	private static $removeTodo = "TodoView::Remove";
	private static $sessionErrorMessage = "TodoView::Error";
	private static $sessionEditTodo = "TodoView::EditTodo";

	private $username;
	private $todosFromDb;

    public function __construct($username) {
		$this->username = $username;
    }

	public function setViewStraight(){
		if($_POST)
			$this->redirect();
	}

    public function userWantsToCreateTodo(){
		return isset($_POST[self::$createTodo]);
	}

	public function userWantsToUpdateTodo(){
		if($this->userWantsToCancelUpdateTodo()){
			$this->resetTodoToBeEdited();
		}
		if(isset($_POST[self::$updateTodo])) {
			$this->setTodoToBeEdited();
		}
		return isset($_POST[self::$saveUpdatedTodo]);
	}

	private function userWantsToCancelUpdateTodo(){
		return isset($_POST[self::$cancelUpdatedTodo]);
	}

	public function userWantsToDeleteTodo(){
		return isset($_POST[self::$removeTodo]);
	}

	public function userWantsToToogleTodo(){
		return isset($_POST[self::$doneTodo]);
	}

	public function getTodoIDToBeUpdated(){
		if(isset($_POST[self::$saveUpdatedTodo])){
			$this->resetTodoToBeEdited();
			return $this->todosFromDb[$_POST[self::$saveUpdatedTodo]]->getTodoID();
		}
	}

	public function getTodoIDToBeDeleted(){
		if(isset($_POST[self::$removeTodo])){
			return $this->todosFromDb[$_POST[self::$removeTodo]]->getTodoID();
		}
	}

	public function getTodoIDToBeToggled(){
		if(isset($_POST[self::$doneTodo])){
			return $this->todosFromDb[$_POST[self::$doneTodo]]->getTodoID();
		}
	}

	public function getTodoMessageToBeUpdated() {
		if (!isset($_POST[self::$updatedTodo]) || empty($_POST[self::$updatedTodo]))
			throw new EmptyTodoException();
		else if (strlen($_POST[self::$updatedTodo]) > 55)
			throw new ToLongTodoException();
		else
			return trim($_POST[self::$updatedTodo]);
	}

	public function setTodoToBeEdited(){
		if(isset($_POST[self::$updateTodo]))
			$_SESSION[self::$sessionEditTodo] = $_POST[self::$updateTodo];
	}

	public function resetTodoToBeEdited(){
		$_SESSION[self::$sessionEditTodo]  = null;
	}

	public function getTodoToBeCreated() {
		if (!isset($_POST[self::$newTodo]) || empty($_POST[self::$newTodo]))
			throw new EmptyTodoException();
		else if (strlen($_POST[self::$newTodo]) > 55)
			throw new ToLongTodoException();
		else
			return trim($_POST[self::$newTodo]);
	}

	public function setTodosFromDb($todos){
		$this->todosFromDb = $todos;
	}

    public function response() {
        return $this->doTodoFormHTML();
    }

	private function doTodoFormHTML(){
		return "<form method='post'>
				<fieldset>
				<legend>Todo-list</legend>
					". $this->getErrorMessageHTML()  ."
					<label for='todo'>Enter new todo :</label>
					<input type='text' id='".self::$newTodo."' name='".self::$newTodo."' placeholder='Write todo here...'>
					<input type='submit' name='".self::$createTodo."' value='Add todo' />
					<br>
					"
					.$this->getTodosHTML().
					"
				</fieldset>
			</form>";
	}

	public function setErrorMessageForEmptyTodo(){
		$_SESSION[self::$sessionErrorMessage] = "Todo can't be empty!";
	}

	public function setErrorMessageForToLongTodo(){
		$_SESSION[self::$sessionErrorMessage] = "Todo can't be more than 55 characters!";
	}

	public function setGeneralErrorMessage(){
		$_SESSION[self::$sessionErrorMessage] = "Something went wrong, try again later!";
	}

	private function getErrorMessageHTML(){
		if(isset($_SESSION[self::$sessionErrorMessage]) && !$_POST){
			$errorMessage = "<p>". $_SESSION[self::$sessionErrorMessage] ."</p>";
			unset($_SESSION[self::$sessionErrorMessage]);
			return $errorMessage;
		}
		return "";
	}

	private function getTodosHTML(){
		$todosToRender = "<table>";
		if($this->todosFromDb) {
			foreach ($this->todosFromDb as $key => $todo) {
				$timestamp = $todo->getTimestamp()->setTimezone(new \DateTimeZone("Europe/Stockholm"));
				if(isset($_SESSION[self::$sessionEditTodo]) && $key == $_SESSION[self::$sessionEditTodo] ) {
					$todosToRender .= "<tr>" . "<td>" . intval($key + 1) . "</td>"
						. "<td colspan='2'> <input type='text' name='".self::$updatedTodo."' value='". $todo->getTodo() ."' placeholder='Write todo here...'></td>"
						. "<td>" . "<button name='" . self::$saveUpdatedTodo . "' value='" . $key . "'>Save</button>" . "</td>"
						. "<td>" . "<button name='" . self::$cancelUpdatedTodo . "' value='" . $key . "'>Cancel</button>" . "</td>"
						. "</tr>";
				} else {
					$todosToRender .= "<tr class='" . $this->getDoneClass($todo->getDone()) . "'>" . "<td>" . intval($key + 1) . "</td>"
						. "<td>" . $todo->getTodo() . "</td>"
						. "<td>" . date_format($timestamp, 'Y-m-d H:i') . "</td>"
						. "<td>" . "<button name='" . self::$doneTodo . "' value='" . $key . "'>Done</button>" . "</td>"
						. "<td>" . "<button name='" . self::$updateTodo . "' value='" . $key . "' />Edit</button>" . "</td>"
						. "<td>" . "<button name='" . self::$removeTodo . "' value='" . $key . "' />Remove</button>" . "</td>"
						. "</tr>";
				}
			}
			$todosToRender .= "</table>";
		}
		else {
			$todosToRender = "<p>Nothing more to do, go back to sleep or go for a run!</p>";
		}
		return $todosToRender;
	}

	private function getDoneClass($strikethrough){
		if($strikethrough)
			return "strikeout";
	}
}