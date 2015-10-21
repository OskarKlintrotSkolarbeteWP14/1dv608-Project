<?php
/**
  * Solution for assignment 2
  * @author Daniel Toll
  */
namespace model;

require_once("UserCredentials.php");
require_once("TempCredentials.php");
require_once("TempCredentialsDAL.php");
require_once("LoggedInUser.php");
require_once("UserClient.php");
require_once("LoginDAL.php");


class LoginModel {

	private $database;
	private $dal;

	private $sessionUserLocation = "LoginModel::loggedInUser";

	/**
	 * @var null | TempCredentials
	 */
	private $tempCredentials = null;

	private $tempDAL;

	public function __construct(\DatabaseConnection $databaseConnection) {
		$this->database = $databaseConnection;
		$this->dal = new LoginDAL($databaseConnection);

		$this->sessionUserLocation .= \Settings::APP_SESSION_NAME;

		if (!isset($_SESSION)) {
			//Alternate check with newer PHP
			//if (\session_status() == PHP_SESSION_NONE) {
			assert("No session started");
		}
		$this->tempDAL = new TempCredentialsDAL();
		
	}

	/**
	 * Checks if user is logged in
	 * @param  UserClient $userClient The current calls Client
	 * @return boolean                true if user is logged in.
	 */
	public function isLoggedIn(UserClient $userClient) {
		if (isset($_SESSION[$this->sessionUserLocation])) {
			$user = $_SESSION[$this->sessionUserLocation];

			if ($user->sameAsLastTime($userClient) == false) {
				return false;
			}
			return true;
		} 

		return false;
	}

	/**
	 * Attempts to authenticate
	 * @param  UserCredentials $uc
	 * @return boolean
	 */
	public function doLogin(UserCredentials $uc) {
		
		$this->tempCredentials = $this->tempDAL->load($uc->getName());

		$validUser = $this->dal->getUserCredentials($uc->getName());

//		$loginByUsernameAndPassword = \Settings::USERNAME === $uc->getName() && \Settings::PASSWORD === $uc->getPassword();
		$loginByUsernameAndPassword = $validUser->getUsername() === $uc->getName() && password_verify($uc->getPassword(), $validUser->getPassword());
		$loginByTemporaryCredentials = $this->tempCredentials != null && $this->tempCredentials->isValid($uc->getTempPassword());

		if ( $loginByUsernameAndPassword || $loginByTemporaryCredentials) {
			$user = new LoggedInUser($uc); 

			$_SESSION[$this->sessionUserLocation] = $user;

			return true;
		}
		return false;
	}

	public function doLogout() {
		unset($_SESSION[$this->sessionUserLocation]);
	}

	/**
	 * @return TempCredentials
	 */
	public function getTempCredentials() {
		return $this->tempCredentials;
	}

	/**
	 * renew the temporary credentials
	 * 
	 * @param  UserClient $userClient 
	 */
	public function renew(UserClient $userClient) {
		if ($this->isLoggedIn($userClient)) {
			$user = $_SESSION[$this->sessionUserLocation];
			$this->tempCredentials = new TempCredentials($user);
			$this->tempDAL->save($user, $this->tempCredentials);
		}
	}
	
}