<?php

class Request_HTTP extends Request implements RequestInterface {
  protected $_protocol = 'http';

  /**
   * Parse a URI string like '/path/to/resource?arg1=asdf&arg2=qwer'
   *
   * @param  string  the URI to parse
   * @param  boolean whether or not to decode %xx sequences
   *
   * @return array   the parsed URI data
   */
  public static function parse_uri($uri, $decode=true) {
    if ($decode) {
      $uri = rawurldecode($uri);
    }

    $uri_path = '';
    $uri_args = array();

    if (strpos($uri, '?') !== false) {
      list($uri_path, $temp) = explode('?', $uri, 2);

      foreach (explode('&', $temp) as $arg) {
        $k = null;
        $v = null;
        if (strpos($arg, '=') !== false) {
          list($k, $v) = explode('=', $arg, 2);
        } else {
          $k = $arg;
        }
        $uri_args[$k] = $v;
      }
    } else {
      $uri_path = $uri;
    }

    return array($uri_path, $uri_args);
  }

  /**
   * Parse an HTTP header style list
   * e.g. 'key_a,key_b;param_a=x,key_c;param_a=x;param_b=x'
   *
   * @param  string the list in string form
   *
   * @return array  the parsed list in array form
   */
  public static function parse_list($list) {
    $array = array();

    foreach (explode(',', $list) as $el) {
      $params = explode(';', $el);

      $name = array_shift($params);
      $array[$name] = array();

      foreach ($params as $param) {
        if (strpos($param, '=') !== false) {
          list($key, $val) = explode('=', $param, 2);
        } else {
          $key = $param;
          $val = null;
        }

        $array[$name][$key] = $val;
      }
    }

    return $array;
  }

  /**
   * Parse an HTTP header style weighted list
   * e.g. 'key_a,key_b;q=0.5,key_c;q=0.75'
   *
   * @param string the list in string form
   * @param string the weighting field, defaults to 'q'
   *
   * @return array  the parsed list in array form, sorted by the weight field
   */
  public static function parse_weighted_list($list, $weight_field='q') {
    $array = Request_HTTP::parse_list($list);
    foreach ($array as $key => &$val) {
      if (!isset($val[$weight_field])) { $val[$weight_field] = 1; }
      $val = (float) $val[$weight_field];
    }
    arsort($array);
    return $array;
  }

  /**
   * Creates a new Request_HTTP object from the specified parameters.
   *
   * @param string URI ($_SERVER['REQUEST_URI'] if not specified)
   * @param array  arguments ($_GET if not specified)
   * @param array  cookies ($_COOKIE if not specified)
   * @param array  desired formats in order of most preferred to least preferred (detected from $_SERVER if not specified)
   * @param array  desired languages in order of most preferred to least preferred (detected from $_SERVER if not specified)
   *
   * @return Request_HTTP the request object with all parameters set
   */
  public function __construct($uri=null, Array $arguments=null, Array $cookies=null, Array $desired_types=null, Array $desired_languages=null) {
    if ($uri === null) {
      $uri = $_SERVER['REQUEST_URI'];

      if (strpos($uri, Elastic::get_option('base_path')) === 0) {
        $uri = substr($uri, strlen(Elastic::get_option('base_path')));
      }
    }

    while (substr($uri, -1, 1) === '/') {
      $uri = substr($uri, 0, -1);
    }

    if ($arguments === null) {
      list($uri, $arguments) = Request_HTTP::parse_uri($uri);
    } elseif(($pos = strpos($uri, '?')) !== false) {
      $uri = substr($uri, 0, $pos);
    }

    $this->_uri = $uri;
    $this->_request_arguments = ($arguments !== null) ? $arguments : $_GET;
    $this->_request_cookies = ($cookies !== null) ? $cookies : $_COOKIE;
    $this->_desired_types = ($desired_types !== null) ? $desired_types : Request_HTTP::parse_weighted_list(isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : 'text/html');
    $this->_desired_languages = ($desired_languages !== null) ? $desired_languages : Request_HTTP::parse_weighted_list(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : 'en');

    return $this;
  }

  /**
   * Return the POST parameters of the request.
   *
   * @return mixed post content of the request
   */
  public function get_request_content() {
    return $_POST;
  }
}

?>
