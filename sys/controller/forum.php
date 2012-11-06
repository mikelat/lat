<?php namespace Controller;

use Cache, Load;

class Forum extends Controller {

	public function index() {
		Load::view('forum/list');
	}

	public function cache() {
		if(ENVIRONMENT === 'development' && $_SERVER['REMOTE_ADDR'] == '192.168.1.1') {
			Cache::reload();
			echo "Cache reloaded successfully.";
		}
		else {
			echo "You don't have permissions for this.";
		}
	}
}