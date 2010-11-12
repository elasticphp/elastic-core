<?php

class Request_Raw extends Request implements RequestInterface {
  protected $_protocol = 'raw';

  public function __construct($uri=null, Array $arguments=null, Array $cookies=null, Array $desired_types=null, Array $desired_languages=null) {
    $this->_uri = $uri;
    $this->_request_arguments = $arguments;
    $this->_request_cookies = $cookies;
    $this->_desired_types = $desired_types;
    $this->_desired_languages = $desired_languages;
  }

  public function get_request_content() {
    return null;
  }
}

?>
