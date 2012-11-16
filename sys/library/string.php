<?php
class String {

	/**
	 * Encode HTML for safe use
	 *
	 * @param string $val
	 * @return string
	 */
	public static function html_encode($val) {
		if(is_array($val)) {
			return array_map('htmlspecialchars', $val);
		}
		else {
			return htmlspecialchars($val);
		}

	}

	/**
	 * Return a random string of letters/numbers
	 *
	 * @param number $length
	 * @return string
	 */
	public static function random_string($length=5) {
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!@#$%^&*()_+-=~;:<>,.?{}[]';
		$char_len = strlen($characters) - 1;
		$result = '';
		for ($i = 0; $i < $length; $i++) {
			$result .= $characters[mt_rand(0, $char_len)];
		}
		return $result;
	}
}