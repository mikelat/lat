<?php namespace Model;

Use Url, DB;

class Thread extends Model {

	function get($fid) {
		return DB::table('thread')->where('forum_id', $fid)->get();
	}

	function replies($id) {

	}
}