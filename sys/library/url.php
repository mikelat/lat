<?php
class Url {

	private static $segments = array();

	/**
	 * Return the url segment that we asked for
	 */
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

	public static function set($url) {
		self::$segments = explode("/", $url);
	}

	/**
	 * Make a URL
	 */
	public static function make($url="") {
		if(substr($url, -1, 1) !== "/") {
			$url .= "/";
		}

		if($url === "/") {
			$url = "";
		}

		return Config::get('url') . $url;
	}
}