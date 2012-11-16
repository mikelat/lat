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

	public function delay() {
		usleep(2000000);
		echo "This page render was delayed by 2 seconds on purpose for testing purposes to emulate a long page load.";
	}
}