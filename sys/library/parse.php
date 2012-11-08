<?php
class Parse {

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
}