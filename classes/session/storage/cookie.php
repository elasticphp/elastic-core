<?php

class Session_Storage_Cookie extends Session_Storage implements Session_StorageInterface {
  protected $_secret_key = null;

  public function __construct() {
    $this->set_secret_key(sha1(realpath(__FILE__).filemtime(realpath(__FILE__))));
    return parent::__construct();
  }

  public function set_secret_key($secret_key) {
    if (is_string($secret_key)) {
      $this->_secret_key = $secret_key;
      return true;
    } else {
      return false;
    }
  }

  public function get_secret_key() {
    return $this->_secret_key;
  }

  public function load($name, $id) {
    if (Request::current()->has_cookie('session:'.$name.':data') && Request::current()->has_cookie('session:'.$name.':hash')) {
      $data = Request::current()->get_cookie('session:'.$name.':data');
      $hash = Request::current()->get_cookie('session:'.$name.':hash');
      if ($hash == sha1($data.$this->_secret_key)) {
        $arr = base64_decode($data);
        if (function_exists('gzinflate')) { $arr = gzinflate($arr); }
        $arr = json_decode($arr, true);
        if ($arr['expires'] > time()) {
          $data = $arr['data'];
        } else {
          $data = false;
        }
      } else {
        $data = false;
      }
    } else {
      $data = false;
    }

    return $data;
  }

  public function save($name, $id, $data, $timeout) {
    $data = array('data' => $data, 'expires' => time()+$timeout);
    $data = json_encode($data);
    if (function_exists('gzdeflate')) { $data = gzdeflate($data); }
    $data = base64_encode($data);
    $hash = sha1($data.$this->_secret_key);

    Response::current()->set_cookie(
      'session:'.$name.':data',
      $data,
      time()+$timeout,
      Elastic::get_option('base_path'),
      Elastic::get_option('base_domain')
    );
    Response::current()->set_cookie(
      'session:'.$name.':hash',
      $hash,
      time()+$timeout,
      Elastic::get_option('base_path'),
      Elastic::get_option('base_domain')
    );
  }

  public function destroy($name, $id) {
    Response::current()->set_cookie(
      'session:'.$name.':data',
      '',
      0,
      Elastic::get_option('base_path'),
      Elastic::get_option('base_domain')
    );
    Response::current()->set_cookie(
      'session:'.$name.':hash',
      '',
      0,
      Elastic::get_option('base_path'),
      Elastic::get_option('base_domain')
    );
  }
}

?>