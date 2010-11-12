<?php

class Session extends MultiArrayObject {
  protected $_storage;
  protected $_identifier;
  protected $_name;
  protected $_id;
  protected $_timeout;

  public function __construct(Session_StorageInterface $storage, Session_IdentifierInterface $identifier, $name='default', $timeout=3600) {
    $this->set_storage($storage);
    $this->set_identifier($identifier);
    $this->set_name($name);
    $this->set_timeout($timeout);

    $this->_id = $this->_identifier->get_id();
    $this->load();

    Dispatcher::listen('response.pre_send_headers', array($this, 'save'));

    return $this;
  }

  public function get_name() {
    return $this->_name;
  }

  public function set_name($name) {
    if (is_string($name)) {
      $this->_name = $name;
      $this->_identifier->set_name($name);
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
      $this->_identifier->set_timeout($timeout);
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
      $this->_identifier->set_id($id);
      return true;
    } else {
      return false;
    }
  }

  public function get_storage() {
    return $this->_storage;
  }

  public function set_storage(Session_StorageInterface $storage) {
    $this->_storage = $storage;
    return true;
  }

  public function get_identifier() {
    return $this->_identifier;
  }

  public function set_identifier(Session_IdentifierInterface $identifier) {
    $this->_identifier = $identifier;
    return true;
  }

  public function load() {
    if ($data = $this->_storage->load($this->_name, $this->_id)) {
      return $this->exchangeArray($data);
    } else {
      return false;
    }
  }

  public function save() {
    $this->_identifier->save();
    return $this->_storage->save($this->_name, $this->_id, $this->array_copy(), $this->_timeout);
  }

  public function destroy() {
    $this->_storage->delete($this->_name, $this->_id);
    $this->exchangeArray(array());
    $this->_identifier->destroy();
  }
}

?>