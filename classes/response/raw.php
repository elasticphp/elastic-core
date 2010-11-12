<?php

class Response_Raw extends Response implements ResponseInterface {
  public function send_content() {
    Event::factory('response.content_send_pre')->notify_all();
    echo $this->get_content();
    Event::factory('response.content_send_post')->notify_all();

    return $this;
  }
}

?>
