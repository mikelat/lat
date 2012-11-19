<?php namespace Controller;

use Cache, Load, User, DB, Model\Forum as Forum;

class C_Forum extends Controller {

	public function __construct() {
		Load::model('forum');
	}

	/**
	 * View forum index.
	 */
	public function index() {
		Load::view('forum/index', array('forums' => self::forum_list()));
	}

	/**
	 * View specific forum
	 *
	 * @param string $slug
	 */
	public function view($slug) {
		$id = Cache::slug('forum', $slug);

		$threads = DB::table('thread')->where(array(
				'forum_id' => $id
		))->get();

		Load::view('forum/thread_list', array(
				'forum_list' => self::forum_list($id)
			,	'forum' => Cache::get('forum', $id)
		));
	}

	/**
	 * Generate a forum list
	 *
	 * @param number $id
	 * @return string
	 */
	private function forum_list ($id=0) {
		$forums = Cache::get('forum');

		if(!empty($forums)) {
			$forums_parent = array();

			foreach($forums as $f) {
				$forums_parent[$f['parent']][] = $f;
			}

			if($id > 0) {
				$forums_parent = isset($forums_parent[$id]) ? $forums_parent[$id] : '';
			}

			return $forums_parent;
		}

		return '';
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
		//echo serialize(array('sql' => array( 'table' => 'forum', 'type' => 'select', 'select' => '*' ), 'slug' => 'slug'));
	}
}