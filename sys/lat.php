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
Controller\Controller::_clear();

// Load database
require Config::get('path_database') . $sql_cfg['driver'] . '/driver' . EXT;
require Config::get('path_database') . $sql_cfg['driver'] . '/query' . EXT;
DB::load($sql_cfg);
unset($sql_cfg);

// Default Libraries
Load::library('string');
Load::library('cache');
Load::library('user');
Load::library('url');

// Set defaults for libraries
User::load_session();
Cache::load();

// Guess the base url
if(Config::get('url') == "") {
	Config::import('url', (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://'. $_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']));
}

//DB::table('configuration')->set('option_value', 1)->update('option_name', 'bots_enabled', 'faf');
//$var = DB::table('session')->limit(1)->row('user_id', 'session_id');
//echo serialize(array('sql' => array( 'table' => 'configuration', 'type' => 'select', 'select' => 'config_name, config_value', )));

Url::load($_SERVER['REQUEST_URI']);