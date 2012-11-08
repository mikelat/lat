<?php

Load::language('_form');

class Form {

	private static $validate_fields = array();
	private static $forms = array();
	private static $regex = array(
			'email'    => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/'
		,	'password' => '/^(?=.*(\d|\W))/' // at least one non-alphabetical character
	);
	private static $language_buffer = array();
	private static $regex_buffer = array();

	/**
	 * Returns an encoded input for increased safety
	 *
	 * @param string $name
	 * @return string
	 */
	public static function get($name=null) {
		return Parse::html_encode(self::raw($name));
	}

	/**
	 * Returns raw post variable
	 *
	 * @param string $name
	 * @return string
	 */
	public static function raw($name=null) {
		if(isset($_POST[$name])) {
			return $_POST[$name];
		}
		elseif($name === null) {
			return $_POST;
		}
		else {
			return null;
		}
	}

	/**
	 * Detects if we're submitting a form
	 * @param string $name
	 * @return boolean
	 */
	public static function request_submit($name=null) {
		return (boolean) self::get('submit');
	}

	/**
	 * Detects if we're submitting a form
	 * @param string $name
	 * @return boolean
	 */
	public static function request_validate($name=null) {
		return (boolean) self::get('validate') || self::get('submit');
	}

	/**
	 * Sets or gets a regex string
	 *
	 * @param string $name
	 * @return string
	 */
	public static function regex($name, $value=null) {
		if($value === null && isset(self::$regex[$name])) {
			return self::$regex[$name];
		}
		elseif($value !== null) {
			self::$regex[$name] = $value;
		}
		else {
			return null;
		}
	}

	/**
	 * Create input from array
	 *
	 * @param array $arr
	 * @return string
	 */
	public static function input($arr, $return=false) {

		$attr = "";
		$label = "";
		$class = array();
		$required = false;
		$form = end(self::$forms);

		if(!isset($arr['type'])) {
			$arr['type'] = 'text';
		}

		foreach($arr as $n => $a) {
			switch($n) {

				// Name attribute is also ID
				case 'name':
					$attr .= ' name="' . $a . '" id="' . $a . '"';
				break;

				// Validation for PHP and JS
				case 'validate':
					foreach($a as $validate) {
						if(strpos($validate, ':') !== false) {
							$val = explode(":", $validate);
							$attr .= ' data-validate-' . $val[0] . '="' . $val[1] . '"';

							// store this for PHP later
							self::$validate_fields[$form][$attr['name']][$val[0]] = $val[1];

							// add it to the li class for any styling changes
							$class[] = 'validate-' . $val[0];

							if($val[0] == 'maxlength') {
								$attr .= ' maxlength="' . $val[1] . '"';
							}

							if($val[0] == 'regex') {
								self::$regex_buffer[$val[1]] = self::regex($val[1]);
								self::store_error('regex-' . $val[1]);
							}
							else {
								self::store_error($val[0], $val[1]);
							}
						}
						else {
							$attr .= ' data-validate-' . $validate;

							// store this for PHP later
							self::$validate_fields[$form][$attr['name']][$validate] = true;

							// add it to the li class for any styling changes
							$class[] = 'validate-' . $validate;

							// store language entry for javascript later
							self::store_error($validate);
						}
					}
				break;

				// Label is put into its own var to put in the beginning
				case 'label':
					$label = '<label>' . $arr['label'] . '</label>';
				break;

				// Assume it's a regular HTML attr
				default:
					if($n === $a) {
						$attr .= ' ' . $n;
					}
					else {
						$attr .= ' ' . $n . '="' . $a . '"';
					}
				break;
			}
		}

		// standard return
		if(!$return) {
			return '<li' . (!empty($class) ? ' class="' . implode(' ', $class) . '">' : '>') . $label . '<input' . $attr . ' /><p class="error-msg"></p></li>';
		}
		// returning vars instead of standard li output
		else {
			return array($attr, $label, $class);
		}
	}

	/**
	 * Outputs a form for opening
	 *
	 * @param string $name
	 * @param boolean $multipart
	 * @param string $url
	 * @return string
	 */
	public static function open_form($name, $multipart=false, $url=null) {
		$attr = ' method="post" id="' . $name . '" name="' . $name . '"';

		if($url === null) {
			$attr .= ' action="' . implode('/', Url::get()) . '"';
		}

		Load::javascript_file('validate.js');
		self::$forms[] = $name;
		return '<form' . $attr . '>';
	}

	/**
	 * Returns form closure
	 *
	 * @return string
	 */
	public static function close_form() {
		self::$language_buffer['_errors'] = Load::word('_form', '_errors');
		self::$language_buffer['_ajax'] = Load::word('_form', '_ajax');

		if(!empty(self::$language_buffer)) {
			Load::javascript_var('form_language', self::$language_buffer);
		}

		if(!empty(self::$regex_buffer)) {
			Load::javascript_var('regex', self::$regex_buffer);
		}

		return '</form>';
	}

	/**
	 * Validate the form
	 *
	 * @param string $form
	 * @return array
	 */
	public static function is_valid($form=null) {
		$return = array('success' => false, 'error' => array());
		if($form === null) {
			$form = end(self::$forms);
		}

		// Not a form, validating, or not posting.
		if((!self::request_validate() && !self::request_submit()) || !isset(self::$validate_fields[$form])) {
			return $return;
		}

		$return['success'] = true;

		// It's a validation request, which won't submit. Ignore validation.
		if(self::request_validate() && !self::request_submit()) {
			return $return;
		}

		foreach(self::$validate_fields[$form] as $name => $validate) {
			$value = self::get($name);

			foreach($validate as $n => $v) {
				switch($n) {
					case 'minlength':
						if(strlen($value) < $v) {
							$return['error'][$name] = self::language_error($n, $v);
							$return['success'] = false;
						}
					break;

					case 'maxlength':
						if(strlen($value) > $v) {
							$return['error'][$name] = self::language_error($n, $v);
							$return['success'] = false;
						}
					break;

					case 'regex':
						if(!preg_match(self::regex($v), $value)) {
							$return['error'][$name] = self::language_error('regex-' . $v);
							$return['success'] = false;
						}
					break;

					case 'match':
						if(self::get($name) !== self::get($v)) {
							$return['error'][$name] = self::language_error($n, $v);
							$return['success'] = false;
						}
					break;
				}
			}
		}

		return $return;
	}

	/**
	 * Returns a language entry for an error
	 *
	 * @param string $name
	 * @param string $value
	 * @return string
	 */

	private static function language_error($name, $value=null) {
		if($value !== null && Load::word('_form', $name . '-' . $value) !== null) {
			return Load::word('_form', $name . '-' . $value);
		}

		if(Load::word('_form', $name) !== null) {
			return Load::word('_form', $name, ucfirst(str_replace("_", " ", $value)));
		}
	}

	/**
	 * Store a language error entry for JS
	 *
	 * @param string $name
	 * @param string $value
	 * @return string
	 */

	private static function store_error($name, $value=null) {
		if($value !== null && Load::word('_form', $name . '-' . $value) !== null) {
			self::$language_buffer[$name . '-' . $value] = Load::word('_form', $name . '-' . $value);
		}

		if(Load::word('_form', $name) !== null) {
			self::$language_buffer[$name] = Load::word('_form', $name);
		}
	}
}