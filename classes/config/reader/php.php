<?php

class Config_Reader_PHP extends Config_Reader implements Config_ReaderInterface {
  public function load($name='default') {
    $config = array();

    foreach (Elastic::find_file('config/'.$name.PHP_EXT, false, false, true) as $file) {
      $arr = include $file;

      if (is_array($arr)) {
        $config = array_merge_recursive($config, $arr);
      }
    }

    return $config;
  }
}

?>
