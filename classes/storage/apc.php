<?php

class Storage_APC extends Storage implements StorageInterface {
  public function set($key, $data, $timeout=null) {
    if ($timeout === null) { $timeout = $this->_timeout; }
    return apc_store($key, $data, $timeout);
  }

  public function get($key) {
    return apc_fetch($key);
  }
  
  public function has($key) {
    return apc_exists($key);
  }
  
  public function delete($key) {
    return apc_delete($key);
  }
}

?>