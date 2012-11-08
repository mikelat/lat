<?php
// Load configuration libary and import what we have
require $cfg['path_library'] . 'config' . EXT;
Config::import($cfg);
unset($cfg);

// Load the loader (lol)
require Config::get('path_library') . 'load' . EXT;

// Logging Library
Load::library('log');

// Global Language
Load::language('_global');

// Load default controller and buffer output
require SYS . 'controller' . EXT;
require SYS . 'model' . EXT;
Controller\Controller::_init();

// Load database
require Config::get('path_db') . $sql_cfg['driver'] . '/driver' . EXT;
require Config::get('path_db') . $sql_cfg['driver'] . '/query' . EXT;
DB::load($sql_cfg);
unset($sql_cfg);

// Default Libraries
Load::library('session');
Load::library('parse');
Load::library('cache');
Load::library('url');

// Set defaults for libraries
Url::set($_SERVER['REQUEST_URI']);
Session::load();
Cache::load();

// Guess the base url
if(Config::get('url') == "") {
	Config::import('url', (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://'. $_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']));
}

// Add default JS files
Load::javascript_var('url', Config::get('url'));

//DB::table('configuration')->set('option_value', 1)->update('option_name', 'bots_enabled', 'faf');
//$var = DB::table('session')->limit(1)->row('user_id', 'session_id');
//echo serialize(array('sql' => array( 'table' => 'configuration', 'type' => 'select', 'select' => 'config_name, config_value', )));

// Figure out what page we need to load
$class = strtolower(Url::get(1));
$func = Url::get(2);
$args = array_slice(Url::get(), 2);

$page_found = false;
if($class == null) {
	$class = 'forum'; // TODO: replace this later with actual configuration option
}

if($func == null || !preg_match("/^[a-z][a-z_]*/", $func)) {
	$func = 'index';
}
else {
	$args = array_slice($args, 1);
}

$controller = Load::controller($class);

if($controller === false || !is_callable(array($controller, $func))) {
	Log::error("Page not found", 404);
}

$controller->_class('pg-' . strtolower($class));
$controller->_class('fn-' . $func);
call_user_func_array(array($controller, $func), $args);
call_user_func(array($controller, '_buffer'));
call_user_func(array($controller, '_render'));
