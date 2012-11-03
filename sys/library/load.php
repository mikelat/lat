<?php namespace Lat;

class Load {

	public static function library($file) {
		require Config::get('path', 'library') . strtolower($file) . EXT;
	}

	public static function model($file) {
		require Config::get('path', 'model') . strtolower($file) . EXT;
	}

	public static function view($file, $data=array(), $return=false) {
		require Config::get('path', 'view') . strtolower($file) . EXT;

	    $callback = function($matches) use ($data) {
	    	switch($matches[1]) {
	    		case 'cfg':
	    			if($matches[2][0] !== 'sql') {
						Config::get($matches[2]);
	    			}
	    			break;
	    		case 'var':
					if(isset($data[$matches[2]])) {
						return $data[$matches[2]];
					}
	    			break;
	    	}
	    };

		$out = preg_replace_callback("/<!-- (var|cfg):([A-Za-z0-9_-]+) -->/", $callback, $out);

		if($return) {
			return $out;
		}
		else {
			echo $out;
		}
	}
}