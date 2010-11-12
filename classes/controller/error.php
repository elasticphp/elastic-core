<?php

class Controller_Error extends Controller {
  public function action_404() {
    $this->status = Response::STATUS_NOT_FOUND;
    $this->content = 'Could not find what you were looking for!';
  }

  public function action_500() {
    $this->status = Response::STATUS_SERVER_ERROR;
    $this->content = 'The server has encountered a problem.';
  }
}

?>