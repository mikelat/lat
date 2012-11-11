<?php namespace Model;

use DB;

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
return 'test';
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
}