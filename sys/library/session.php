<?php
class Session {

	public static function load() {
		$ip_address = preg_replace("/[^0-9A-F:.]/", "", strtoupper($_SERVER['REMOTE_ADDR']));
		$session_id = preg_replace("/[^0-9A-F.]/", "", strtoupper(Input::cookie("sid")));



		/*
		$cookie_sid = $this->get_cookie("sid");
		$ip = preg_replace("{[^0-9/.]}", "", $_SERVER['REMOTE_ADDR']);
		$sid = $this->lat->parse->preg_whitelist($cookie_sid, "a-z0-9");

		if($this->lat->cache['config']['bots_on'])
		{
			$bot_list = explode("\n", $this->lat->cache['config']['bots_list']);

			// Compare known bots with our current user agent
			foreach($bot_list as $bot)
			{
				$bot = explode("|", $bot);

				if(preg_match("{{$bot[0]}}i", $_SERVER['HTTP_USER_AGENT']) && $bot != "")
				{
					$this->lat->user['spider'] = $bot[1]."|".$bot[2];
					$cookie_sid = $sid = substr($bot[1], 0, 10);
				}
			}
		}

		// Let's hope that we can save one extra query!
		if($sid)
		{
			// Query: Fetch the session with our user too (we hope) :3
			$query = array("select" => "s.ip, s.sid, s.key, s.act, s.spider, s.captcha, s.escalated, u.*",
						   "from"   => "kernel_session s",
						   "left"   => array("user u ON (s.uid=u.id)"),
						   "where"  => "s.ip='{$ip}' AND s.sid='{$sid}'");

			$session_query = $this->lat->sql->query($query);
		}

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
}