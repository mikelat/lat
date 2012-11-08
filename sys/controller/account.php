<?php namespace Controller;

use Load, Form, Parse, Model\User;

class Account extends Controller {

	public function index() {
		echo "this is the account page";
		usleep(2000000);
	}

	public function signup() {
		Load::library('form');
		Load::model('user');

		// Output the Form
		Load::view('account/signup');

		if(Form::request_validate() || Form::request_submit()) {

			$validate = Form::is_valid();
			$display_name = Form::get('display_name');

			if($display_name) {
				$dn_avaliable = User::display_name_avaliable($display_name);
				$validate['error']['display_name'] = $dn_avaliable ? Load::word('account', 'good_name', $display_name) : Load::word('account', 'bad_name', $display_name);
				$validate['success'] = $dn_avaliable ? $validate['success'] : false;
			}

			if($validate['success'] && Form::request_submit()) {
				User::create(array(
						'email_address' => Form::get('email_address')
					,	'display_name' => Form::get('display_name')
					,	'password' => Form::get('password')
				));
			}
			else {
				$this->_render($validate);
			}
		}
	}
}