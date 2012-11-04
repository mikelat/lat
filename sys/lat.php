<?php
// Load configuration libary and import what we have
require $cfg['path-library'] . 'config' . EXT;
Config::import($cfg);
unset($cfg);

// Load the loader (lol)
require Config::get('path-library') . 'load' . EXT;

// Logging Library
Load::library('Log');

// Load default controller and buffer output
require SYS . 'controller' . EXT;
Controller::_init();

// Load database
require Config::get('path-db') . $sql_cfg['driver'] . '/driver' . EXT;
require Config::get('path-db') . $sql_cfg['driver'] . '/query' . EXT;
DB::load($sql_cfg);
unset($sql_cfg);

// Default Libraries
Load::library('Cache');
Load::library('Session');
Load::library('Input');
Load::library('Url');

// Set defaults for libraries
Url::set($_SERVER['REQUEST_URI']);
Session::load();
Cache::load();

// Guess the base url

if(Config::get('url') == "") {
	Config::import('url', (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://'. $_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']));
}

//DB::table('configuration')->set('option_value', 1)->update('option_name', 'bots_enabled', 'faf');
//$var = DB::table('session')->limit(1)->row('user_id', 'session_id');
//echo serialize(array('sql' => array( 'table' => 'configuration', 'type' => 'select', 'select' => 'config_name, config_value', )));

// Figure out what page we need to load

$class = strtolower(Url::get(1));
$func = strtolower(Url::get(2));
$page_found = false;
if($class == null) {
	$class = 'forum'; // TODO: replace this later with actual configuration option
}
if($func == null) {
	$func = 'index';
}

if(file_exists(Config::get('path-controller') . $class . EXT)) {
	require Config::get('path-controller') . $class . EXT;
	$class = ucwords($class);

	if(is_callable($class . '::' . $func)) {
		$class::$func();
		$page_found = true;
	}
}

if($page_found === false) {
	Log::error("Page not found", 404);
}

Controller::_render();