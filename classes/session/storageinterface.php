<?php

interface Session_StorageInterface {
  public function save($name, $id, $data, $timeout);
  public function load($name, $id);
  public function destroy($name, $id);
}

?>