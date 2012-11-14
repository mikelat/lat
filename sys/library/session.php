<?php
class Session {
	private static $create_session = false;
	private static $session_data = array();
	private static $user_data = array();
	private static $ip_address = "";
	private static $session_id = "";

	public static function data($name = null, $value = null) {
		if($name === null && $value === null) {
			return static::$session_data;
		}
		elseif($name !== null && $value === null) {
			return isset(static::$session_data[$name]) ? static::$session_data[$name] : null;
		}
		elseif($name !== null && $value !== null) {
			static::$session_data[$name] = $value;
		}

		return null;
	}

	public static function user($name = null, $value = null) {
		if($name === null && $value === null) {
			return static::$user_data;
		}
		elseif($name !== null && $value === null) {
			return isset(static::$user_data[$name]) ? static::$user_data[$name] : null;
		}
		elseif($name !== null && $value !== null) {
			static::$user_data[$name] = $value;
		}

		return null;
	}

	public static function ip_address() {
		return self::$ip_address;
	}

	public static function id() {
		return self::$session_id;
	}

	public static function load() {
		$timer = microtime(true);
		self::$ip_address = preg_replace("/[^0-9A-F:.]/", "", strtoupper($_SERVER['REMOTE_ADDR']));
		self::$session_id = preg_replace("/[^0-9A-Z-a-z.]/", "", self::cookie("session_id"));
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
					self::data('spider', $bot_info);
					self::$session_id = substr($bot[1], 0, 25);
				}
			}
		}

		// Attempt to load the current session
		if(self::id() != "")
		{
			$session_query = DB::table('session s')->left_join('user u', 's.user_id=u.user_id')
				->where(array(
						's.ip_address' => self::ip_address()
					,	's.session_id' => self::id()
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
			// Generate session ID
			if(self::data('spider') === null) {
				self::$session_id = String::random_string(25);
			}

			self::cookie("session_id", self::id());
			self::$create_session = true;
		}

		Log::info('Loaded Session: ' . self::id(), microtime(true) - $timer);

		/*

		// Let's hope that we can save one extra query!

		// Old session
		if($session_query['sid'])
		{
			$this->lat->user = $session_query;
		}
		// New session
		else
		{
			// Check password cookie
			$c_user = $this->lat->parse->unsigned_int($this->get_cookie("user"));
			$c_pass = $this->get_cookie("pass");

			if($c_user && $c_pass)
			{
				// Query: Get the details of the user we want to be
				$query = array("select" => "u.*",
							   "from"   => "user u",
							   "where"  => "u.id=".$c_user);

				$user_fetch = $this->lat->sql->query($query);

				// Check for password attempt abuse
				$anum = $this->check_abuse($ip);

				// User details are a match!
				if($user_fetch['pass'] == $c_pass && !$user_fetch['validate'] && $anum != -1)
				{
					$this->lat->user = $user_fetch;

					// Query: Delete previous sessions logged in as that user, ip, or old
					$query = array("delete"   => "kernel_session",
								   "where"    => "(ip='{$ip}' AND uid=0) OR uid={$c_user} OR last_time < ".(time() - ($this->lat->cache['config']['session_length'] * 60)),
								   "shutdown" => 1);

					$this->lat->sql->query($query);

					// Query: Update our last login (cookies count as a login)
					$query = array("update"   => "user",
								   "set"      => array("last_login" => time()),
								   "where"    => "id=".$c_user,
								   "shutdown" => 1);

					$this->lat->sql->query($query);
				}
				// Incorrect details
				else
				{
					$c_user = 0;
					$c_pass = "";
					$this->out_cookie("user", "", true);
					$this->out_cookie("pass", "", true);
					$this->add_abuse();
				}
			}

			if(!$this->lat->user['id'])
			{
				// Query: Delete other session guests with our IP, or old sessions
				$query = array("delete"   => "kernel_session",
							   "where"	  => "(ip='{$ip}' AND uid=0) OR last_time < ".(time() - ($this->lat->cache['config']['session_length'] * 60)));

				$this->lat->sql->query($query);
			}

			// Generate Standard Key - One that doesn't refresh upon pageloads
			$this->lat->user['key'] = substr(md5(uniqid(microtime())), 0, 10);

			// Generate session ID
			if($this->lat->user['spider'])
			{
				$this->lat->user['sid'] = $sid;
			}
			else
			{
				do {
					$this->lat->user['sid'] = substr(md5(uniqid(microtime())), 0, 10);

					// Query: Check if the session ID already exists
					$query = array("select" => "count(uid) as num",
								   "from"   => "kernel_session",
								   "where"  => "sid='{$this->lat->user['sid']}'");

					$exec = $this->lat->sql->query($query);

				} while ($exec['num']);
			}

		 	$this->create = true;

			// Attempt to send the sid cookie
			$cookie_sid = "";
			$this->out_cookie("sid", $this->lat->user['sid']);
		}

		// Cookies are disabled, use url sid then :(
		if($cookie_sid == "")
		{
			$this->lat->url = $this->lat->cache['config']['script_url']."index.php?sid={$this->lat->user['sid']};";
		}
		else
		{
			$this->lat->url = $this->lat->cache['config']['script_url']."index.php?";
		}

		// Turn null into zero
		if(!$this->lat->user['id'])
		{
			$this->lat->user['id'] = 0;
		}

		// This user is using default long date format
		if($this->lat->user['long_date'] == "")
		{
			$this->lat->user['long_date'] = $this->lat->cache['config']['long_date'];
		}

		// This user is using default short date format
		if($this->lat->user['short_date'] == "")
		{
			$this->lat->user['short_date'] = $this->lat->cache['config']['short_date'];
		}

		$this->lat->user['ip'] = $ip;

		// If there isn't a group, you're a guest!
		if(!$this->lat->user['gid'])
		{
			$this->lat->user['gid'] = 3;
		}

		// Throw in group permissions in here
		$this->lat->user['group'] = $this->lat->cache['group'][$this->lat->user['gid']];
		*/
	}

	function update($act="")
	{
		if(self::$create_session) {
			DB::table('session')->insert(array(
						'session_id' => self::id()
					,	'ip_address' => self::ip_address()
					,	'user_id' => 0
					,	'session_updated' => time()
					,	'session_created' => time()
					,	'url_string' => implode('/', Url::get())
					,	'user_agent' => substr($_SERVER['HTTP_USER_AGENT'], 0, 255)
			));
		}
		else {
			DB::table('session')->set(array(
						'session_updated' => time()
					,	'url_string' => implode('/', Url::get())
				))->update(array(
						'ip_address' => self::ip_address()
					,	'session_id' => self::id()
					,	'user_agent' => substr($_SERVER['HTTP_USER_AGENT'], 0, 255)
				)
			);
		}
	}

	/**
	 * Returns or sets a cookie
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
			return $content;
		}

		// Return Cookie
		if(isset($_COOKIE[$name])) {
			return $_COOKIE[$name];
		}
		else {
			return null;
		}
	}
}