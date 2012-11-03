<?php namespace Lat;

class Cookie {

	/**
	 * Return a cookie by name
	 */
	public static function get($name) {
		if(isset($_COOKIE[$name])) {
			return $_COOKIE[$name];
		}
		else {
			return null;
		}
	}

	/**
	 * Send out a cookie by name
	 */
	public static function set($name, $content, $expires=null) {
		if($expires === null) {
			$expires = time() + 31536000;
		}

		setcookie($name, $content, $expire, "/");
	}
}