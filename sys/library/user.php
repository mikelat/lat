<?php
class User {
	private static $create_session = false;
	private static $session_data_raw = '';
	private static $session_data;
	private static $session_data_changed = false;
	private static $member_data = array();
	private static $ip_address = "";
	private static $session_id = "";
	private static $lock_updated = array();
	private static $lock = array();

	/* If you change either of these, current passwords will no longer work! */
	const hash_cycles = 500; // how many cycles to has the password
	const hash_algorithm = 'sha512'; // hashing algorithm

	/**
	 * Sets or returns session data
	 *
	 * @param string $name
	 * @param string $value
	 * @return string
	 */
	public static function session($name = null, $value = null) {
		if(!is_array(self::$session_data)) {
			self::$session_data = @unserialize(self::$session_data_raw);
		}

		if($name === null && $value === null) {
			return self::$session_data;
		}
		elseif($name !== null && $value === null) {
			return isset(self::$session_data[$name]) ? self::$session_data[$name] : null;
		}
		elseif($name !== null && $value !== null) {
			self::$session_data_changed = true;
			self::$session_data[$name] = $value;
		}

		return null;
	}

	/**
	 * Returns member data from currently logged in member
	 *
	 * @param string $name
	 * @param string $value
	 * @return string
	 */
	public static function get($name = null, $value = null) {
		if($name === null && $value === null) {
			return self::$member_data;
		}
		elseif($name !== null && $value === null) {
			if($name == 'member_id') {
				return isset(self::$member_data[$name]) ? self::$member_data[$name] : 0;
			}
			else {
				return isset(self::$member_data[$name]) ? self::$member_data[$name] : null;
			}
		}
		elseif($name !== null && $value !== null) {
			self::$member_data[$name] = $value;
		}

		return null;
	}

	/**
	 * Returns ip address
	 *
	 * @return string
	 */
	public static function ip_address() {
		return self::$ip_address;
	}

	/**
	 * Returns session id
	 *
	 * @return string
	 */
	public static function session_id() {
		return self::$session_id;
	}

