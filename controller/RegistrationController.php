<?php
/**
 * Created by PhpStorm.
 * User: Oskar Klintrot
 * Date: 2015-10-05
 * Time: 10:20
 */

namespace controller;

require_once("model/RegistrationDAL.php");
require_once("view/RegistrationView.php");

class RegistrationController
{
    private $model;
    private $view;
    private $dal;

    public function __construct(\view\RegistrationView $view, \model\RegistrationModel $model, \model\RegistrationDAL $registrationDAL) {
        $this->model = $model;
        $this->view =  $view;
        $this->dal = $registrationDAL;
    }

    public function userWantToRegister() {
        return $this->view->userWantToRegister();
    }

    public function userWantToRegisterNewUser() {
        return $this->view->userWantToRegisterNewUser();
    }

    public function doRegistration() {
        $validationSuccess = $this->view->validate();
        $saveMemberSuccess = false;
        if ($validationSuccess) {
            $saveMemberSuccess = $this->dal->SaveUser($this->view->getUser());
        }
        if ($saveMemberSuccess) {
            $this->view->registrationSuccessful();
            return true;
        }
        return false;
    }

}