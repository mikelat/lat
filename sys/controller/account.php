<?php namespace Controller;

use Load, Form, Model\User;

class Account extends Controller {

	public function index() {
		echo "this is the account page";
		usleep(2000000);
	}

	public function signup() {
		Load::library('form');
		Load::view('account/signup');
		Load::model('user');
		$validate = Form::is_valid();

		// Ajax Validation
		if(Form::get('validate')) {
			return $this->_render(Form::response(User::display_name_avaliable(), "That display name has been taken."));
		}
		elseif($validate['success']) {
			User::create(array(
					'email_address' => Form::get('email_address')
				,	'display_name' => Form::get('display_name')
				,	'password' => Form::get('password')
			));
		}
	}
}