<?php namespace Model;

use DB, Form, String, User, Url, Load;

class Member extends Model {

	private static $display_name_passed = array();
	private static $display_name_failed = array();

	private static $email_address_passed = array();
	private static $email_address_failed = array();

	/**
	 * Creates user if possible based upon inputted variables
	 *
	 * @param string $name
	 * @return boolean
	 */
	public static function create($arr) {
		Load::library('form');

		if(!isset($arr['display_name']) || !self::display_name_avaliable($arr['display_name'])) {
			return false;
		}

		if(!isset($arr['password'])) {
			return false;
		}

		$arr['display_name'] = trim($arr['display_name']);
		$arr['password_salt'] = String::random_string(15);
		$arr['password'] = User::hash_password($arr['password'], $arr['password_salt']);
		$arr['user_created'] = time();

		DB::table('user')->insert($arr);
	}

	/**
	 * Checks if display name is avaliable, caches result
	 *
	 * @param string $name
	 * @return boolean
	 */
	public static function display_name_avaliable($name) {
		$name = trim($name);

		if(in_array($name, self::$display_name_passed)) {
			return true;
		}

		if(in_array($name, self::$display_name_failed)) {
			return false;
		}

		if(DB::table('user')->num('display_name', $name)) {
			self::$display_name_failed[] = $name;
			return false;
		}
		else {
			self::$display_name_passed[] = $name;
			return true;
		}
	}

	/**
	 * Checks if email address is avaliable, caches result
	 *
	 * @param string $email
	 * @return boolean
	 */
	public static function email_address_avaliable($email) {
		if(in_array($email, self::$email_address_passed)) {
			return true;
		}

		if(in_array($email, self::$email_address_failed)) {
			return false;
		}

		if(DB::table('user')->num('email_address', $email)) {
			self::$email_address_failed[] = $email;
			return false;
		}
		else {
			self::$email_address_passed[] = $email;
			return true;
		}
	}
}