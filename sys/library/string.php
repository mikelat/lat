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

	/**
	 * Get specified slug id
	 *
	 * @param number $segment
	 * @return string
	 */
	public static function slug_id($string) {
		if(preg_match('/([0-9]+)-.+/', $string, $matches)) {
			return $matches[1];
		}
		return null;
	}

	/**
	 * Return a random string of letters/numbers
	 *
	 * @param number $length
	 * @return string
	 */
	public static function number_format($number) {
		return number_format($number);
	}

	/**
	 * Outputs a formatted date string with time tags
	 *
	 * @param number $time
	 * @param string $long
	 * @return string
	 */
	public static function time_format($time=0, $long=false)
	{
		$now = time();
		$date = '';

		// No time was given
		if(!$time) {
			$time = $now;
		}

		if(gmdate("dmy", $now) == gmdate("dmy", $time)) {
			$date = Load::word('_global', 'date_today', date('g:ia', $time));
		}
		// Yesterday!
		elseif(gmdate("dmy", $now - 86400) == gmdate("dmy",$time)) {
			$date = Load::word('_global', 'date_yesterday', date('g:ia', $time));
		}
		// Tommorow!
		elseif(gmdate("dmy", $now + 86400) == gmdate("dmy",$time)) {
			$date = Load::word('_global', 'date_tommorow', date('g:ia', $time));
		}
		else {
			$date = date('Y-m-d g:ia', $time);
		}

		return '<time datetime="' . gmdate("Y-m-d H:i", $now) . 'Z">' . $date . '</time>';
	}
}