<?php

class Event {
  protected $_name = null;
  protected $_data = null;
  protected $_processed = false;
  protected $_return = null;

  public static function factory($name, Array $data=array()) {
    return new self($name, $data);
  }

  public function __construct($name, Array $data=array()) {
    return ($this->set_name($name) && $this->set_data($data)) ? $this : false;
  }

  public function get_name() {
    return $this->_name;
  }

  public function set_name($name) {
    if (is_string($name)) {
      $this->_name = $name;
      return true;
    } else {
      return false;
    }
  }

  public function get_data() {
    return $this->_data;
  }

  public function set_data(Array $data=array()) {
    foreach ($data as $k => &$v) {
      if ($v === '{{event}}') {
        $v = $this;
      }
    }

    $this->_data = $data;
  }

  public function is_processed() {
    return $this->_processed;
  }

  public function set_processed($processed) {
    if (($processed === true) || ($processed === false)) {
      $this->_processed = $processed;
      return true;
    } else {
      return false;
    }
  }

  public function get_return() {
    return $this->_return;
  }

  public function set_return($return) {
    $this->_return = $return;
  }

  public function notify_one() {
    Dispatcher::notify_one($this);
    return $this;
  }

  public function notify_all() {
    Dispatcher::notify_all($this);
    return $this;
  }
}

?>