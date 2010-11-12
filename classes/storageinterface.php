<?php

interface StorageInterface {
  public function get($key);
  public function set($key, $data, $timeout=null);
  public function has($key);
  public function delete($key);
}

?>