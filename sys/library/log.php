<?php
class Log {

	private static $log = array();
	public static $query_time = 0;
	public static $query_total = 0;

	public static function query($query, $time=0) {
		self::$query_time += $time;
		self::$query_total ++;
		self::$log[] = array('query', $query, $time);
	}

	public static function debug($msg, $time=0) {
		self::$log[] = array('debug', $msg, $time);
	}

	public static function info($msg, $time=0) {
		self::$log[] = array('info', $msg, $time);
	}

	public static function error($msg, $code=500) {
		self::$log[] = array('error', $msg, 0);
		self::halt($msg . "\nError Code: " . $code);
	}

	public static function get($type=null) {
		return self::$log;
	}

	/**
	 * 404 errors
	 */

	/**
	 * For times when proper error messages won't work
	 */
	public static function halt($msg, $return=false) {

		$html = <<<HTML
<!doctype html>
<html lang="en">
<title>Kernel Error</title>
<div style="font: 14px 'Helvetica Neue', Helvetica, Arial, sans-serif;">
<h1 style="color: #d68c31">Latova Error</h1>
<blockquote style="font-size: 15px;">
	Oops! An internal error occured!<br />
	The error has been logged and the administratior has been notified.
</blockquote>
HTML;

		if(ENVIRONMENT === 'development') {
			$html .= <<<HTML
<blockquote>
	<div style="color:red">System currently in debugging mode. Outputting error:</div>
	<textarea rows="6" cols="80" style="border: 1px red solid; padding: 2px;">{$msg}</textarea>
</blockquote>
HTML;
		}

		$html .= '<blockquote>You can reload the page by clicking <a href="javascript:history.go();">here</a>.</blockquote></div>';

		if($return === false) {
			ob_end_clean();
			die($html);
		}
		else {
			return $html;
		}
	}
}