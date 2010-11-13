<?php

// Set your timezone
date_default_timezone_set('UTC');
// Set your locale
setlocale(LC_ALL, 'en_US.utf-8');
// This just needs to be here in case of stupid PHP configs
ini_set('unserialize_callback_func', 'spl_autoload_call');

// By default, the base domain is set to whatever the client sent in the host
// header. You may want to set this explicitly. It's used in the reverse routing
// and cookie systems.
Elastic::set_option('base_domain', $_SERVER['HTTP_HOST']);
// Same here, used for generating URLs and cookie headers.
Elastic::set_option('base_path', '/');

// Make it so session data is stored entirely in cookies. You may want to change
// this, but maybe not. Who knows!
Container::instance()->set_implementation('Session_StorageInterface', 'Session_Storage_Cookie');
Container::instance()->set_implementation('Session_IdentifierInterface', 'Session_Identifier_Cookie');

// Set the reader/writer for the Config class to the PHP file variant.
Container::instance()->set_implementation('Config_ReaderInterface', 'Config_Reader_PHP');
Container::instance()->set_implementation('Config_WriterInterface', 'Config_Writer_PHP');

// Include modules here if necessary.
// Elastic::module('module-name', MOD_ROOT.'/module-location');

// Set some routes up!
// Note that routes are parsed in reverse order of declaration. This means that
// routes declared later in other files are parsed before these ones.
// These are just examples, you can get really tricky here if you want.
Route::set('front/page')
  ->uri('(<controller>(/<action>(/<id>)))')
  ->options(array('class' => 'Controller_Front_Page_<controller>', 'method' => 'action_<action>', 'directory' => 'front/page'))
  ->segments(array('id' => '\d+'))
  ->defaults(array('controller' => 'home', 'action' => 'index'));

Route::set('front/pagelet')
  ->uri('pagelet(/<controller>(/<action>(/<id>)))')
  ->options(array('class' => 'Controller_Front_Pagelet_<controller>', 'method' => 'action_<action>', 'directory' => 'front/pagelet'))
  ->segments(array('id' => '\d+'))
  ->defaults(array('controller' => 'home', 'action' => 'index'));

// Start up a request object, execute it, then output everything. By default
// this is HTTP, but other drivers are available for the Request/Response
// objects. Please see the manual (when it exists) for more information.
Request::factory('http')->execute('http')->send_headers()->send_content()->finish();

?>
