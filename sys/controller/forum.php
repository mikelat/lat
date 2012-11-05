<?php
class Forum extends Controller {

	public static function index() {
		Load::view('forum/list');
	}

	public static function cache() {
		if(ENVIRONMENT === 'development') {
			Cache::reload();
			echo "Cache reloaded successfully.";
		}
	}
}