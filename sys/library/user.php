<?php
class User {
	private static $create_session = false;
	private static $session_data = array();
	private static $user_data = array();
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
	 * @return multitype:|Ambigous <NULL, multitype:>|NULL
	 */
	public static function session($name = null, $value = null) {
		if($name === null && $value === null) {
			return self::$session_data;
		}
		elseif($name !== null && $value === null) {
			return isset(self::$session_data[$name]) ? self::$session_data[$name] : null;
		}
		elseif($name !== null && $value !== null) {
			self::$session_data[$name] = $value;
		}

		return null;
	}

	/**
	 * Returns user data from currently logged in user
	 *
	 * @param string $name
	 * @param string $value
	 * @return string
	 */
	public static function get($name = null, $value = null) {
		if($name === null && $value === null) {
			return self::$user_data;
		}
		elseif($name !== null && $value === null) {
			if($name == 'user_id') {
				return isset(self::$user_data[$name]) ? self::$user_data[$name] : 0;
			}
			else {
				return isset(self::$user_data[$name]) ? self::$user_data[$name] : null;
			}
		}
		elseif($name !== null && $value !== null) {
			self::$user_data[$name] = $value;
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
		$timer = microtime(true);
		self::$ip_address = preg_replace("/[^0-9A-F:.]/", "", strtoupper($_SERVER['REMOTE_ADDR']));
		self::$session_id = self::cookie("session_id");
		$session_query = false;

		// Checking for robots
		if(Config::get('bots_on'))
		{
			$bot_list = explode("\n", Config::get('bots_list'));

			// Compare known bots with our current user agent
			foreach($bot_list as $bot)
			{
				$bot_info = explode("|", $bot);

				if(preg_match("/{$bot[0]}/i", $_SERVER['HTTP_USER_AGENT']) && $bot_info != "")
				{
					self::session('spider', $bot_info);
					self::$session_id = substr($bot[1], 0, 25);
				}
			}
		}

		// Attempt to load the current session
		if(self::session_id() != "")
		{
			$session_query = DB::table('session s')->left_join('user u', 's.user_id=u.user_id')
				->where(array(
						's.ip_address' => self::ip_address()
					,	's.session_id' => self::session_id()
					,	's.user_agent' => substr($_SERVER['HTTP_USER_AGENT'], 0, 255)
				)
			)->row('s.session_data', 'u.*');
		}

		if($session_query !== false) {
			self::$session_data = $session_query['session_data'];
			unset($session_query['session_data']);
			self::$user_data = $session_query;
		}
		else {
			// Delete hour old sessions
			DB::shutdown('session')->delete('session_updated <', time() - 3600);

			// Generate session ID
			if(self::session('spider') === null) {
				self::$session_id = String::random_string(25);
			}

			$user_id = self::cookie('user_id');
			$user_token = self::cookie('token');

			Log::debug('Creating session, account details detected: user_id: [' . $user_id . '] user_token: [' . $user_token . ']');

			if($user_id && $user_token) {
				$user = DB::table('user')->where('user_id', $user_id)->row();
				$tokens = self::get_tokens('login', @unserialize($user['token']));

				Log::debug($user . ' ' . (in_array($user_token, $tokens['login']) ? 'yes' : 'no') . ' ' . self::lock());

				if($user !== false && in_array($user_token, $tokens['login']) && self::lock() < 250) {
					self::$user_data['token'] = serialize($tokens);
					self::login($user);

					Log::debug('Logged in user: ' . $user['user_id']);
				}
				else {
					//self::lock(10);
					//self::logout();

					Log::debug('Failed login on user: ' . $user['user_id']);
				}
			}

			self::cookie("session_id", self::session_id());
			self::$create_session = true;
		}

		Log::info('Loaded Session: ' . self::session_id(), microtime(true) - $timer);
	}

	/**
	 * Updates our user session
	 *
	 * @param string $act
	 */
	function update($act="")
	{
		if(self::$create_session) {
			DB::shutdown('session')->replace(array(
						'session_id' => self::session_id()
					,	'ip_address' => self::ip_address()
					,	'user_id' => self::get('user_id')
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
					,	'session_data' => serialize(self::$session_data)
					,	'user_id' => self::get('user_id')
				))->update(array(
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
	 * @return void|unknown|NULL
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
	 * Login a user (assume correct credientials)
	 *
	 * @param string $id
	 * @param string $password
	 * @param string $remember
	 * @return boolean
	 */
	public static function login($user, $remember=false) {
		self::$user_data = $user;

		if($remember) {
			$tokens = self::get_tokens();
			$new_token = String::random_string(20);
			$tokens['login'][time()] = $new_token;
			self::$user_data['token'] = serialize($tokens);
			self::cookie('user_id', self::get('user_id'));
			self::cookie('token', $new_token);
		}

		DB::shutdown('user')->set(array(
				'user_updated' => time()
			,	'token' => self::$user_data['token']
		))->update('user_id', $user['user_id']);

		return true;
	}

	/**
	 * Logout a user, clear cookies
	 */
	public static function logout() {
		self::$user_data = array();

		// Remove our current token
		$tokens = self::get_tokens();
		unset($tokens['login'][array_search(self::cookie('token'), $tokens)]);

		self::cookie('user_id', '');
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
			$lock = DB::table('user_lock')->where(array(
					'ip_address' => self::ip_address()
				,	'lock_updated >' => time() - 900
			))->row('score', 'lock_updated');

			self::$lock[self::ip_address()] = intval($lock['score']);
			self::$lock_updated[self::ip_address()] = intval($lock['lock_updated']);
		}

		if($value !== null) {
			// Delete lock records past the last hour
			DB::shutdown('user_lock')->delete('lock_updated <', time() - 3600);

			self::$lock[self::ip_address()] = self::$lock[self::ip_address()] + $value;

			if(self::$lock[self::ip_address()] > 255) {
				self::$lock[self::ip_address()] = 255;
			}

			DB::shutdown('user_lock')->replace(array(
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
			$tokens = @unserialize(self::get('token'));
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