<?php
/**
 * Created by PhpStorm.
 * User: Oskar
 * Date: 2015-10-21
 * Time: 15:40
 */

namespace view;

require_once("iLayoutView.php");

class TodoView implements iLayoutView
{
    public function __construct() {

    }

    public function response() {
        return "<form action='?register' method='post' enctype='multipart/form-data'>
				<fieldset>
				<legend>Todo-list</legend>
					<p>Todo: </p>
					<label for='todo'>Todo :</label>
					<input type='text' id='todo' value='Write todo here...'>
					<br>
				</fieldset>
			</form>";
    }
}