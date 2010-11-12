<?php

interface RequestInterface {
  /**
   * Return the name of the requested resource.
   * This should be a full file path.
   *
   * @return string full path of the requested resource
   */
  public function get_resource_name();

  /**
   * Return the arguments supplied with the request.
   * - For HTTP, this would be $_GET
   * - For a console app, this would be $ARGV
   *
   * @return array arguments supplied with the request
   */
  public function get_request_arguments();

  /**
   * Read a "chunk" from the body of the request
   * - For HTTP, this would be $_POST
   * - For a console app, this would be stdin
   * This would not be a one-time function. Certain protocols would be
   * expected to keep supplying data as the request went on. An example
   * of this would be user input in a console application.
   *
   * @return mixed any pending request content
   */
  public function get_request_content();

  /**
   * Get the types of content that the client desires.
   * These are MIME types.
   *
   * @return array collection of MIME types as strings.
   */
  public function get_desired_types();

  /**
   * Get the languages that the client desires, in order of highest to lowest
   * preference.
   *
   * @return array collection of ISO 639-1 language codes as strings
   */
  public function get_desired_languages();
}

?>