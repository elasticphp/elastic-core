<?php

class Elastic_String {
  const CHARS_ALPHA = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

  public static function rand_str($length=8, $characters=self::CHARS_ALPHA) {
    $string = '';
    for ($i=0;$i<$length;$i++) {
      $string .= substr($characters, rand(0, strlen($characters)-1), 1);
    }
    return $string;
  }
}

?>