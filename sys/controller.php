<?php namespace Lat\Controller;

use Lat\Log;
use Lat\Load;

class Controller {

	static function _load() {
		//mb_http_output("UTF-8");
		//ob_start("mb_output_handler");
	}

	static function _render() {

		Load::view('wrapper', array('html' => ''));
		self::_debug();
		//$html = ob_get_contents();
		//ob_end_clean();

		//ob_start('ob_gzhandler');
		//echo $html;
		//ob_end_flush();
	}

	static function _debug() {
		$log = Log::get();
		$out = "<ul>";
		$query_time = 0;
		foreach($log as $l) {
			$out .= "<li class='{$l[0]}'>[{$l[0]}] {$l[1]} (executed in ".number_format($l[2], 6)."s)</li>";
			$query_time += $l[2];
		}
		;
		$out .= "</ul>";
		Load::view('debug', array('log' => $out,
			'version' => VERSION,
			'queries' => count($log),
			'query-time' => number_format($query_time, 5) . "s",
			'exec-time' => number_format(microtime(true) - TIMER, 5) . "s"));
	}
}