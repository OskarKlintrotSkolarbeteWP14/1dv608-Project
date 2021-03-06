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
require_once("model/Todo.php");
require_once("exception/EmptyTodoException.php");
require_once("exception/ToLongTodoException.php");

class TodoView implements iLayoutView
{
	private static $createTodo = "TodoView::Add";
	private static $newTodo = "TodoView::New";
	private static $updateTodo = "TodoView::Edit";
	private static $updatedTodo = "TodoView::UpdatedTodo";
	private static $saveUpdatedTodo = "TodoView::Save";
	private static $cancelUpdatedTodo = "TodoView::Cancel";
	private static $confirmRemoveTodo = "TodoView::ConfirmationRemove";
	private static $cancelRemoveTodo = "TodoView::CancelRemove";
	private static $doneTodo = "TodoView::Done";
	private static $removeTodo = "TodoView::Remove";
	private static $sessionErrorMessage = "TodoView::Error";
	private static $sessionEditTodo = "TodoView::EditTodo";
	private static $sessionAskUserToConfirmRemovingTodo = "TodoView::TodoToBeConfirmed";
	private static $sessionPaginationPage = "TodoView::Page";
	private static $todosPerPage = 5;
	private static $nextPage = "TodoView::Next";
	private static $prevPage = "TodoView::Prev";
	private static $query = "page";

	private $username;
	private $todosFromDb;

    public function __construct($username) {
		$this->username = $username;
		$this->setupPagination();
    }

	private function setupPagination(){
		if (isset($_POST[self::$prevPage]))
			$_SESSION[self::$sessionPaginationPage] = $_POST[self::$prevPage];
		else if (isset($_POST[self::$nextPage]))
			$_SESSION[self::$sessionPaginationPage] = $_POST[self::$nextPage];
		else if ($_GET && strpos(@parse_url($_SERVER['REQUEST_URI'])['query'], self::$query) !== false) {
			$_SESSION[self::$sessionPaginationPage] = intval($_GET[self::$query]);
		}

		if(isset($_SESSION[self::$sessionPaginationPage])) {
			return;
		}
		else {
			$_SESSION[self::$sessionPaginationPage] = 0;
		}
	}

	public function setViewStraight(){
		if($_POST) {
			$parameters = $_GET;
			unset($parameters[self::$query]);
			if($_SESSION[self::$sessionPaginationPage] > 0) {
				$params[self::$query] = $_SESSION[self::$sessionPaginationPage];
				$queryString = http_build_query($params);
				$actual_link = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $queryString;
			}
			else
				$actual_link = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
			header("Location: $actual_link");
		}
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
		if (isset($_POST[self::$cancelRemoveTodo])){
			unset($_SESSION[self::$sessionAskUserToConfirmRemovingTodo]);
		}
		else if (isset($_POST[self::$removeTodo])){
			$_SESSION[self::$sessionAskUserToConfirmRemovingTodo] = $_POST[self::$removeTodo];
		}
		else if (isset($_POST[self::$confirmRemoveTodo])) {
			unset($_SESSION[self::$sessionAskUserToConfirmRemovingTodo]);
			return true;
		}
		else
			return false;
	}

	public function userWantsToToogleTodo(){
		return isset($_POST[self::$doneTodo]);
	}

	public function getTodoIDToBeUpdated(){
		if(isset($_POST[self::$saveUpdatedTodo])){
			$this->resetTodoToBeEdited();
			return $this->todosFromDb[$_POST[self::$saveUpdatedTodo]][1]->getTodoID();
		}
	}

	public function getTodoIDToBeDeleted(){
		if(isset($_POST[self::$confirmRemoveTodo])){
			return $this->todosFromDb[$_POST[self::$confirmRemoveTodo]][1]->getTodoID();
		}
	}

	public function getTodoIDToBeToggled(){
		if(isset($_POST[self::$doneTodo])){
			return $this->todosFromDb[$_POST[self::$doneTodo]][1]->getTodoID();
		}
	}

	public function getTodoMessageToBeUpdated() {
		if (!isset($_POST[self::$updatedTodo]) || empty($_POST[self::$updatedTodo]))
			throw new EmptyTodoException();
		else if ($this->validateTodoMessage($_POST[self::$updatedTodo]))
			return trim($_POST[self::$updatedTodo]);
	}

	public function setTodoToBeEdited(){
		if(isset($_POST[self::$updateTodo]))
			$_SESSION[self::$sessionEditTodo] = $_POST[self::$updateTodo];
	}

	public function resetTodoToBeEdited(){
		$_SESSION[self::$sessionEditTodo]  = null;
	}

	public function getTodoToBeCreated()
	{
		if (!isset($_POST[self::$newTodo]) || empty($_POST[self::$newTodo]))
			throw new EmptyTodoException();
		else if ($this->validateTodoMessage($_POST[self::$newTodo])){
			$_SESSION[self::$sessionPaginationPage] = 0;
			return trim($_POST[self::$newTodo]);
		}
	}

	private function validateTodoMessage($message){
		if (strlen($message) > 55)
			throw new ToLongTodoException();
		else if (filter_var($message, FILTER_SANITIZE_STRING) !== $message)
			throw new \Exception();
		return true;
	}

