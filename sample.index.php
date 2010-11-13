<?php

error_reporting(E_ALL | E_STRICT);

// Constants
define('DS', DIRECTORY_SEPARATOR);
define('LF', PHP_EOL);
define('PHP_EXT', '.php');

// Set the DOC_ROOT to ./.. (set this explicitly if need be)
define('DOC_ROOT', realpath(dirname(__FILE__).DS.'..'));

// These are defaults
$sys_dir = 'system';
$mod_dir = 'modules';
$app_dir = 'application';
$www_dir = 'www';

// Make sure your $*_dir settings check out
if (is_dir(DOC_ROOT.DS.$sys_dir)) { define('SYS_ROOT', realpath(DOC_ROOT.DS.$sys_dir)); }
if (is_dir(DOC_ROOT.DS.$mod_dir)) { define('MOD_ROOT', realpath(DOC_ROOT.DS.$mod_dir)); }
if (is_dir(DOC_ROOT.DS.$app_dir)) { define('APP_ROOT', realpath(DOC_ROOT.DS.$app_dir)); }
if (is_dir(DOC_ROOT.DS.$www_dir)) { define('WWW_ROOT', realpath(DOC_ROOT.DS.$www_dir)); }

// Don't need them anymore, don't want them polluting the application
unset($sys_dir, $mod_dir, $app_dir, $www_dir);

// If we can't find one of the directories, there's a "problem"
if (!defined('DOC_ROOT') || !defined('SYS_ROOT') || !defined('MOD_ROOT') || !defined('APP_ROOT') || !defined('WWW_ROOT')) {
  die('Error: Please check your $*_dir settings.');
}

// Get the base Elastic class
require SYS_ROOT.DS.'classes'.DS.'elastic'.PHP_EXT;
// Include the Loader class
require SYS_ROOT.DS.'classes'.DS.'loader'.PHP_EXT;
// Include the Container class
require SYS_ROOT.DS.'classes'.DS.'container'.PHP_EXT;
// Create the default loader. Can be accessed later by using Loader::instance('default') again.
Loader::instance('default')->register();
// Include the initialisation file for the core
require SYS_ROOT.DS.'init'.PHP_EXT;

require APP_ROOT.DS.'bootstrap'.PHP_EXT;

?>
