<?php

interface ResponseInterface {
  /**
   * Return the name of the resource.
   *
   * @return string the name of the resource
   */
  public function get_name();

  /**
   * Set the name of the resource
   *
   * @param  string  the name of the resource
   * @return boolean whether the name was set successfully
   */
  public function set_name($name);

  /**
   * Get the mime-type of the resource
   *
   * @return string MIME type
   */
  public function get_type();

  /**
   * Set the mime-type of the resource
   *
   * @param  string  MIME type
   * @return boolean whether the type was set successfully
   */
  public function set_type($type);

  /**
   * Get the last modified time of the resource
   *
   * @return integer unix timestamp
   */
  public function get_time();

  /**
   * Set the last modified time of the resource
   *
   * @param  integer unix timestamp
   * @return boolean whether the time was set successfully
   */
  public function set_time($time);

  /**
   * Get the language of the resource.
   *
   * @return string ISO 639-1 language code
   */
  public function get_language();

  /**
   * Set the language of the resource.
   *
   * @param  string  ISO 639-1 language code
   * @return boolean whether the language was set successfully
   */
  public function set_language($language);

  /**
   * Get the content of the response.
   *
   * @return string the content of the response
   */
  public function get_content();

  /**
   * Set the content of the response.
   *
   * @param  string  the data to set the content to
   * @return boolean whether the content was set successfully
   */
  public function set_content($content);

  /**
   * Append to the content of the response.
   *
   * @param  string  data to append
   * @return boolean whether the data was appended successfully
   */
  public function add_content($content);

  /**
   * Send the response headers to the client.
   *
   * @return boolean status of the operation
   */
  public function send_headers();

  /**
   * Send the content of the response to the client.
   *
   * @return boolean status of the operation
   */
  public function send_content();
}

?>