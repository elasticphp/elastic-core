<?php

class Request {
  protected static $_current;
  protected $_parent;

  protected $_uri;
  protected $_protocol = null;
  protected $_route = array('controller' => null, 'action' => null, 'arguments' => array());
  protected $_request_arguments = array();
  protected $_request_cookies = array();
  protected $_desired_types = array();
  protected $_desired_languages = array();
  protected $_response;

  /**
   * Create a new Request_* object.
   *
   * @param string URI
   * @param string protocol
   * @param array  arguments
   * @param array  cookies
   * @param array  desired content type(s)
   * @param array  desired language(s)
   *
   * @return mixed  a Request_$protocol object or false on failure
   */
  public static function factory($protocol='http', $uri=null, Array $arguments=null, Array $cookies=null, Array $desired_types=null, Array $desired_languages=null) {
    if ($request = Container::factory("Request_$protocol", null, array('uri' => $uri, 'arguments' => $arguments, 'cookies' => $cookies, 'desired_types' => $desired_types, 'desired_languages' => $desired_languages))) {
      return $request;
    } else {
      return false;
    }
  }

  /**
   * Return the currently executing request.
   *
   * @return Request the currently executing request
   */
  public static function current() {
    return Request::$_current;
  }

  public function get_protocol() {
    return $this->_protocol;
  }

  public function get_uri() {
    return $this->_uri;
  }

  public function set_uri($uri) {
    if (is_string($uri)) {
      $this->_uri = $uri;
      return true;
    } else {
      return false;
    }
  }

  /**
   * Return the response object associated with this request.
   *
   * @return Response the response object associated with this request
   */
  public function get_response() {
    return $this->_response;
  }

  /**
   * Create a new Response_* object and execute this request.
   *
   * @param  string protocol
   * @return mixed  a Response_$protocol object or false on failure
   */
  public function execute($protocol='http') {
    if ($cref = new ReflectionClass("Response_$protocol")) {
      $this->_response = $cref->newInstanceArgs(array('request' => $this));
    } else {
      return false;
    }

    $this->_parent = Request::$_current;
    Request::$_current = $this;

    if (!$route = Route::match($this->_uri)) {
      return false;
    }

    $this->_route = $route;

    if (class_exists($route['class'])) {
      $controller = new $route['class'];

      if (method_exists($controller, 'before')) {
        $controller->before();
      }

      if (method_exists($controller, $route['method'])) {
        $controller->{$route['method']}($route['arguments']);
      } else {
        trigger_error("Action '{$route['method']}' doesn't exist", E_USER_WARNING);
      }

      if (method_exists($controller, 'after')) {
        $controller->after();
      }
    } else {
      $controller = new Controller_Error;
      $controller->before();
      $controller->action_404();
      $controller->after();
    }

    return $this->_response;
  }

  /**
   * Return the name of the requested resource.
   * This should be a full file path.
   *
   * @return string full path of the requested resource
   */
  public function get_resource_name() {
    return $this->_name;
  }

  /**
   * Return the route parameters for this request.
   *
   * @return array the route parameters for this request
   */
  public function get_route() {
    return $this->_route;
  }

  /**
   * Return the arguments supplied with the request.
   * - For HTTP, this would be $_GET
   * - For a console app, this would be $ARGV
   *
   * @return array arguments supplied with the request
   */
  public function get_request_arguments() {
    return $this->_arguments;
  }

  /**
   * Check if a specified argument was sent with the request.
   *
   * @return boolean
   */
  public function has_argument($name) {
    if (isset($this->_request_arguments[$name])) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Get a specific named argument if it was sent with the request.
   *
   * @return array|boolean
   */
  public function get_argument($name) {
    if (isset($this->_request_arguments[$name])) {
      return $this->_request_arguments[$name];
    } else {
      return false;
    }
  }

  /**
   * Get the cookies that the client sent with the request.
   *
   * @return array cookies
   */
  public function get_request_cookies() {
    return $this->_request_cookies;
  }

  /**
   * Check if a specified cookie was sent with the request.
   *
   * @return boolean
   */
  public function has_cookie($name) {
    if (isset($this->_request_cookies[$name])) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Get a specific named cookie if it was sent with the request.
   *
   * @return array|boolean
   */
  public function get_cookie($name) {
    if (isset($this->_request_cookies[$name])) {
      return $this->_request_cookies[$name];
    } else {
      return false;
    }
  }

  /**
   * Get the type of content that the client desires.
   * This is a MIME type.
   *
   * @return array collection of MIME types as strings
   */
  public function get_desired_types() {
    return $this->_desired_types;
  }

  /**
   * Get the languages that the client desires, in order of highest to lowest
   * preference.
   *
   * @return array collection of ISO 639-1 language codes as strings
   */
  public function get_desired_languages() {
    return $this->_desired_languages;
  }

  public function finish() {
    Request::$_current = $this->_parent;
  }
}

?>
