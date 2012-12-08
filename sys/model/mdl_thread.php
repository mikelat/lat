<?php namespace Model;

Use Url, DB;

class Thread extends Model {

	function get($fid) {
		return DB::table('thread t')->where('t.forum_id', $fid)
			->left_join('member ms', 't.start_member_id=ms.member_id')
			->left_join('member ml', 't.last_member_id=ml.member_id')
			->get('t.*', 'ms.name as start_member_name', 'ms.slug as start_member_slug', 'ml.name as last_member_name', 'ml.slug as last_member_slug');
	}

	function replies($id) {
		return DB::table('thread_reply r')->where('r.thread_id', $id)
			->left_join('member m', 'r.member_id=m.member_id')
			->get('r.*', 'm.name', 'm.slug', 'm.member_created', 'm.member_updated');
	}
}