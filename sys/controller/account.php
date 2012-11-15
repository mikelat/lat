<?php namespace Controller;

use Load, Form, Url, DB, Session, Model\User;

class Account extends Controller {

	public function __construct() {
		Load::model('user');
		Load::library('form');
	}

	public function index() {
		Load::view('account/login');
print_r(Session::user());
		if(Form::request_submit()) {
			$validate = Form::is_valid();

			// Check for lockout
			if(Session::lock() >= 150 && $validate['_success']) {
					$validate['_msg'] = Session::lock_message();
					$validate['_success'] = false;
			}

			if($validate['_success']) {
				$user = DB::table('user')->where('email_address', Form::get('email_address'))->row();

				if($user === false || md5($user['password_salt'] . Form::get('password')) !== $user['password']) {
					$validate['_msg'] = Load::word('account', 'error_login');
					$validate['_success'] = false;
					Session::lock(10);

					// Add captcha if we've used up our 5 chances
					if(Session::lock() >= 50) {
						$validate['_captcha'] = true;
					}
				}
			}

			// Successful login!
			if($validate['_success']) {
				Session::login($user, Form::get('remember_me'));
				Url::load('/', "<h3>Wow!! That was a successful login!!!</h3><br /><br />");
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
				$dn_avaliable = User::display_name_avaliable($display_name);
				$validate['display_name']['msg'] = $dn_avaliable ? Load::word('account', 'good_name', $display_name) : Load::word('account', 'bad_name', $display_name);
				$validate['display_name']['success'] = $dn_avaliable;
				$validate['_success'] = $dn_avaliable ? $validate['_success'] : false;
			}

			// Check if email is already being used
			if($validate['_success'] && Form::request_submit()) {
				if(Session::lock() < 150 && !User::email_address_avaliable(Form::get('email_address'))) {
					Session::lock(10); // adds 10 to lock score
					$validate['_msg'] = Load::word('account', 'error_email_address_used', Form::get('email_address'));
					$validate['_success'] = false;
				}

				if(Session::lock() >= 150) {
					$validate['_msg'] = Session::lock_message();
					$validate['_success'] = false;
				}
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