	public function setTodosFromDb($todos){
		if($todos) {
			foreach ($todos as $key => $item) {
				$this->todosFromDb[] = [$key, $item];
			}
		}
	}

    public function response() {
		if(isset($_SESSION[self::$sessionAskUserToConfirmRemovingTodo]))
			return $this->doAskUserToConfirmRemovingTodoHTML();
		else
        	return $this->doTodoFormHTML();
    }

	private function doAskUserToConfirmRemovingTodoHTML(){
		return "<form method='post'>
				<p>Do you really want to remove the todo?</p>
			    <button name='" . self::$confirmRemoveTodo . "' value='" . $_SESSION[self::$sessionAskUserToConfirmRemovingTodo] . "'>Yes</button>
			    <button name='" . self::$cancelRemoveTodo . "'>No</button>
			    </form>";
	}

	private function doTodoFormHTML(){
		return "<form method='post'>
				<fieldset>
				<legend>Todo-list</legend>
					". $this->getErrorMessageHTML()  ."
					<label for='todo'>Enter new todo :</label>
					<input type='text' id='".self::$newTodo."' name='".self::$newTodo."' placeholder='Write todo here...' autofocus>
					<input type='submit' name='".self::$createTodo."' value='Add todo' />
					<br>
					"
					.$this->getTodosHTML().
					"
					"
					.$this->getPaginationHTML().
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

/*	private function getTodosHTML(){
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
*/

	private function getTodosHTML(){
		$paginatedTodos = $this->getTodosPaginated();
		$todosToRender = "<table>";
		if($paginatedTodos) {
			foreach ($paginatedTodos as $key => $todo) {
				$timestamp = $todo[1]->getTimestamp()->setTimezone(new \DateTimeZone("Europe/Stockholm"));
				if(isset($_SESSION[self::$sessionEditTodo]) && $key == $_SESSION[self::$sessionEditTodo] ) {
					$todosToRender .= "<tr>" . "<td>" . intval($todo[0] + 1) . "</td>"
						. "<td colspan='2'> <input type='text' name='".self::$updatedTodo."' value='". $todo[1]->getTodo() ."' placeholder='Write todo here...'></td>"
						. "<td>" . "<button name='" . self::$saveUpdatedTodo . "' value='" . $todo[0] . "'>Save</button>" . "</td>"
						. "<td>" . "<button name='" . self::$cancelUpdatedTodo . "' value='" . $todo[0] . "'>Cancel</button>" . "</td>"
						. "</tr>";
				} else {
					$todosToRender .= "<tr class='" . $this->getDoneClass($todo[1]->getDone()) . "'>" . "<td>" . intval($todo[0] + 1) . "</td>"
						. "<td>" . $todo[1]->getTodo() . "</td>"
						. "<td>" . date_format($timestamp, 'Y-m-d H:i') . "</td>"
						. "<td>" . "<button name='" . self::$doneTodo . "' value='" . $todo[0] . "'>Done</button>" . "</td>"
						. "<td>" . "<button name='" . self::$updateTodo . "' value='" . $key . "' />Edit</button>" . "</td>"
						. "<td>" . "<button name='" . self::$removeTodo . "' value='" . $todo[0] . "' />Remove</button>" . "</td>"
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

	private function getTodosPaginated(){
		$paginatedTodos = "";
		if(isset($_SESSION[self::$sessionPaginationPage])){
			$page = $_SESSION[self::$sessionPaginationPage];
			if($this->todosFromDb) {
				foreach ($this->todosFromDb as $key => $item) {
					if ($key >= $page * self::$todosPerPage && $key < $page * self::$todosPerPage + self::$todosPerPage)
						$paginatedTodos[] = $item;
				}
			}
			if($paginatedTodos)
				return $paginatedTodos;
			else
				return $this->todosFromDb;
		} else {
			return $this->todosFromDb;
		}
	}

	private function getDoneClass($strikethrough){
		if($strikethrough)
			return "strikeout";
	}

	private function getPaginationHTML(){
		if(intval($_SESSION[self::$sessionPaginationPage] - 1) < 0 && $this->onLastPage())
			return "<button name='" . self::$prevPage . "' disabled>Older</button>"
			. "<button name='" . self::$nextPage . "' disabled >Newer</button>";
		else if(intval($_SESSION[self::$sessionPaginationPage] - 1) < 0)
			return "<button name='" . self::$prevPage . "' value='" . intval($_SESSION[self::$sessionPaginationPage] + 1) . "' >Older</button>"
				 . "<button name='" . self::$nextPage . "' disabled >Newer</button>";
		else if($this->onLastPage())
			return "<button name='" . self::$prevPage . "' disabled>Older</button>"
				 . "<button name='" . self::$nextPage . "' value='" . intval($_SESSION[self::$sessionPaginationPage] - 1) . "' >Newer</button>";
		else
			return "<button name='" . self::$prevPage . "' value='" . intval($_SESSION[self::$sessionPaginationPage] + 1) . "' >Older</button>"
				 . "<button name='" . self::$nextPage . "' value='" . intval($_SESSION[self::$sessionPaginationPage] - 1) . "' >Newer</button>";
	}

	private function onLastPage() {
		if(count($this->todosFromDb) / self::$todosPerPage <= $_SESSION[self::$sessionPaginationPage] + 1)
			return true;
		else
			return false;
	}
}