<?php

class Loader {
  protected static $_instances = array();
  protected $_name = null;
  protected $_class_regex = null;
  protected $_directory_on_namespace = true;
  protected $_directory_on_underscore = true;
  protected $_namespace_to_underscore = true;
  protected $_try_original_case = true;
  protected $_try_lower_case = true;
  protected $_try_upper_case = true;
  protected $_try_any_case = false;

  protected function __construct($name) {
    $this->_name = $name;
    return $this;
  }

  public function settings(array $args) {
    foreach ($args as $k => $v) {
      if ($k == 'class_regex') {
        $this->_class_regex = (is_string($v) ? $v : null);
      } elseif ($k == 'directory_on_namespace') {
        $this->_directory_on_namespace = ($v == true);
      } elseif ($k == 'directory_on_underscore') {
        $this->_directory_on_underscore = ($v == true);
      } elseif ($k == 'namespace_to_underscore') {
        $this->_namespace_to_underscore = ($v == true);
      } elseif ($k == 'try_original_case') {
        $this->_try_original_case = ($v == true);
      } elseif ($k == 'try_lower_case') {
        $this->_try_lower_case = ($v == true);
      } elseif ($k == 'try_upper_case') {
        $this->_try_upper_case = ($v == true);
      } else {
        trigger_error('Unexpected configuration option: "'.$k.'"', E_USER_WARNING);
      }
    }
    return $this;
  }

  public static function instance($name) {
    if (!is_string($name) || !strlen($name)) {
      return false;
    }

    if (!isset(Loader::$_instances[$name])) {
      Loader::$_instances[$name] = new Loader($name);
    }

    return Loader::$_instances[$name];
  }

  public function load($class_name) {
    if (is_string($this->_class_regex) && !preg_match('#'.$this->_class_regex.'#', $class_name)) { return false; }
    if ($this->_directory_on_underscore) { $class_name = str_replace('_',  DS, $class_name); }
    if ($this->_directory_on_namespace)  { $class_name = str_replace('\\', DS, $class_name); }

    if (($this->_try_original_case == true) && ($file = Elastic::find_file('classes'.DS.$class_name.PHP_EXT, false))) {
      include $file;
      return true;
    }

    if (($this->_try_lower_case == true) && ($file = Elastic::find_file('classes'.DS.strtolower($class_name).PHP_EXT, false))) {
      include $file;
      return true;
    }

    if (($this->_try_upper_case == true) && ($file = Elastic::find_file('classes'.DS.strtoupper($class_name).PHP_EXT, false))) {
      include $file;
      return true;
    }

    if (($this->_try_any_case == true) && ($file = Elastic::find_file('classes'.DS.$class_name.PHP_EXT, false, false))) {
      include $file;
      return true;
    }

    return false;
  }

  public function register() {
    spl_autoload_register(array($this, 'load'));
  }

  public function unregister() {
    spul_autoload_unregister(array($this, 'load'));
  }
}

?>