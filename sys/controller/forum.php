<?php namespace Controller;

use Cache, Load, Model\Forum as Forum;

class C_Forum extends Controller {

	public function __construct() {
		Load::model('forum');
	}

	public function index() {//var_dump(Cache::get('forum'));
		$forums = Cache::get('forum');
		$forums_parent = array();

		if(!empty($forums)) {
			foreach($forums as $f) {
				$forums_parent[$f['parent']][] = $f;
			}
		}

		//echo serialize(array('sql' => array( 'table' => 'forum', 'type' => 'select', 'select' => '*' ), 'slug' => 'slug'));
		Load::view('forum/list', array('forums_parent' => $forums_parent));
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