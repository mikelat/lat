<?php namespace Model;

Use Url;

class Forum extends Model {

	public static function link($name, $id, $slug='') {
		if($slug === '') {
			return $name;
		}
		else {
			return '<a href="' . Url::make('forum/view/' . $slug) . '">' . $name . '</a>';
		}
	}
}