<?php

class Config {
  protected $_name;
  protected $_reader;
  protected $_writer;
  protected $_config = null;

  public function __construct($name='default', Config_ReaderInterface $reader, Config_WriterInterface $writer) {
    $this->_name = $name;
    $this->_reader = $reader;
    $this->_writer = $writer;
    return $this;
  }

  public function get_reader() {
    return $this->_reader;
  }

  public function set_reader(Config_ReaderInterface $reader) {
    $this->_reader = $reader;
    return true;
  }

  public function get_writer() {
    return $this->_writer;
  }

  public function set_writer(Config_WriterInterface $writer) {
    $this->_writer = $writer;
    return true;
  }

  public function load() {
    if ($config = $this->_reader->load()) {
      $this->_config = $config;
      return true;
    } else {
      return false;
    }
  }

  public function save() {
    return $this->_writer->save($this->_config);
  }

  public function get($key, $default=null) {
    if ($this->_config === null) { $this->load(); }

    return isset($this->_config[$key]) ? $this->_config[$key] : $default;
  }

  public function set($key, $value) {
    if ($this->_config === null) { $this->load(); }

    $this->_config[$key] = $value;
    return true;
  }
}

?>
