<?php
class Parse {

	/**
	 * Encode HTML for safe use
	 *
	 * @param string $val
	 * @return string
	 */
	public static function encode($val) {
		return htmlspecialchars($val, ENT_HTML5);
	}
}