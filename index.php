<?php
// For script execution time
define('TIMER', microtime(true));

// Software version
define("VERSION", "0.0.1 dev");

// File extention
define('EXT', '.php');

// Root path, absolute
define("ROOT", realpath("./") . '/');

// Source folder
define("SYS", ROOT . "sys/");

// Data folder
define("DAT", ROOT . "dat/");

// Language
define("LANGUAGE", "en");

// Define paths
$cfg = array(
		'path_controller' => SYS . 'controller/'
	,	'path_database' => SYS . 'database/'
	,	'path_language' => SYS . 'language/'
	,	'path_library' => SYS . 'library/'
	,	'path_model' => SYS . 'model/'
	,	'path_view' => SYS . 'view/'
);

// Define enviroments
$enviroment = array(
	// Development servers
		"development" => array("localhost", "127.0.0.1", "l.mikelat.com")

	// Testing servers
	,	"testing"     => array()

	// Staging servers
	,	"staging"     => array()
);

// If it doesnt match any of the above, defaults to production
$current_enviroment = 'production';

// Determine current enviroment
foreach($enviroment as $env => $host) {
	if(in_array($_SERVER['SERVER_NAME'], $host)) {
		$current_enviroment = $env;
	}
}

// Set enviroment
define('ENVIRONMENT', $current_enviroment);
unset($enviroment, $current_enviroment); // cleanup enviroment vars

// Show errors if we're development
if(ENVIRONMENT == "development") {
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	ini_set('display_errors', 'On');

	// Grab configruation outside of git :)
	require(ROOT . '../cfg' . EXT);
}
else {
	// Grab our root configuration file
	require(ROOT . 'cfg' . EXT);
}

// Load lat core
require SYS . 'lat' . EXT;