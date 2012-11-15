<?php
class Driver {
	private static $shutdown_queries = array();
	private static $connection = null;
	protected static $cfg = array();

	/**
	 * Execute a database query
	 */
	public static function query($query, $data=null, $shutdown=false) {
		$q = self::$connection->prepare($query);

		$timer = microtime(true);
		$q->execute($data);
		$error = $q->errorInfo();

		if($error[0] !== '00000' && ENVIRONMENT === 'development') {
			Log::halt("Database Query Error: " . $error[2] . "\n\nQuery Ran: " . $q->queryString);
		}

		if($shutdown === false) {
			Log::query(str_replace(array_keys($data), array_values( explode("||||", '"' . implode('"||||"', $data) . '"') ), $q->queryString), microtime(true) - $timer);
		}

		return $q;
	}

	/**
	 * Adds a shutdown query
	 */
	public static function shutdown_query($query, $data=null) {
		self::$shutdown_queries[] = array($query, $data);
		Log::query(str_replace(array_keys($data), array_values( explode("||||", '"' . implode('"||||"', $data) . '"') ), $query));
	}

	/**
	 * Execute shutdown queries
	 */
	public static function shutdown_exec() {
		foreach(self::$shutdown_queries as $sq) {
			self::query($sq[0], $sq[1], true);
		}
	}

	/**
	 * Return the database prefix
	 */
	public static function prefix($table) {
		return self::$cfg['prefix'] . $table;
	}

	/**
	 * Return the connection object, connect if necessary
	 */
	public static function load($sql_cfg) {

		self::$cfg = $sql_cfg;

		// create a new connection
		$connection_string = "mysql:host=" . self::$cfg['host'] . ";dbname=" . self::$cfg['database'];
		self::$connection = new \PDO($connection_string, self::$cfg['username'], self::$cfg['password']);
	}

	/**
	 * Return the connection object, connect if necessary
	 */
	public static function build($sql) {

		$query = "";
		$data = array();

		switch($sql['type']) {
			case 'select':
				if(is_array($sql['select'])) {
					$sql['select'] = implode(", ", $sql['select']);
				}
				$query = "SELECT " . $sql['select'] . " FROM ";
				break;
			case 'update':
				$query = "UPDATE ";
				break;
			case 'delete':
				$query = "DELETE FROM ";
				break;
			case 'insert':
				$query = "INSERT INTO ";
				break;
			case 'replace':
				$query = "REPLACE INTO ";
				break;
		}

		$query .= self::prefix($sql['table']);

		// JOINS
		if(isset($sql['join'])) {
			foreach($sql['join'] as $join) {
				$query .= " " . $join[0] . " JOIN " . self::prefix($join[1]) . " ON " . $join[2];
			}
		}

		// SET
		if(isset($sql['set'])) {
			foreach($sql['set'] as $name => $value) {
				$set[] = $name . "=:v".count($data);
				$data[':v'.count($data)] = $value;
			}
			$query .= " SET " . implode(", ", $set);
		}

		// WHERE
		if(isset($sql['where'])) {
			$query .= " WHERE " . self::parse_where($sql['where'], $data);
		}

		// GROUP BY
		if(isset($sql['group'])) {
			if(is_array($sql['group'])) {
				$sql['group'] = implode(", ", $sql['group']);
			}
			$query .= " GROUP BY " . $sql['group'];
		}

		// ORDER BY
		if(isset($sql['order'])) {
			if(is_array($sql['order'])) {
				$sql['order'] = implode(", ", $sql['order']);
			}
			$query .= " ORDER BY " . $sql['order'];
		}

		// LIMIT
		if(isset($sql['limit'])) {
			if($sql['limit'][1] === null) {
				$query .= " LIMIT " . intval($sql['limit'][0]);
			} else {
				$query .= " LIMIT " . intval($sql['limit'][0]) . "," . intval($sql['limit'][1]);
			}
		}

		// INSERT/REPLACE
		if(isset($sql['insert'])) {
			$query .= ' ('. implode(', ', array_keys($sql['insert'][0])) . ') VALUES';

			foreach($sql['insert'] as $ins) {
				$i = array();
				foreach($ins as $v) {
					$i[] = ':v'.count($data);
					$data[':v'.count($data)] = $v;
				}
				$query .= ' (' . implode(", ", $i) .')';
			}
		}

		return array($query, $data);
		/*
			// GROUP BY
			if($query['group'])
			{
				$construct .= " GROUP BY ".$query['group'];
			}
			// HAVING
			if($query['having'])
			{
				$construct .= " HAVING ".$query['having'];
			}
		 */
	}

	/**
	 * Executes built database query and returns an object
	 */

	private function parse_where($where, &$data, $brackets = false) {
		$where_string = "";

		foreach($where as $n => $w) {

			// nested where
			if(is_array($w) && is_int($n)) {
				$where_string .= self::parse_where($w, $data, false);

			}
			else {
				// and/or modifier
				if($where_string !== "") {
					if(substr(strtoupper($n), 0, 3) === "OR ") {
						$where_string .= " OR ";
					} else {
						$where_string .= " AND ";
					}
				}

				$where_string .= $n;

				// symbols everywhere
				if(ctype_alpha(substr($n, "-1", "1"))) {
					$where_string .= "=";
				} else {
					$where_string .= " ";
				}

				$where_string .= ":v".count($data);
				$data[":v".count($data)] = $w;
			}
		}

		// return with brackets (nested where statement)
		if($brackets) {
			return "(" . $where_string . ")";
		}
		else {
			return $where_string;
		}
	}
}