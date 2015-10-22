<?php
/**
 * Created by PhpStorm.
 * User: Oskar
 * Date: 2015-10-21
 * Time: 15:40
 */

namespace view;

require_once("iLayoutView.php");
require_once("PRG.php");

class TodoView extends PRG implements iLayoutView
{
	private static $addTodo = "TodoView::Add";
	private static $newTodo = "TodoView::New";

	private $username;

    public function __construct($username) {
		$this->username = $username;
    }

    public function userWantsToAddTodo(){
		return isset($_POST[self::$addTodo]);
	}

	public function getTodoToBeSaved() {
		if (isset($_POST[self::$newTodo]))
			return trim($_POST[self::$newTodo]);

		return "";
	}

    public function response() {
        return $this->doTodoForm();
    }

	private function doTodoForm(){
		return "<form method='post' enctype='multipart/form-data'>
				<fieldset>
				<legend>Todo-list</legend>
					<p>Todo: </p>
					<label for='todo'>Enter new todo :</label>
					<input type='text' id='".self::$newTodo."' name='".self::$newTodo."' placeholder='Write todo here...'>
					<input type='submit' name='".self::$addTodo."' value='Add todo' />
					<br>
				</fieldset>
			</form>";
	}
}