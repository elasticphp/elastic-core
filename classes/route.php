<?php

class Route {
  protected static $_routes = array();
  protected static $_base_path;

  protected $_storage;
  protected $_name;
  protected $_uri;
  protected $_segments = array();
  protected $_options = array();
  protected $_defaults = array();
  protected $_internal = null;
  protected $_regex = null;

  const REGEX_SEGMENT_NAME = '<([a-zA-Z0-9_]++)>';
  const REGEX_SEGMENT_CHAR = '[^/.,;?\n]+';
  const REGEX_ESCAPE_CHAR = '[\.\+\*\?\[\^\]\$\{\}\=\!\|]';

  public static function set($name) {
    if (!isset(Route::$_routes[$name])) { Route::$_routes[$name] = Container::factory('Route', null, array('name' => $name)); }
    return Route::$_routes[$name];
  }

  public static function get($name) {
    if (isset(Route::$_routes[$name])) {
      return Route::$_routes[$name];
    } else {
      return false;
    }
  }

  public static function all() {
    return Route::$_routes;
  }

  public static function del($name) {
    if (isset(Route::$_routes[$name])) {
      unset(Route::$_routes[$name]);
      return true;
    } else {
      return false;
    }
  }

  public static function match($uri) {
    foreach (array_reverse(Route::$_routes) as $route) {
      if (($result = $route->matches($uri)) && ($result !== false)) {
        return $result;
      }
    }

    return false;
  }

  public static function base_path($file) {
    Route::$_base_path = $file;
  }

  public function __construct($name, StorageInterface $storage) {
    $this->_name = $name;
    $this->_storage = $storage;

    return $this;
  }

  public function uri($uri=null) {
    if (is_string($uri)) {
      $this->_uri = $uri;

      return $this;
    }

    return $this->_uri;
  }

  public function segments(Array $segments=null) {
    if (is_array($segments)) {
      $this->_segments = $segments;

      return $this;
    }

    return $this->_segments;
  }

  public function options(Array $options=null) {
    if (is_array($options)) {
      $this->_options = array_merge(array('directory' => ''), $options);

      return $this;
    }

    return $this->_options;
  }

  public function defaults(Array $defaults=null) {
    if (is_array($defaults)) {
      $this->_defaults = $defaults;

      return $this;
    }

    return $this->_defaults;
  }

  public function internal($internal=null) {
    if (is_bool($internal)) {
      $this->_internal = $internal;

      return $this;
    }

    return $this->_internal;
  }

  public function compile() {
    $key = 'route:regex:'.$this->_name;
    if ($this->_storage->has($key)) {
      return $this->_storage->get($key);
    }

    $regex = preg_replace('#'.Route::REGEX_ESCAPE_CHAR.'#', '\\\\$0', $this->_uri);

    if (strpos($regex, '(') !== false) { $regex = str_replace(array('(', ')'), array('(?:', ')?'), $regex); }

    $regex = str_replace(array('<', '>'), array('(?P<', '>'.Route::REGEX_SEGMENT_CHAR.')'), $regex);

    if (!empty($this->_segments)) {
      $search = array();
      $replace = array();
      foreach ($this->_segments as $k => $v) {
        if (strpos($v, '(') !== false) { $v = str_replace(array('(', ')'), array('(?:', ')?'), $v); }
        $search[]  = '<'.$k.'>'.Route::REGEX_SEGMENT_CHAR;
        $replace[] = '<'.$k.'>'.$v;
      }
      $regex = str_replace($search, $replace, $regex);
    }

    $regex = '#^'.$regex.'$#';
    
    $this->_storage->set($key, $regex);

    return $regex;
  }

  public function matches($uri) {
    ($this->_regex !== null) || $this->_regex = $this->compile();
    if (!is_string($this->_regex)) { return false; }

    if (preg_match($this->_regex, $uri, $matches)) {
      $matches = array_merge($this->_defaults, $matches);

      foreach (array_keys($matches) as $key) {
        if (is_integer($key)) { unset($matches[$key]); }
      }

      $return = $this->_options;
      foreach ($return as $rk => &$rv) {
        foreach ($matches as $mk => $mv) {
          if (is_integer($mk)) { continue; }
          $rv = str_replace('<'.$mk.'>', $mv, $rv);
        }
      }

      $return['arguments'] = $matches;

      return $return;
    }

    return false;
  }
}

?>
