<?php

class Storage_Array extends Storage implements StorageInterface {
  protected $_data = array();

  public function set($key, $data, $timeout=null) {
    if ($timeout === null) { $timeout = $this->_timeout; }
    if ($this->_data[$key] = array('data' => $data, 'expires' => time()+(int)$timeout)) {
      return true;
    } else {
      return false;
    }
  }

  public function get($key) {
    if (isset($this->_data[$key])) {
      if ($this->_data[$key]['expires'] > time()) {
        return $this->_data[$key]['data'];
      } else {
        unset($this->_data[$key]);
        return false;
      }
    } else {
      return false;
    }
  }

  public function has($key) {
    if (isset($this->_data[$key])) {
      if ($this->_data[$key]['expires'] > time()) {
        return true;
      } else {
        unset($this->_data[$key]);
        return false;
      }
    } else {
      return false;
    }
  }

  public function delete($key) {
    if (isset($this->_data[$key])) {
      unset($this->_data[$key]);
    }

    return true;
  }
}

?>