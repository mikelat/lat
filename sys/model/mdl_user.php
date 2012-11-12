<?php namespace Model;

use DB, Form, String, Load;

class User extends Model {

	private static $display_name_passed = array();
	private static $display_name_failed = array();

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

		if(!isset($arr['email_address']) || !preg_match(Form::regex('email'), $arr['email_address'])) {
			return false;
		}

		if(!isset($arr['password'])) {
			return false;
		}

		$arr['password_salt'] = String::random_string(10);
		$arr['password'] = md5($arr['password_salt'] . $arr['password']);

		DB::table('user')->insert($arr);
	}

	/**
	 * Checks if display name is avaliable, caches result
	 *
	 * @param string $name
	 * @return boolean
	 */
	public static function display_name_avaliable($name) {
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

	private static function generate_salt() {
	}
}