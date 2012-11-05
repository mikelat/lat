<?php
class Account extends Controller {

	public static function index() {
		echo "this is the account page";
	}

	public static function signup() {
		Load::library('form');
		Load::view('account/signup');
	}
}
