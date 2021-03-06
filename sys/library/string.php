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
	 * Creates a slug for a url
	 *
	 * @param string $string
	 * @return mixed
	 */
	public static function make_slug($string='') {
		return str_replace(' ', '-', preg_replace('/[^0-9A-Za-z \.]/', '_', utf8_decode($string)));
	}

	/**
	 * Find string length from UTF8 strings
	 *
	 * @param string $str
	 * @return number
	 */
	public static function length($str='') {
		return strlen(utf8_decode($str));
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

		$server_offset = User::get('session_time_offset') * 60;
		$gmt_time = gmdate("Y-m-d H:i", $time);

		$time += $server_offset;
		$now += $server_offset;

		// Time format
		if(Config::get('date_24')) {
			$parsed_time = gmdate('G:i', $time);
		}
		else {
			$parsed_time = gmdate('g:i', $time) . Load::word('_global', gmdate('a', $time));
		}

		// Today
		if(gmdate("dmy", $now) == gmdate("dmy", $time)) {
			$date = Load::word('_global', 'today', $parsed_time);
		}
		// Yesterday
		elseif(gmdate("dmy", $now - 86400) == gmdate("dmy",$time)) {
			$date = Load::word('_global', 'yesterday', $parsed_time);
		}
		// Tommorow
		elseif(gmdate("dmy", $now + 86400) == gmdate("dmy",$time)) {
			$date = Load::word('_global', 'tommorow', $parsed_time);
		}
		// Standard Date
		else {
			// Converts time date placeholders
			$date = preg_replace_callback('/\[(s|d|dd|m|mm|yy|yyyy|day|month)\]/',
			function ($match) use ($time) {
				switch($match[1]) {
					case 'd':
						return gmdate('j', $time);
					case 'dd':
						return gmdate('d', $time);
					case 'm':
						return gmdate('n', $time);
					case 'mm':
						return gmdate('m', $time);
					case 'yy':
						return gmdate('y', $time);
					case 'yyyy':
						return gmdate('Y', $time);
					case 'day':
						return Load::word('_global', 'day_' . gmdate('N', $time));
					case 'month':
						return Load::word('_global', 'month_' . gmdate('n', $time));
					case 's':
						return Load::word('_global', 'suffix_' . substr(gmdate('j', $time), -1, 1));
				}
			}, $long ? Config::get('date_long') : Config::get('date_short')) . ' ' . $parsed_time;
		}
		return '<time datetime="' . $gmt_time . 'Z" data-unix="' . $time . '"' . ($long ? ' data-long' : '') .'>' . $date . '</time>';
	}
}