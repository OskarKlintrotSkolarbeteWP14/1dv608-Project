<?php
/**
 * Created by PhpStorm.
 * User: Oskar
 * Date: 2015-10-22
 * Time: 11:55
 */

namespace view;


class PRG
{
    public function redirect()
    {
        if ($_POST) {
            $actual_link = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            //header("Location: $actual_link");
            var_dump($_SERVER['REQUEST_URI']);
        }
    }
}