<?php

interface Session_IdentifierInterface {
  public function get_name();
  public function set_name($name);
  public function has_id();
  public function get_id();
  public function set_id($id);
  public function destroy();
}

?>