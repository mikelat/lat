<?php
class Config {

	private static $config = array();

	/**
	 * Return a configuration option in the array
	 */
	public static function get($cfg = null) {

		if($cfg === null) {
			return static::$config;
		}

		return isset(static::$config[$cfg]) ? static::$config[$cfg] : null;
	}

	/**
	 * Add configuration options into our array
	 */
	public static function import() {
		$args = func_get_args();

		if(count($args) > 1) {
			$args = array($args[0] => $args[1]);
		}
		else {
			$args = $args[0];
		}

		static::$config = array_merge(static::$config, $args);
	}
}