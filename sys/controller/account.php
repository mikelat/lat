<?php namespace Controller;

use Load, Form, Url, DB, User, Model\Account;

class C_Account extends Controller {

	public function __construct() {
		Load::model('account');
		Load::library('form');
	}

	public function index() {
		if(!User::get('user_id')) {
			Url::load('/account/login');
		}

		Load::view('account/account');
	}

	public function login() {
		Load::view('account/login');

		if(Form::request_submit()) {
			$validate = Form::is_valid();
			$user = DB::table('user')->where('display_name', Form::get('display_name'))->row();

			// 25 failed attempts = complete lockout
			if(User::lock() >= 250 && $validate['_success']) {
					$validate['_msg'] = User::lock_message();
					$validate['_success'] = false;
			}

			if($user === false && $validate['_success']) {
				$validate['_msg'] = Load::word('account', 'error_display_name_not_found');
				$validate['_success'] = false;
			}

			if($validate['_success']) {

				if(User::hash_password(Form::get('password'), $user['password_salt']) !== $user['password']) {
					$validate['_msg'] = Load::word('account', 'error_login');
					$validate['_success'] = false;
					User::lock(10);

					// Add captcha if we've used up our 5 chances
					if(User::lock() >= 100) {
						$validate['_captcha'] = true;
					}
				}
			}

			// Successful login!
			if($validate['_success']) {
				User::login($user, Form::get('remember_me'));
				Url::load('/', array('msg' => "<h3>Wow!! That was a successful login!!!</h3><br /><br />", 'header' => Load::view('header', null, true)));
			}
			else {
				$this->_render($validate);
			}
		}
	}

	public function signup() {
		// Output the Form
		Load::view('account/signup');

		if(Form::request_validate() || Form::request_submit()) {
			$validate = Form::is_valid();
			$display_name = trim(Form::get('display_name'));

			// Check if display name is taken
			if($display_name) {
				$dn_avaliable = Account::display_name_avaliable($display_name);
				$validate['display_name']['msg'] = $dn_avaliable ? Load::word('account', 'good_name', $display_name) : Load::word('account', 'bad_name', $display_name);
				$validate['display_name']['success'] = $dn_avaliable;
				$validate['_success'] = $dn_avaliable ? $validate['_success'] : false;
			}

			// Check if email is already being used
			/*
			if($validate['_success'] && Form::request_submit()) {
				if(User::lock() < 150 && !Model_Account::email_address_avaliable(Form::get('email_address'))) {
					User::lock(10); // adds 10 to lock score
					$validate['_msg'] = Load::word('account', 'error_email_address_used', Form::get('email_address'));
					$validate['_success'] = false;
				}

				if(User::lock() >= 150) {
					$validate['_msg'] = User::lock_message();
					$validate['_success'] = false;
				}
			}
			*/

			if($validate['_success'] && Form::request_submit()) {
				Account::create(array(
						'display_name' => $display_name
					,	'password' => Form::get('password')
					,	'ip_address' => User::ip_address()
				));

				Url::load('/', array('msg' => "<h3>Thanks for signing up! You can now login!</h3><br /><br />", 'header' => Load::view('header', null, true)));
			}
			else {
				$this->_render($validate);
			}
		}
	}

	public function logout() {
		// Output the Form
		Load::view('account/signup');
		User::logout();
		Url::load('/', array('msg' => "Sucessfully logged you out!<br><br>", 'header' => Load::view('header', null, true)));
	}
}