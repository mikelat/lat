<?php namespace Controller;

use Load;

class C_Thread extends Controller {

	public function __construct() {
		Load::model('thread');
	}

	/**
	 * View specific thread
	 *
	 * @param string $slug
	 */
	public function view($slug) {
		echo 'this is where thread viewing will go!';
	}
}