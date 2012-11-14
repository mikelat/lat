<?php namespace Controller;

use Load, Form, Url, Session, Model\User;

class Account extends Controller {

	public function __construct() {
		Load::model('user');
		Load::library('form');
	}

	public function index() {
		Load::view('account/login');
	}

	public function signup() {
		// Output the Form
		Load::view('account/signup');

		if(Form::request_validate() || Form::request_submit()) {
			$validate = Form::is_valid();
			$display_name = trim(Form::get('display_name'));

			if($display_name) {
				$dn_avaliable = User::display_name_avaliable($display_name);
				$validate['display_name']['msg'] = $dn_avaliable ? Load::word('account', 'good_name', $display_name) : Load::word('account', 'bad_name', $display_name);
				$validate['display_name']['success'] = $dn_avaliable;
				$validate['_success'] = $dn_avaliable ? $validate['_success'] : false;
			}

			if($validate['_success'] && Form::request_submit()) {
				User::create(array(
						'email_address' => Form::get('email_address')
					,	'display_name' => Form::get('display_name')
					,	'password' => Form::get('password')
					,	'ip_address' => Session::ip_address()
				));

				Url::load('/', "<h3>Thanks for signing up! Login coming soon!</h3><br /><br />");
			}
			else {
				$this->_render($validate);
			}
		}
	}
}