<?php

class Cache {

	private static $raw_cache = array();
	private static $cache = array();

	public static function get() {
		$args = func_get_args();

		// can't find the cache you're looking for
		if(!isset(static::$raw_cache[$args[0]])) {
			return null;
		}

		// we found it but it hasn't been unserialized
		if(isset(static::$raw_cache[$args[0]]) && !isset(static::$cache[$args[0]])) {
			self::$cache[$args[0]] = unserialize(self::$raw_cache[$args[0]]['cache_content']);
		}

		$cache =& static::$cache[$args[0]];

		// more than one argument means we're returning something very specific
		if(count($args) > 1) {
			array_shift($args);
			foreach($args as $a)
			{
				$cache =& $cache[$a];
			}
		}

		return $cache;
	}

	/**
	 * Load all cache as raw for storage
	 */
	public static function load() {
		 foreach(DB::table('cache')->get() as $query) {
		 	self::$raw_cache[$query['cache_name']] = $query;
		 }

		 Config::import(self::get('configuration'));
	}

	/**
	 * Reload a cache (or all of them)
	 */
	public static function reload($name=null) {
		// We're updating everything
		if($name === null) {
			array_walk(array_keys(self::$raw_cache), "self::reload");
			return true;
		}
		// Invalid cache detected
		elseif(empty(self::$raw_cache[$name])) {
			return false;
		}

		$cache_data = unserialize(self::$raw_cache[$name]['cache_query']);
		$build = DB::build($cache_data['sql']);
		$query = DB::query($build[0], $build[1])->fetchAll(\PDO::FETCH_ASSOC);
		$new_cache = array();

		// 2 columns means its a key
		if(!empty($query)) {
			if(count($query[0]) === 2) {
				foreach($query as $q) {
					$new_cache[reset($q)] = end($q);
				}
			}
			else {
				// If no array id is set use the first column grabbed from the query
				if(!isset($cache_data['array_id'])) {
					$cache_data['array_id'] = key($query[0][0]);
				}

				foreach($query as $q) {
					$new_cache[$q[$cache_data['array_id']]] = $q;
				}
			}
		}

		// Update our cache in a query
		self::$cache[$name] = $new_cache;
		DB::table('cache')->set('cache_content', serialize($new_cache))->update('cache_name', $name);
		return true;
	}

	/**
	 * Used for configuration cache reloading, to make underscores into arrays
	 */
	private static function set_configuration_cache(&$cache, $key, $value) {
		if(count($key) > 1) {
			return self::set_configuration_cache($cache[array_shift($key)], $key, $value);
		}
		else {
			return $cache[$key[0]] = $value;
		}
	}
}