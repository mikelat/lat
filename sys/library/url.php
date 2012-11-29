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
	public static function load($url='/', $extra=array()) {
		Load::init();
		Controller\Controller::_clear();

		if(isset($extra['msg'])) {
			echo $extra['msg'];
		}

		self::set($url);
		$class = strtolower(self::get(1));
		$func = Url::get(2);
		$args = array_slice(self::get(), 2);

		$page_found = false;
		if($class == null || !preg_match("/^[a-z][a-z-]*/", $class)) {
			$class = 'forum'; // TODO: replace this later with actual configuration option
		}

		// Starts with number, its a view func
		if(preg_match("/^[0-9]+-.+/", $func)) {
			$func = 'view';
		}
		// Assume index if not valid or
		elseif($func == null || !preg_match("/^[a-z][a-z-]*/", $func)) {
			$func = 'index';
		}
		else {
			$args = array_slice($args, 1);
		}

		// Load the controller
		$controller = Load::controller($class, true);

		// Can't find the controller
		if($controller === false || !is_callable(array($controller, $func))) {
			Log::error("Page not found", 404);
		}

		Load::javascript_var('current_url', $url);

		// Execute controller and render!
		$controller->_class('pg-' . strtolower($class));
		$controller->_class('fn-' . $func);
		$timer = microtime(true);
		$func = str_replace('-', '_', $func);
		call_user_func_array(array($controller, $func), $args);
		Log::info('Executed Controller Method ' . $func . '()', microtime(true) - $timer);
		call_user_func(array($controller, '_buffer'));
		call_user_func_array(array($controller, '_render'), array(null, $extra));
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
	 * Creates a generic slug link
	 *
	 * @param string $type
	 * @param array $arr
	 * @param string $prefix
	 * @return string
	 */
	public static function make_slug($type, $arr, $prefix='') {
		if($prefix !== '') {
			$prefix = $prefix . '_' . $type . '_';
			$id = $prefix . 'id';
		}
		else {
			$id = $type . '_id';
		}

		return '<a class="url-' . $type . ' url-' . $type . '-' . $arr[$id] . '" '
			. 'href="' . Url::make($type . '/' . $arr[$id] . '-' . $arr[$prefix . 'slug'])
			. (isset($arr[$prefix . 'description']) ? '" title="' . $arr[$prefix . 'description'] : '')
			. '">' . $arr[$prefix . 'name'] . '</a>';
	}

	/**
	 * Create member profile link
	 *
	 * @param array $arr
	 * @param string $prefix
	 * @return string
	 */
	public static function make_avatar($arr, $prefix='') {
		if($prefix !== '') {
			$prefix = $prefix . '_member_';
			$id = $prefix . 'id';
		}
		else {
			$id = 'member_id';
		}

		return '<a class="avatar avatar-' . $arr[$id] . '" title="' . $arr[$prefix . 'name'] . '" '
			. 'href="' . Url::make('member/' . $arr[$id] . '-' . $arr[$prefix . 'slug']) . '">'
			. '<img src="' . self::make('avatar.png', true) . '" alt="' . $arr[$prefix . 'name'] . '" /></a>';
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