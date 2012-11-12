<?php
class Url {

	private static $segments = array();

	/**
	 * Get specified url segment
	 *
	 * @param number $segment
	 * @return string
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

	/**
	 * Loads a new page (also serves to redirect users)
	 *
	 * @param string $url
	 * @param string $message
	 * @param array $extra
	 * @return string
	 */
	public static function load($url='/', $message=null, $extra=array()) {

		if($message !== null) {
			Controller\Controller::_clear();
			echo $message;
		}

		self::set($url);
		$class = strtolower(self::get(1));
		$func = Url::get(2);
		$args = array_slice(self::get(), 2);

		$page_found = false;
		if($class == null) {
			$class = 'forum'; // TODO: replace this later with actual configuration option
		}

		// Determine if we're using index
		if($func == null || !preg_match("/^[a-z][a-z_]*/", $func)) {
			$func = 'index';
		}
		else {
			$args = array_slice($args, 1);
		}

		// Load the controller
		$controller = Load::controller($class);

		// Can't find the controller
		if($controller === false || !is_callable(array($controller, $func))) {
			Log::error("Page not found", 404);
		}

		Load::javascript_var('current_url', $url);

		// Execute controller and render!
		$controller->_class('pg-' . strtolower($class));
		$controller->_class('fn-' . $func);
		call_user_func_array(array($controller, $func), $args);
		call_user_func(array($controller, '_buffer'));
		call_user_func(array($controller, '_render'));
	}

	/**
	 * Makes a link for our site with the base url
	 *
	 * @param string $url
	 * @param string $is_file
	 * @return string
	 */
	public static function make($url="", $is_file=false) {
		if($is_file === false) {
			if(substr($url, -1, 1) !== "/") {
				$url .= "/";
			}

			if($url === "/") {
				$url = "";
			}
		}

		return Config::get('url') . $url;
	}

	/**
	 * Set URI string
	 *
	 * @param string $url
	 */
	public static function set($url) {
		self::$segments = explode("/", $url);
	}
}