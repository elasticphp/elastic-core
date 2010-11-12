<?php

class Session_Identifier_Cookie extends Session_Identifier implements Session_IdentifierInterface {
  public function get_id() {
    if (!$this->has_id()) {
      if (Request::current()->has_cookie('session:'.$this->_name.':id')) {
        $this->_id = Request::current()->get_cookie('session:'.$this->_name.':id');
      } elseif (Request::current()->has_argument('session:'.$this->_name.':id')) {
        $this->_id = Request::current()->get_argument('session:'.$this->_name.':id');
      } else {
        $this->_id = Elastic_String::rand_str(32);
      }
    }

    return parent::get_id();
  }

  public function save() {
    Response::current()->set_cookie(
      'session:'.$this->_name.':id',
      $this->_id,
      time()+$this->_timeout,
      Elastic::get_option('base_path'),
      Elastic::get_option('base_domain')
    );

    return parent::save();
  }

  public function destroy() {
    Response::current()->set_cookie(
      'session:'.$this->_name.':id',
      '',
      0,
      Elastic::get_option('base_path'),
      Elastic::get_option('base_domain')
    );

    return parent::destroy();
  }
}

?>