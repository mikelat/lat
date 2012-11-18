<?php

class Cache {

	private static $raw_cache = array();
	private static $cache = array();
	private static $slugs = array();

	/**
	 * Grabs stored cache (unserializes if necessary)
	 *
	 * @return string|array
	 */
	public static function get() {
		$args = func_get_args();

		// we found it but it hasn't been unserialized
		if(isset(static::$raw_cache[$args[0]]) && !isset(static::$cache[$args[0]])) {
			self::$cache[$args[0]] = unserialize(self::$raw_cache[$args[0]]['content']);
		}
		// can't find the cache you're looking for
		elseif(!isset(static::$raw_cache[$args[0]])) {
			return null;
		}

		if(func_num_args() > 1) {
			return static::$cache[$args[0]][$args[1]];
		}
		else {
			return static::$cache[$args[0]];
		}
	}

	/**
	 * Grabs stored slug (unserializes if necessary)
	 *
	 * @return string|array
	 */
	public static function slug() {
		$args = func_get_args();

		// we found it but it hasn't been unserialized
		if(isset(static::$raw_cache[$args[0]]) && !isset(static::$slugs[$args[0]])) {
			self::$slugs[$args[0]] = unserialize(self::$raw_cache[$args[0]]['slugs']);
		}
		// can't find the cache you're looking for
		elseif(!isset(static::$raw_cache[$args[0]])) {
			return null;
		}

		if(func_num_args() > 1) {
			return static::$slugs[$args[0]][$args[1]];
		}
		else {
			return static::$slugs[$args[0]];
		}
	}

	/**
	 * Loads up all the caches
	 */
	public static function load() {
		foreach(DB::table('cache')->get() as $query) {
			self::$raw_cache[$query['name']] = $query;
		}

		Config::import(self::get('configuration'));
	}

	/**
	 * Reloads a cache, serializes and updates entry in DB
	 *
	 * @param string $name
	 * @return boolean
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

		$cache_data = unserialize(self::$raw_cache[$name]['query']);
		$build = DB::build($cache_data['sql']);
		$query = DB::query($build[0], $build[1])->fetchAll(\PDO::FETCH_ASSOC);
		$new_cache = $new_slugs = array();

		// 2 columns means its a key
		if($query) {
			if(count($query[0]) === 2) {
				foreach($query as $q) {
					$new_cache[reset($q)] = end($q);
				}
			}
			else {
				// If no array id is set use the first column grabbed from the query
				if(!isset($cache_data['array_id'])) {
					$cache_data['array_id'] = key($query[0]);
				}

				// Set slugs if set
				if(isset($cache_data['slug'])) {
					foreach($query as $q) {
						if($q[$cache_data['slug']] != '') {
							$new_slugs[$q[$cache_data['slug']]] = $q[$cache_data['array_id']];
						}
					}
				}

				foreach($query as $q) {
					$new_cache[$q[$cache_data['array_id']]] = $q;
				}
			}
		}

		// Update our cache in a query
		self::$slugs[$name] = $new_slugs;
		self::$cache[$name] = $new_cache;
		DB::shutdown('cache')->set(array('content' => serialize($new_cache), 'slugs' => serialize($new_slugs)))->update('name', $name);
		return true;
	}
}