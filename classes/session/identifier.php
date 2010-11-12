<?php

class Session_Identifier {
  protected $_name;
  protected $_timeout;
  protected $_id;

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

  public function has_id() {
    if (is_string($this->_id) && strlen($this->_id)) {
      return true;
    } else {
      return false;
    }
  }

  public function get_id() {
    return $this->_id;
  }

  public function set_id($id) {
    if (is_string($id)) {
      $this->_id = $id;
      return true;
    } else {
      return false;
    }
  }

  public function get_timeout() {
    return $this->_timeout;
  }

  public function set_timeout($timeout) {
    if (is_integer($timeout)) {
      $this->_timeout = $timeout;
      return true;
    } else {
      return false;
    }
  }
  
  public function destroy() {
    $this->_id = null;
    return true;
  }
  
  public function save() {}
}

?>