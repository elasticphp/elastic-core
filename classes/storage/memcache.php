<?php

class Storage_Memcache extends Storage implements StorageInterface {
  protected $_memcache;

  public function __construct(Array $servers) {
    $this->_memcache = new Memcache;
    foreach ($servers as $server) {
      $this->_memcache->addServer($server['host'], $server['port']);
    }
  }

  public function set($key, $data, $timeout=null) {
    if ($timeout === null) { $timeout = $this->_timeout; }
    return $this->_memcache->set($key, $data, 0, $timeout);
  }

  public function get($key) {
    return $this->_memcache->get($key);
  }

  public function has($key) {
    return ($this->_memcache->get($key) !== false) ? true : false;
  }

  public function delete($key) {
    return $this->_memcache->delete($key);
  }
}

?>