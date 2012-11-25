<?php namespace Controller;

use Log, Load, User, DB, Output, Config;

class Controller {

	public $_global_classes = array();
	public $_html_buffer = array();

	/**
	 * Starts a buffer and clears the last one if applicable
	 */
	public function _clear() {
		ob_end_clean();

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

	/**
	 * Get HTML buffer
	 *
	 * @return string:
	 */
	public function _buffer() {
		User::update();

		// load debug info if we're in development
		if(ENVIRONMENT === 'development') {
			$this->_debug();
		}

		// grab buffered output
		$this->_html_buffer = ob_get_contents();
		return $this->_html_buffer;
	}

	/**
	 * Output the page!
	 *
	 * @param string $html
	 */
	public function _render($html=null, $extra=array()) {
		ob_end_clean();
		ob_start('ob_gzhandler');

		// Headers
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

		if(isset($_POST['submit']) && $_POST['submit'] || isset($_POST['validate']) && $_POST['validate']) {
			$_POST['json'] = 1;
		}

		// Standard render since nothing was passed
		if($html === null) {

			if(User::get('member_id')) {
				$this->_global_classes[] = 'member-' . User::get('member_id');
				$this->_global_classes[] = 'logged-in';
			}
			else {
				$this->_global_classes[] = 'logged-out';
			}

			$jss = array(
					'url' => Config::get('url')
				,	'date_short' => Config::get('date_short')
				,	'date_long' => Config::get('date_long')
				,	'date_24' => Config::get('date_24')
				,	'date_timezone' => Config::get('date_24')
				,	'time_offset' => User::get('session_time_offset')
				,	'am' => Load::word('_global', 'am')
				,	'pm' => Load::word('_global', 'pm')
                ,   'today' => Load::word('_global', 'today')
                ,   'tommorow' => Load::word('_global', 'tommorow')
                ,   'yesterday' => Load::word('_global', 'yesterday')
			);

			for ($i = 1; $i <= 12; $i++) {
				$jss['month_' . $i] = Load::word('_global', 'month_' . $i);
			}

			for ($i = 1; $i <= 7; $i++) {
				$jss['day_' . $i] = Load::word('_global', 'day_' . $i);
			}

			for ($i = 0; $i <= 9; $i++) {
				$jss['suffix_' . $i] = Load::word('_global', 'suffix_' . $i);
			}

			$classes = implode(" ", $this->_global_classes);

			$output = array(
						'content' => $this->_html_buffer
					,	'classes' => $classes
					,	'jsf' => Load::javascript_file()
					,	'jsv' => Load::javascript_var()
					,	'jss' => $jss
			);

			if(is_array($extra)) {
				$output = array_merge($output, $extra);
			}

			// send back as json array
			if(isset($_POST['json']) && $_POST['json']) {
				echo json_encode($output);
			}

			// just output to the browser
			else {
				echo Load::view('wrapper', $output);
			}
		}
		// This was called directly and therefore is overriding the default render output
		else {
			if(is_array($html)) {
				$html = json_encode($html);
			}
			echo $html;
		}

		ob_end_flush();
		DB::shutdown_exec(); // execute any pending shutdown queries
		exit();
	}

	/**
	 * Adds a new string to html classes
	 *
	 * @param string $class
	 */
	public function _class($class) {
		$this->_global_classes[] = $class;
	}

	/**
	 * Output debug information
	 */
	public function _debug() {
		$log = Log::get();
		Load::view('debug', array(
				'log' => $log
			,	'version' => VERSION
			,	'queries' => Log::$query_total
			,	'shutdown_queries' => Log::$query_shutdown
			,	'query_time' => number_format(Log::$query_time, 5) . "s"
			,	'exec_time' => number_format(microtime(true) - TIMER, 5) . "s"
			,	'memory' => memory_get_peak_usage()
		));
	}
}