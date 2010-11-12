<?php

class Controller {
  public $status = null;
  public $type = null;
  public $language = null;
  public $content = null;

  public function before() {}

  public function after() {
    if ($this->status === null) { $this->status = Response::STATUS_OK; }
    if ($this->content === null) { $this->content = ''; }

    Request::current()->get_response()->set_type($this->type);
    Request::current()->get_response()->set_language($this->language);
    Request::current()->get_response()->set_status($this->status);
    Request::current()->get_response()->set_content($this->content);
  }
}

?>
