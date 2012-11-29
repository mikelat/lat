<?php namespace Controller;
use Cache, Load, String, Model\Forum, Model\Thread;
use DB; // temporary for development

class C_Forum extends Controller {

	public function __construct() {
		Load::model('forum');
	}

	/**
	 * View forum index
	 */
	public function index() {
		$forum_list = Load::view('forum/forum_list', array(
				'forum_list' => Forum::get_parents()
		), true);

		Load::view('forum/forum_index', array('forum_list' => $forum_list));
	}

	/**
	 * View specific forum
	 *
	 * @param string $slug
	 */
	public function view($slug) {
		Load::model('thread');
		$id = String::slug_id($slug);

		// Generate sub-forum list
		$forum_list = Load::view('forum/forum_list', array('forum_list' => array(
				0 => array(array('forum_id' => $id, 'name' => Load::word('forum', 'sub_forums')))
			,	$id => Forum::get_parents($id))
		), true);

		Load::view('forum/thread_list', array(
				'forum_list' => $forum_list
			,	'forum' => Cache::get('forum', $id)
			,	'thread_list' => Thread::get($id)
		));
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