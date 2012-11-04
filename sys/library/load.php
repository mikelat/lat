<?php
class Load {

	public static function library($file) {
		require Config::get('path-library') . strtolower($file) . EXT;
		Log::info("Loaded {$file} library.");
	}

	public static function model($file) {
		require Config::get('path-model') . strtolower($file) . EXT;
		Log::info("Loaded {$file} model.");
	}

	public static function view($view_file, $view_data=null, $view_return=false) {
		ob_start();
		if(is_array($view_data)) {
			extract($view_data, EXTR_SKIP);
		}
		require Config::get('path-view') . strtolower($view_file) . EXT;
		$out = ob_get_contents();
		ob_end_clean();

		Log::info("Loaded {$view_file} view.");

		if($view_return) {
			return $out;
		}
		else {
			echo $out;
		}
	}
}