<?php
/**
 * Created by PhpStorm.
 * User: Oskar
 * Date: 2015-10-06
 * Time: 17:54
 */

namespace model;

require_once("exception/InvalidPasswordException.php");
require_once("exception/InvalidUsernameException.php");
require_once("exception/ToShortPasswordException.php");
require_once("exception/ToShortUsernameException.php");

use exception\InvalidPasswordException;
use exception\InvalidUsernameException;
use exception\ToShortPasswordException;
use exception\ToShortUsernameException;

class User
{
    private $username;
    private $password;

    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    public function runTests(){
        $this->testValidUsername();
        $this->testValidPassword();
        $this->testUsernameLength();
        $this->testPasswordLength();
    }

    public function testValidUsername() {
        if(strlen($this->username) != strlen(strip_tags($this->username)))
            throw new InvalidUsernameException();
    }

    public function testUsernameLength() {
        if(strlen($this->username) < 3)
            throw new ToShortUsernameException();
    }

    public function testValidPassword() {
        if(strlen($this->password) != strlen(strip_tags($this->password)))
            throw new InvalidPasswordException();
    }

    public function testPasswordLength() {
        if(strlen($this->password) < 6)
            throw new ToShortPasswordException();
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }
}