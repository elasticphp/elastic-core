<?php

class Response {
  const STATUS_OK           = 1;
  const STATUS_NOT_FOUND    = 2;
  const STATUS_UNAUTHORISED = 3;
  const STATUS_SERVER_ERROR = 4;

  protected $_request;
  protected $_status;
  protected $_name;
  protected $_type;
  protected $_time;
  protected $_language;
  protected $_cookies = array();
  protected $_content;

  public static function current() {
    return Request::current()->get_response();
  }

  public function __construct(RequestInterface $request) {
    $this->_request = $request;
  }

  /**
   * Return the status of the response.
   *
   * @return integer
   */
  public function get_status() {
    return $this->_status;
  }

  /**
   * Set the status of the response.
   *
   * @param integer
   *
   * @return boolean
   */
  public function set_status($status) {
    if (!is_integer($status)) { return false; }
    $this->_status = $status;
    return true;
  }

  /**
   * Return the name of the resource.
   *
   * @return string|null
   */
  public function get_name() {
    return $this->_name;
  }

  /**
   * Set the name of the resource.
   *
   * @param string
   *
   * @return boolean
   */
  public function set_name($name) {
    if (!is_string($name)) { return false; }
    $this->_name = $name;
    return true;
  }

  /**
   * Get the mime-type of the resource.
   *
   * @return string|null
   */
  public function get_type() {
    return $this->_type;
  }

  /**
   * Set the mime-type of the resource.
   *
   * @param string
   *
   * @return boolean
   */
  public function set_type($type) {
    if (!is_string($type)) { return false; }
    $this->_type = $type;
    return true;
  }

  /**
   * Get the last modified time of the resource.
   *
   * @return integer|null
   */
  public function get_time() {
    return $this->_time;
  }

  /**
   * Set the last modified time of the resource.
   *
   * @param integer
   *
   * @return boolean
   */
  public function set_time($time) {
    if (!is_integer($time)) { return false; }
    $this->_time = $time;
    return true;
  }

  /**
   * Get the language of the resource.
   *
   * @return string|null
   */
  public function get_language() {
    return $this->_language;
  }

  /**
   * Set the language of the resource.
   *
   * @param string
   *
   * @return boolean
   */
  public function set_language($language) {
    if (!is_string($language)) { return false; }
    $this->_language = $language;
    return true;
  }

  /**
   * Get the cookies to be sent with the resource.
   *
   * @return array
   */
  public function get_cookies() {
    return $this->_cookies;
  }

  /**
   * Check to see if a cookie with the specified name is set to be sent with
   * the resource.
   *
   * @param string Name of the cookie
   *
   * @return array
   */
  public function has_cookie($name) {
    if (isset($this->_cookies[$name])) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Get a cookie that is to be sent with the resource if it exists.
   *
   * @param string Name of the cookie
   *
   * @return array
   */
  public function get_cookie($name) {
    if (isset($this->_cookies[$name])) {
      return $this->_cookies[$name];
    } else {
      return false;
    }
  }

  /**
   * Add a cookie to those to be sent with the resource.
   *
   * @param string  Name for the cookie
   * @param string  Data for the cookie
   * @param integer Time that the cookie expires in unix timestamp format (Defaults to "never" effectively)
   * @param string  Path where the cookie is valid (Defaults to the directory of the request URI)
   * @param string  Domain where the cookie is valid (Defaults to the domain of the request URI)
   *
   * @return boolean
   */
  public function set_cookie($name, $data, $expires=null, $path=null, $domain=null) {
    if (!is_string($name))     { return false; }
    if (!is_string($data))     { return false; }
    if (!is_integer($expires)) { return false; }
    if (!is_string($path))     { return false; }
    if (!is_string($domain))   { return false; }

    $this->_cookies[$name] = array(
      'name' => $name,
      'data' => $data,
      'expires' => $expires,
      'path' => $path,
      'domain' => $domain
    );

    return true;
  }

  public function send_headers() {
    Event::factory('response.pre_send_headers')->notify_all();
    Event::factory('response.post_send_headers')->notify_all();
    return $this;
  }

  /**
   * Get the content of the resource.
   *
   * @return string
   */
  public function get_content() {
    $event = Event::factory('response.get_content', array('content' => $this->_content))->notify_all();
    $data = $event->get_data();
    return $data['content'];
  }

  /**
   * Set the content of the resource.
   *
   * @param string
   *
   * @return boolean
   */
  public function set_content($content) {
    if (!is_string($content)) { return false; }
    $this->_content = $content;
    return true;
  }

  /**
   * Append to the content of the resource.
   *
   * @param string
   *
   * @return boolean
   */
  public function add_content($content) {
    if (!is_string($content)) { return false; }
    $this->_content .= $content;
    return true;
  }

  /**
   * Send the content to the client.
   * Raises the events "response.content_send_pre" and
   * "response.content_send_post" in predictable places.
   *
   * @return void
   */
  public function send_content() {
    Event::factory('response.pre_send_content')->notify_all();
    Event::factory('response.post_send_content')->notify_all();
    return $this;
  }

  public function finish() {
    $this->_request->finish();
  }
}

?>
