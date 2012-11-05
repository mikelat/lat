<?php
class Input {
	public static function get($name) {
		if(isset($_POST[$name])) {
			return $_POST[$name];
		}
		else {
			return null;
		}
	}
}