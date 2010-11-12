<?php

class Response_HTTP extends Response implements ResponseInterface {
  protected static $_sent_headers = false;

  /**
   * Sends the response headers to the client if there have not been any
   * headers sent yet.
   *
   * @return object $this
   */
  public function send_headers() {
    Event::factory('response.pre_send_headers')->notify_all();
    if (Response_HTTP::$_sent_headers === false) {
      $headers = array();

      switch ($this->_status) {
        case Response::STATUS_OK:
          $headers[] = 'HTTP/1.1 200 OK';
          break;
        case Response::STATUS_NOT_FOUND:
          $headers[] = 'HTTP/1.1 404 Not Found';
          break;
        case Response::STATUS_UNAUTHORISED:
          $headers[] = 'HTTP/1.1 401 Unauthorized';
          break;
        case Response::STATUS_SERVER_ERROR:
          $headers[] = 'HTTP/1.1 500 Error';
          break;
        default:
          $headers[] = 'HTTP/1.1 500 Elastic Confused :(';
          break;
      }

      if ($this->_type !== null) {
        $headers[] = 'Content-Type: '.$this->_type;
      }

      if ($this->_time !== null) {
        $headers[] = 'Last-Modified: '.gmdate('D, d M Y H:i:s', $this->_time).' GMT';
      }

      if ($this->_language !== null) {
        $headers[] = 'Content-Language: '.$this->_language;
      }

      foreach ($this->_cookies as $cookie) {
        $headers[] = sprintf(
          'Set-Cookie: %s=%s; expires=%s; domain=%s; path=%s;',
          $cookie['name'], $cookie['data'], gmdate('D, d M Y H:i:s', $cookie['expires']), $cookie['domain'], $cookie['path']
        );
      }

      foreach ($headers as $header) {
        header($header, false);
      }

      Response_HTTP::$_sent_headers = true;
    }
    Event::factory('response.post_send_headers')->notify_all();

    return $this;
  }

  /**
   * Sends the response content to the client.
   *
   * @return object $this
   */
  public function send_content() {
    Event::factory('response.content_send_pre')->notify_all();
    echo $this->get_content();
    Event::factory('response.content_send_post')->notify_all();

    return $this;
  }
}

?>
