<?php
class Driver {

	private static $connection = null;
	protected static $cfg = array();

	/**
	 * Execute a database query
	 */
	public static function query($query, $data=null) {
		$q = self::$connection->prepare($query);

		if(!$q) {
			echo 'test';

		}

		$timer = microtime(true);
		$q->execute($data);
		$error = $q->errorInfo();

		if($error[0] !== '00000' && ENVIRONMENT === 'development') {
			Log::halt('Database Query Error: ' . $error[2] . "\n\nQuery Ran: " . $q->queryString);
		}

		Log::query(str_replace(array_keys($data), array_values( explode(",", '"' . implode('","', $data) . '"') ), $q->queryString), microtime(true) - $timer);

		return $q;
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
				$query = "DELETE ";
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
				$query .= ' ' . $join[0] . ' JOIN ' . self::prefix($join[1]) . ' ON ' . $join[2];
			}
		}

		// SET
		if(isset($sql['set'])) {
			foreach($sql['set'] as $name => $value) {
				$set[] = $name . "=:v".count($data);
				$data[":v".count($data)] = $value;
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

			// LEFT JOIN
			if($query['left'])
			{
				if(is_array($query['left']))
				{
					foreach ($query['left'] as $left_join)
						$construct .= " LEFT JOIN ".$this->lat->config['SQL_PREF'].$left_join;
				}
				else
				{
					$construct .= " LEFT JOIN ".$this->lat->config['SQL_PREF'].$query['left'];
				}
			}

			// RIGHT JOIN
			if($query['right'])
			{
				if(is_array($query['right']))
				{
					foreach ($query['right'] as $right_join)
						$construct .= " RIGHT JOIN ".$this->lat->config['SQL_PREF'].$right_join;
				}
				else
				{
					$construct .= " RIGHT JOIN ".$this->lat->config['SQL_PREF'].$query['right'];
				}
			}

			// INNER JOIN
			if($query['inner'])
			{
				if(is_array($query['inner']))
				{
					foreach ($query['inner'] as $inner_join)
						$construct .= " INNER JOIN ".$this->lat->config['SQL_PREF'].$inner_join;
				}
				else
				{
					$construct .= " INNER JOIN ".$this->lat->config['SQL_PREF'].$query['inner'];
				}
			}

			// SET
			if($query['set'])
			{
				if(is_array($query['set']))
				{
					foreach($query['set'] as $col => $val)
					{
						if(substr($col, -1) == "=")
						{
							$set[] = $col.$val;
						}
						else
						{
							if(strpos($val, "'") !== false)
							{
								$val = addslashes($val);
							}
							$set[] = "{$col}='{$val}'";
						}
					}
					$construct .= " SET ".implode(", ", $set);
				}
				else
				{
					$construct .= " SET ".$query['set'];
				}
			}
			// WHERE
			if($query['where'])
			{
				$construct .= " WHERE ".$query['where'];
			}
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
				if(substr($n, "-1", "1") == ">") {
					$where_string .= ">";
				}
				elseif(substr($n, "-1", "1") == "<") {
					$where_string .= "<";
				}
				elseif(substr($n, "-2", "2") == "<=") {
					$where_string .= "<=";
				}
				elseif(substr($n, "-2", "2") == ">=") {
					$where_string .= ">=";
				}
				elseif(substr($n, "-2", "2") == "!=") {
					$where_string .= "!=";
				}
				else {
					$where_string .= "=";
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