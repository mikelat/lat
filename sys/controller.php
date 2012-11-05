<?php
class Controller {

	/*
	 * Initalize page for buffering
	 */
	public static function _init() {

		// Page buffer error handling
		$ob_error_handling = function($html) {
			$error = error_get_last();
			if ($error && $error["type"] == E_USER_ERROR || $error["type"] == E_ERROR) {
				return Log::halt($error['message'] . "\nOccured in " . $error['file'] . " on line " . $error['line'], true);
			}
			return $html;
		};

		// Start buffering
		ob_start($ob_error_handling);
	}

	/*
	 * All done, render the page
	 */
	public static function _render() {
		// load debug info if we're in development
		if(ENVIRONMENT === 'development') {
			self::_debug();
		}

		// grab buffered output
		$html = ob_get_contents();
		ob_end_clean();

		// compress and output!
		ob_start('ob_gzhandler');
		if(!Input::get('json')) {
			echo Load::view('wrapper', array('html' => $html));
		}
		else {
			echo json_encode(array('content' => $html, 'js_files' => Output::js_file(), 'js_vars' => Output::js()));
		}
		ob_end_flush();
	}

	/*
	 * Output debug info
	 */
	public static function _debug() {
		$log = Log::get();
		Load::view('debug', array(
				'log' => $log
			,	'version' => VERSION
			,	'queries' => Log::$query_total
			,	'query_time' => number_format(Log::$query_time, 5) . "s"
			,	'exec_time' => number_format(microtime(true) - TIMER, 5) . "s"
		));
	}
}