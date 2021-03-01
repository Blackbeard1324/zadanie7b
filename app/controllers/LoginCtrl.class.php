<?php

namespace app\controllers;

use app\transfer\User;
use app\forms\LoginForm;

class LoginCtrl
{
    private $form;

    public function __construct()
    {
        $this->form = new LoginForm();
    }

    public function getParams()
    {
        $this->form->login = getFromRequest('login');
        $this->form->pass = getFromRequest('pass');
    }

    public function validate()
    {
        if (!(isset ($this->form->login) && isset ($this->form->pass))) {

            return false;
        }

        if (!getMessages()->isError()) {

            if ($this->form->login == "") {
                getMessages()->addError('Nie podano loginu');
            }
            if ($this->form->pass == "") {
                getMessages()->addError('Nie podano hasła');
            }
        }
        if ( !getMessages()->isError() ) {

            if ($this->form->login == "admin" && $this->form->pass == "admin") {


                $user = new User($this->form->login, 'admin');

                $_SESSION['user'] = serialize($user);

                addRole($user->role);

            } else if ($this->form->login == "user" && $this->form->pass == "user") {

                $user = new User($this->form->login, 'user');

                $_SESSION['user'] = serialize($user);

                addRole($user->role);

            } else {
                getMessages()->addError('Niepoprawny login lub hasło');

            }
            return !getMessages()->isError();
        }
    }

    public function process()
    {

        $this->getParams();

        if ($this->validate()) {
            $this->form->kwota = floatval($this->form->kwota);
            $this->form->lata = intval($this->form->lata);
            $this->form->procent = floatval($this->form->procent);

            $this->result->rata = $this->form->kwota / ($this->form->lata * 12);
            $this->result->result = $this->result->rata + ($this->result->rata * ($this->form->procent / 100));
        }
        $this->generateView();
    }



	public function action_login(){

		$this->getParams();
		
		if ($this->validate()){
			//zalogowany => przekieruj na stronę główną, gdzie uruchomiona zostanie domyślna akcja
			header("Location: ".getConf()->app_url."/");
		} else {
			//niezalogowany => wyświetl stronę logowania
			$this->generateView(); 
		}
		
	}
	
	public function action_logout(){
		// 1. zakończenie sesji - tylko kończymy, jesteśmy już podłączeni w init.php
		session_destroy();
		
		// 2. wyświetl stronę logowania z informacją
		getMessages()->addInfo('Poprawnie wylogowano z systemu');

		$this->generateView();		 
	}
	
	public function generateView(){
		
		getSmarty()->assign('page_title','Strona logowania');
		getSmarty()->assign('form',$this->form);
		getSmarty()->display('LoginView.tpl');		
	}
}