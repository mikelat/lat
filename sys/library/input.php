<?php
class Input {

	public static function get() {
		Config::get();
	}

	/**
	 * Returns or sets a cookie
	 */
	public static function cookie($name, $content=null, $expires=null) {

		// Erase Cookie
		if($content === "") {
			setcookie($name, "");
			return;
		}
		// Set cookie
		elseif($content !== null) {
			if($expires === null) {
				$expires = time() + 31536000;
			}

			setcookie($name, $content, $expire, "/");
			Log::info('Set cookie "' . $name . '" with value: "' . $value . '"');
			return $content;
		}

		// Return Cookie
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
	public static function set_cookie($name, $content, $expires=null) {
	}
}