	/**
	 * Loads up a session
	 */
	public static function load_session() {
		// Load the cache from DB
		Cache::load();

		// Guess the base url
		if(Config::get('url') == "") {
			Config::import('url', (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://'. $_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']));
		}

		$timer = microtime(true);
		self::$ip_address = preg_replace("/[^0-9A-F:.]/", "", strtoupper($_SERVER['REMOTE_ADDR']));
		self::$session_id = self::cookie("session_id");
		$session_query = false;

		// Checking for robots
		if(!self::session_id() && Config::get('bots_enabled'))
		{
			$bot_list = explode("\n", Config::get('bots_list'));

			// Compare known bots with our current user agent
			foreach($bot_list as $bot)
			{
				$bot_info = explode("|", $bot);

				if(preg_match("/{$bot_info[0]}/i", $_SERVER['HTTP_USER_AGENT']) && $bot_info != "")
				{
					self::session('spider', $bot);
					self::$session_id = 'B ' . substr($bot_info[1], 0, 23);
				}
			}
		}

		// Attempt to load the current session
		if(self::session_id() != "")
		{
			$session_query = DB::table('session s')->left_join('member m', 's.member_id=m.member_id')
				->where(array(
						's.ip_address' => self::ip_address()
					,	's.session_id' => self::session_id()
					,	's.user_agent' => substr($_SERVER['HTTP_USER_AGENT'], 0, 255)
				)
			)->row('s.session_data', 'm.*');
		}

		if($session_query !== false) {
			self::$session_data_raw = $session_query['session_data'];
			unset($session_query['session_data']);
			self::$member_data = $session_query;
		}
		else {
			// Delete hour old sessions
			DB::shutdown('session')->delete('session_updated <', time() - 3600);

			// Generate session ID
			if(self::session('spider') === null) {
				self::$session_id = String::random_string(25);
			}

			$member_id = self::cookie('member_id');
			$member_token = self::cookie('token');

			Log::debug('Creating session, account details detected: member_id: [' . $member_id . '] member_token: [' . $member_token . ']');

			if($member_id && $member_token) {
				$member = DB::table('member')->where('member_id', $member_id)->row();
				$tokens = self::get_tokens('login', @unserialize($member['tokens']));

				Log::debug($member . ' ' . (in_array($member_token, $tokens['login']) ? 'yes' : 'no') . ' ' . self::lock());

				if($member !== false && in_array($member_token, $tokens['login']) && self::lock() < 250) {
					self::$member_data['tokens'] = serialize($tokens);
					self::login($member);

					Log::debug('Logged in member: ' . $member['member_id']);
				}
				else {
					self::lock(20);
					self::logout();

					Log::debug('Failed login on member: ' . $member['member_id']);
				}
			}

			self::cookie("session_id", self::session_id());
			self::$create_session = true;
		}

		Log::info('Loaded Session: ' . self::session_id(), microtime(true) - $timer);
	}

	/**
	 * Updates our member session
	 *
	 * @param string $act
	 */
	function update($act="") {
		if(self::$create_session) {
			DB::shutdown('session')->replace(array(
						'session_id' => self::session_id()
					,	'ip_address' => self::ip_address()
					,	'member_id' => self::get('member_id')
					,	'session_updated' => time()
					,	'session_created' => time()
					,	'url_string' => implode('/', Url::get())
					,	'user_agent' => substr($_SERVER['HTTP_USER_AGENT'], 0, 255)
					,	'session_data' => serialize(self::$session_data)
			));
		}
		else {
			DB::shutdown('session')->set(array(
						'session_updated' => time()
					,	'url_string' => implode('/', Url::get())
					,	'member_id' => self::get('member_id')
				), (self::$session_data_changed ? array('session_data' => serialize(self::$session_data)) : array())
			)->update(array(
						'ip_address' => self::ip_address()
					,	'session_id' => self::session_id()
					,	'user_agent' => substr($_SERVER['HTTP_USER_AGENT'], 0, 255)
				)
			);
		}
	}

	/**
	 * Sets, gets, or removes a cookie
	 *
	 * @param string $name
	 * @param string $content
	 * @param string $expires
	 * @return string
	 */
	public static function cookie($name, $content=null, $expires=null) {
		// Erase Cookie
		if($content === "") {
			setcookie($name, "");
			return;
		}
		// Set cookie
		elseif($content !== null) {
			if($expires === null) {
				$expires = time() + 31536000;
			}

			setcookie($name, $content, $expires, "/");
			Log::info('Set cookie "' . $name . '" with value: "' . $content . '"');
		}

		// Return Cookie
		if(isset($_COOKIE[$name])) {
			return $_COOKIE[$name];
		}
		else {
			return null;
		}
	}

	/**
	 * Login a member (assume correct credientials)
	 *
	 * @param string $id
	 * @param string $password
	 * @param string $remember
	 * @return boolean
	 */
	public static function login($member, $remember=false) {
		self::$member_data = $member;

		if($remember) {
			$tokens = self::get_tokens();
			$new_token = String::random_string(20);
			$tokens['login'][time()] = $new_token;
			self::$member_data['tokens'] = serialize($tokens);
			self::cookie('member_id', self::get('member_id'));
			self::cookie('token', $new_token);
		}

		DB::shutdown('member')->set(array(
				'member_updated' => time()
			,	'tokens' => self::$member_data['tokens']
		))->update('member_id', $member['member_id']);

		return true;
	}

	/**
	 * Logout a user, clear cookies
	 */
	public static function logout() {
		self::$member_data = array();

		// Remove our current token
		$tokens = self::get_tokens();
		unset($tokens['login'][array_search(self::cookie('token'), $tokens)]);

		self::cookie('member_id', '');
		self::cookie('token', '');
	}

	/**
	 * Sets or gets the user lock value (helps stop brute force and abuse)
	 *
	 * @param string $value
	 * @return string
	 */
	public static function lock($value=null)
	{
		if(!isset(self::$lock[self::ip_address()])) {
			$lock = DB::table('member_lock')->where(array(
					'ip_address' => self::ip_address()
				,	'lock_updated >' => time() - 900
			))->row('score', 'lock_updated');

			self::$lock[self::ip_address()] = intval($lock['score']);
			self::$lock_updated[self::ip_address()] = intval($lock['lock_updated']);
		}

		if($value !== null) {
			// Delete lock records past the last hour
			DB::shutdown('member_lock')->delete('lock_updated <', time() - 3600);

			self::$lock[self::ip_address()] = self::$lock[self::ip_address()] + $value;

			if(self::$lock[self::ip_address()] > 255) {
				self::$lock[self::ip_address()] = 255;
			}

			DB::shutdown('member_lock')->replace(array(
					'ip_address' => self::ip_address()
				,	'lock_updated' => time()
				,	'score' => self::$lock[self::ip_address()]
			));
		}

		return self::$lock[self::ip_address()];
	}

	/**
	 * Returns last time the lock was appended to
	 *
	 * @param string $ip
	 * @return number
	 */
	public static function lock_updated($ip) {
		return isset(self::$lock_updated[$ip]) ? self::$lock_updated[$ip] : 0;
	}

	/**
	 * Returns error message for lockout
	 *
	 * @return string
	 */
	public static function lock_message() {
		return Load::word('_form', 'error_lockout', ceil((self::lock_updated(self::ip_address()) + 900 - time()) / 60));
	}

	/**
	 * Hashes a password and salt combo
	 *
	 * @param string $password
	 * @param string $salt
	 * @return string
	 */
	public static function hash_password($password, $salt) {
		$hash = openssl_digest($salt . $password, self::hash_algorithm);
		for ($i = 0; $i < self::hash_cycles; $i++) {
			$hash = openssl_digest($hash . $password . $salt, self::hash_algorithm);
		}

		return $hash;
	}

	/**
	 *
	 * @param array $tokens
	 * @param string $type
	 * @return array
	 */
	private static function get_tokens($type='login', $tokens=null) {
		if($tokens === null) {
			$tokens = @unserialize(self::get('tokens'));
		}

		if(!isset($tokens[$type])) {
			$tokens[$type] = array();
		}

		// Clean tokens (if it hasn't been used in 30 days consider it dead
		foreach($tokens[$type] as $last_used => $t) {
			if($last_used < time() - 2592000) {
				unset($tokens[$type][$last_used]);
			}
		}

		return $tokens;
	}
}