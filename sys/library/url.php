<?php namespace Lat;

class Url {
	
	protected static $segments = array();
	
	public static function set($url) {
		self::$segments = explode("/", $url);
	}
	
	public static function get($segment=0) {
		
		// 0 is our signal to return everything
		if($segment == 0) {
			return self::$segments;
		}
		
		// return specific part if it exists
		elseif(isset(self::$segments[$segment])) {
			return self::$segments[$segment];
		}
		
		return null;
	}
}