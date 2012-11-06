<?php
class Form {

	private static $validate_fields = array();
	private static $forms = array();

	/**
	 * Returns post variable
	 *
	 * @param stromg $name
	 * @return string
	 */
	public static function get($name=null) {
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
						}
						else {
							$attr .= ' data-validate-' . $validate;

							// store this for PHP later
							self::$validate_fields[$form][$attr['name']][$validate] = true;

							// add it to the li class for any styling changes
							$class[] = 'validate-' . $validate;
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
			return '<li' . (!empty($class) ? ' class="' . implode(' ', $class) . '">' : '>') . $label . '<input' . $attr . ' />';
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
		return '</form>';
	}

	/**
	 * Validate the form
	 *
	 * @param string $form
	 * @return array
	 */
	public static function is_valid($form=null) {
		$return = array('success' => false, 'error' => '');
		if($form === null) {
			$form = end(self::$forms);
		}

		// Not a form, validating, or not posting
		if(!self::get() || self::get('validate') || !isset(self::$validate_fields[$form])) {
			return $return;
		}

		$error = array();

		foreach(self::$validate_fields[$form] as $name => $validate) {
			$value = self::get($name);
			$return[$name] = '';

			foreach($validate as $n => $v) {
				switch($n) {
					case 'min-length':
						if(strlen($value) > $v) {
							if($v == 1) {
								$error[$name] = 'Required';
							}
							else {
								$error[$name] = 'Must be at least ' . $v . ' characters';
							}
						}
					break;

					case 'regex':

					break;
				}
			}
		}

		return $return;
	}
}