<?php

class Storage {
  protected $_timeout = 3600;

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
}

?>