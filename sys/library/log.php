<?php namespace Lat;

class Log {

	private static $log = array();

	public static function query($query, $time) {
		self::$log[] = array('query', $query, $time);
	}

	public static function debug($msg) {
		self::$log[] = array('debug', $msg);
	}

	public static function info($msg) {
		self::$log[] = array('info', $msg);
	}

	public static function error($msg) {
		self::$log[] = array('error', $msg);
	}

	public static function get($type=null) {
		return self::$log;
	}
}