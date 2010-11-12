<?php

class Session_Storage_Elastic implements Session_StorageInterface {
  protected $_storage;

  public function __construct(StorageInterface $storage) {
    $this->_storage = $storage;
  }

  public function load($name, $id) {
    return $this->_storage->get('session:'.$name.':'.$id);
  }

  public function save($name, $id, $data, $timeout) {
    return $this->_storage->set('session:'.$name.':'.$id, $data, $timeout);
  }

  public function destroy($name, $id) {
    return $this->_storage->delete('session:'.$name.':'.$id, $data, $timeout);
  }
}

?>