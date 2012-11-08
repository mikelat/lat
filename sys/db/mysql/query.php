<?php
class DB extends Driver {

	// Array for query builder
	private $sql = array();

	/**
	 * Query builder table selector, additionally returns object
	 */
    public static function table($table)
    {
		$obj = new self();
		$obj->sql['table'] = $table;
        return $obj;
    }

	/**
	 * Stores query data for "limit"
	 */
	public function limit($number1, $number2=null) {
		$this->sql['limit'] = array($number1, $number2);

		return $this;
	}

	/**
	 * Stores query data for "order by"
	 */
	public function order() {
		$this->order = func_get_args();
		return $this;
	}

	/**
	 * Stores query data for "group by"
	 */
	public function group() {
		$this->group = func_get_args();
		return $this;
	}

	/**
	 * Stores query data for "where"
	 */
	public function where() {
		$args = func_get_args();

		// make double argument (non array) into an array
		if(count($args) == 2 && !is_array($args[0]) && !is_array($args[1])) {
			$args = array($args[0] => $args[1]);
		}

		if(!isset($this->sql['where'])) {
			$this->sql['where'] = $args;
		}
		else {
			$this->sql['where'] = array_merge($this->sql['where'], $args);
		}

		return $this;
	}

	/**
	 * Stores query data for "set"
	 */
	public function set() {
		$args = func_get_args();

		// make data into an array if its not passed as one
		if(count($args) == 2 && !is_array($args[0]) && !is_array($args[1])) {
			$args = array($args[0] => $args[1]);
		}

		$this->sql['set'] = $args;

		return $this;
	}

	/**
	 * Executes built database query and updates
	 */
	public function update() {
		// send arguments to where clause
		if(func_num_args() > 0) {
			call_user_func_array(array($this, "where"), func_get_args());
		}

		// build and run query
		$this->sql['type'] = "update";
		$raw_query = $this->build($this->sql);
		$query = $this->query($raw_query[0], $raw_query[1]);
	}

	/**
	 * Executes built database query and returns array of results
	 */
	public function get() {
		$query = call_user_func_array(array($this, "obj"), func_get_args());
		return $query->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * Executes built database query and returns a single row
	 */
	public function num() {
		if(func_num_args() > 0) {
			call_user_func_array(array($this, "where"), func_get_args());
		}

		$query = self::obj();
		return $query->rowCount();
	}

	/**
	 * Executes built database query and returns a single row
	 */
	public function row() {
		$query = call_user_func_array(array($this, "obj"), func_get_args());
		return $query->fetch(\PDO::FETCH_ASSOC);
	}

	/**
	 * Executes built database query and returns an object
	 */
	public function obj() {
		$this->sql['select'] = "*";

		if(func_num_args() > 0) {
			$this->sql['select'] = func_get_args();
		}

		$this->sql['type'] = "select";

		$raw_query = $this->build($this->sql);
		$query = $this->query($raw_query[0], $raw_query[1]);

		return $query;
	}
}