<?php namespace Lat;

// Load configuration libary and import what we have
require $cfg['path']['library'] . 'config' . EXT;
Config::import($cfg);
unset($cfg);

// Load the loader (lol)
require Config::get('path', 'library') . 'load' . EXT;

// Logging Library
Load::library('Log');

// Start buffering output
ob_start(array('Lat\\', '_buffer'));

// Load database
require Config::get('path', 'db') . Config::get('sql', 'driver') . '/driver' . EXT;
require Config::get('path', 'db') . Config::get('sql', 'driver') . '/query' . EXT;
use Lat\Database\Query as DB;

// Default Libraries
Load::library('Url');
Load::library('Cookie');
Load::library('Cache');
Load::library('Session');
Load::library('Input');

// Set defaults for libraries
Url::set($_SERVER['REQUEST_URI']);
Session::load();
Cache::load();

Cache::reload();

//DB::table('configuration')->set('option_value', 1)->update('option_name', 'bots_enabled', 'faf');
//$var = DB::table('session')->limit(1)->row('user_id', 'session_id');
//echo serialize(array('sql' => array( 'table' => 'configuration', 'type' => 'select', 'select' => 'config_name, config_value', )));

require SYS . 'controller' . EXT;
Controller\Controller::_load();
Controller\Controller::_render();
