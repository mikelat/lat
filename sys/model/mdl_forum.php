<?php namespace Model;

Use Url;

class Forum extends Model {
	public static function link($forum) {
		return '<a title="' . $forum['description'] . '" href="' . Url::make('forum/view/' . $forum['slug']) . '">' . $forum['name'] . '</a>';
	}
}