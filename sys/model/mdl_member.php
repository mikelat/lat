<?php namespace Model;

use DB, Form, String, User, Url, Load;

class Member extends Model {

	private static $name_passed = array();
	private static $name_failed = array();

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

		if(!isset($arr['name']) || !self::name_avaliable($arr['name'])) {
			return false;
		}

		if(!isset($arr['password'])) {
			return false;
		}

		$arr['name'] = trim($arr['name']);
		$arr['password_salt'] = String::random_string(15);
		$arr['password'] = User::hash_password($arr['password'], $arr['password_salt']);
		$arr['member_created'] = time();
		$arr['slug'] = String::make_slug($arr['name']);

		DB::table('member')->insert($arr);
	}

	/**
	 * Checks if display name is avaliable, caches result
	 *
	 * @param string $name
	 * @return boolean
	 */
	public static function name_avaliable($name) {
		$name = trim($name);

		if(in_array($name, self::$name_passed)) {
			return true;
		}

		if(in_array($name, self::$name_failed)) {
			return false;
		}

		if(DB::table('member')->num('name', $name)) {
			self::$name_failed[] = $name;
			return false;
		}
		else {
			self::$name_passed[] = $name;
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

		if(DB::table('member')->num('email_address', $email)) {
			self::$email_address_failed[] = $email;
			return false;
		}
		else {
			self::$email_address_passed[] = $email;
			return true;
		}
	}
}