<?php
class Output {

	private static $js = array();
	private static $js_file = array();

	/**
	 * Save JS variable for output, or return a js var
	 */
	public static function js($name=null, $value=null) {
		// setting value from array
		if(is_array($name)) {
			self::$js = array_merge(self::$js, $name);
		}
		// setting value
		elseif($value !== null) {
			self::$js[$name] = $value;
		}
		// grabbing specific var
		else if($value === null && $name !== null) {
			return self::$js[$name];
		}
		// returning everything
		else {
			return self::$js;
		}
	}

	/**
	 * Save JS variable for output, or return a js var
	 */
	public static function js_file($file=null) {
		// adding a new file
		if($file !== null) {
			if(parse_url($file, PHP_URL_HOST) === null) {
				$file = Url::make('js/' . $file, true);
			}

			self::$js_file[] = $file;
		}
		// returning everything
		else {
			return self::$js_file;
		}
	}
}