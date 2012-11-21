<?php namespace Controller;

use Cache, Load, String, DB;

class C_Forum extends Controller {

	public function __construct() {
	}

	/**
	 * View forum index.
	 */
	public function index() {
		Load::view('forum/index', array('forum_list' => self::forum_list()));
	}

	/**
	 * View specific forum
	 *
	 * @param string $slug
	 */
	public function view($slug) {
		$id = String::slug_id($slug);

		$threads = DB::table('thread')->where(array(
				'forum_id' => $id
		))->get();

		Load::view('forum/thread_list', array(
				'forum_list' => self::forum_list($id)
			,	'forum' => Cache::get('forum', $id)
			,	'thread_list' => $threads
		));
	}

	/**
	 * Generate a forum list
	 *
	 * @param number $id
	 * @return string
	 */
	private function forum_list($id=0) {
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

		return null;
	}

	/**
	 * Reload the cache (temporary for development)
	 */
	public function cache() {
		if(ENVIRONMENT === 'development' && $_SERVER['REMOTE_ADDR'] == '192.168.1.1') {
			Cache::reload();
			echo "Cache reloaded successfully.";
		}
		else {
			echo "You don't have permissions for this.";
		}
	}

	/**
	 * Delay the page load with usleep (temporary for development)
	 */
	public function delay() {
		usleep(2000000);
		echo "This page render was delayed by 2 seconds on purpose for testing purposes to emulate a long page load.";
		//echo serialize(array('sql' => array( 'table' => 'forum', 'type' => 'select', 'select' => '*' ), 'slug' => 'slug'));
	}
}