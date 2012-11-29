<?php namespace Controller;

use Load, String, Model\Thread;

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
		$id = String::slug_id($slug);
		Load::view('thread/thread_index', array('replies' => Thread::replies($id)));
	}
}