<?php namespace Controller;

use Log, Load, Output;

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
	public function _render($html=null) {
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
			$classes = implode(" ", $this->_global_classes);

			// send back as json array
			if(isset($_POST['json']) && $_POST['json']) {
				echo json_encode(array('content' => $this->_html_buffer, 'classes' => $classes, 'jsf' => Load::javascript_file(), 'jsv' => Load::javascript_var()));
			}

			// just output to the browser
			else {
				echo Load::view('wrapper', array(
						'html' => $this->_html_buffer
					,	'classes' => $classes
				));
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
			,	'query_time' => number_format(Log::$query_time, 5) . "s"
			,	'exec_time' => number_format(microtime(true) - TIMER, 5) . "s"
		));
	}
}