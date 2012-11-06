<?php
class Load {
	// javascript data storage
	private static $js_var = array();
	private static $js_file = array();

	// languages
	private static $lang = array();

	/**
	 * Loads a library file
	 *
	 * @param string $file
	 */
	public static function library($file) {
		require_once Config::get('path_library') . strtolower($file) . EXT;
		Log::debug("Loaded {$file} library.");
	}

	/**
	 * Loads a model file
	 *
	 * @param string $file
	 */
	public static function model($file) {
		require_once Config::get('path_model') . strtolower($file) . EXT;
		Log::debug("Loaded {$file} model.");
	}

	/**
	 * Loads a view and renders it
	 *
	 * @param string $view_file
	 * @param array $view_data
	 * @param boolean $view_return
	 * @return string
	 */
	public static function view($view_file, $view_data=null, $view_return=false) {
		$timer = microtime(true);
		ob_start();
		if(is_array($view_data)) {
			extract($view_data, EXTR_SKIP);
		}
		require Config::get('path_view') . strtolower($view_file) . EXT;
		$out = ob_get_contents();
		ob_end_clean();

		Log::info("Loaded {$view_file} view.", microtime(true) - $timer);

		if($view_return) {
			return $out;
		}
		else {
			echo $out;
		}
	}

	/**
	 * Loads a language file
	 *
	 * @param string $file
	 */
	public static function language($file) {
		if(file_exists(Config::get('path_language') . strtolower($file) . EXT)) {
			require Config::get('path_language') . strtolower($file) . EXT;
			self::$lang[$file] = $language;
			Log::debug("Loaded {$file} language file.");
		}
	}

	/**
	 * Returns a word from a language file
	 *
	 * @param string $file
	 */
	public static function word($file, $word) {
		if(isset(self::$lang[$file][$word])) {
			return self::$lang[$file][$word];
		}
		else {
			return null;
		}
	}

	/**
	 * Loads a Javascript File onto the page
	 *
	 * @param string $file
	 * @return string
	 */
	public static function javascript_file($file=null) {
		// adding a new file
		if($file !== null) {
			if(parse_url($file, PHP_URL_HOST) === null) {
				$file = Url::make('js/' . $file, true);
			}

			if(!in_array($file, self::$js_file)) {
				self::$js_file[] = $file;
			}
		}
		// returning everything
		else {
			return self::$js_file;
		}
	}

	/**
	 * Save JS variable for output, or return a js var
	 */
	public static function javascript_var($name=null, $value=null) {
		// setting value from array
		if(is_array($name)) {
			self::$js_var = array_merge(self::$js_var, $name);
		}
		// setting value
		elseif($value !== null) {
			self::$js_var[$name] = $value;
		}
		// grabbing specific var
		else if($value === null && $name !== null) {
			return self::$js_var[$name];
		}
		// returning everything
		else {
			return self::$js_var;
		}
	}
}