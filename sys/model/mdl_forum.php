<?php namespace Model;

Use Cache;

class Forum extends Model {

	/**
	 * Generate a forum list
	 *
	 * @param number $id
	 * @return string
	 */
	public function get_parents($id=0) {
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
}