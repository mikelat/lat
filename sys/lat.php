<?php
// Show errors if we're development
if(ENVIRONMENT == "development") {
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	ini_set('display_errors', 'On');
}

// Grab our root configuration file
require(ROOT . 'cfg' . EXT);

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

// Load up session
User::load_session();

// Load the requested page
Url::load($_SERVER['REQUEST_URI']);