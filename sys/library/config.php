<?php namespace Lat;

class Config {

	private static $config = array();

	/**
	 * Return a configuration option in the array
	 */
	public static function get() {
		$args = func_get_args();
		$cfg =& static::$config;

		if(is_array($args[0])) {
			$args = $args[0];
		}

		if(count($args) > 0) {
			foreach($args as $a)
			{
				$cfg =& $cfg[$a];
			}
		}
		return $cfg;
	}

	/**
	 * Add configuration options into our array
	 */
	public static function import($cfg) {
		static::$config = array_merge(static::$config, $cfg);
	}
}