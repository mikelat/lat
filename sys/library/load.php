<?php
class Load {

	public static function library($file) {
		require Config::get('path_library') . strtolower($file) . EXT;
		Log::debug("Loaded {$file} library.");
	}

	public static function model($file) {
		require Config::get('path_model') . strtolower($file) . EXT;
		Log::debug("Loaded {$file} model.");
	}

	public static function view($view_file, $view_data=null, $view_return=false) {
		$timer = microtime(true);
		ob_start();
		if(is_array($view_data)) {
			extract($view_data, EXTR_SKIP);
		}
		require Config::get('path_view') . strtolower($view_file) . EXT;
		$out = ob_get_contents();
		ob_end_clean();

		Log::info("Loaded {$view_file} view.", microtime(true) - $timer);

		if($view_return) {
			return $out;
		}
		else {
			echo $out;
		}
	}
}