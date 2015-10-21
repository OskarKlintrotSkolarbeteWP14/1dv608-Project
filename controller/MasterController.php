<?php
/**
 * Created by PhpStorm.
 * User: Oskar
 * Date: 2015-10-05
 * Time: 14:39
 */

namespace controller;

//App specific
require_once("view/DateTimeView.php");
require_once("view/LayoutView.php");

//Login
require_once("controller/LoginController.php");
require_once("view/LoginView.php");

//Registration
require_once("controller/RegistrationController.php");
require_once("view/RegistrationView.php");
require_once("model/RegistrationDAL.php");

//Database
require_once("model/DatabaseConnection.php");

use controller;
use model;
use view;

class MasterController
{
    private $m_Login;
    private $v_Login;
    private $c_Login;
    private $m_Registration;
    private $m_RegistrationDAL;
    private $v_Registration;
    private $c_Registration;
    private $m_DatabaseConnection;
    private $loggedIn = false;
    private $registerNewUser = false;

    public function run() {
        //Dependency injection
        $this->m_DatabaseConnection = new \DatabaseConnection();

        $this->m_Login = new model\LoginModel($this->m_DatabaseConnection);
        $this->v_Login = new view\LoginView($this->m_Login);
        $this->c_Login = new controller\LoginController($this->m_Login, $this->v_Login);

        $this->m_Registration = new model\RegistrationModel();
        $this->m_RegistrationDAL = new model\RegistrationDAL($this->m_DatabaseConnection);
        $this->v_Registration = new view\RegistrationView($this->m_Registration, $this->m_RegistrationDAL);
        $this->c_Registration = new controller\RegistrationController($this->v_Registration, $this->m_Registration, $this->m_RegistrationDAL);

        //Controller must be run first since state is changed
        if($this->c_Registration->userWantToRegister()) {
            $viewToRender = $this->v_Registration;
            $this->registerNewUser = true;
            if($this->c_Registration->userWantToRegisterNewUser()) {
                $this->c_Registration->doRegistration();
            }
        }
        else {
            $viewToRender = $this->v_Login;
            $this->c_Login->doControl();
            $this->loggedIn = $this->m_Login->isLoggedIn($this->v_Login->getUserClient());
        }

        //Generate output
        $v_DateTime = new view\DateTimeView();
        $v_Layout = new view\LayoutView();
        $v_Layout->render($this->registerNewUser, $this->loggedIn, $viewToRender, $v_DateTime);
    }
}