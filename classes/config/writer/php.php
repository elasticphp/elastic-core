<?php

class Config_Writer_PHP extends Config_Writer implements Config_WriterInterface {
  public function save(Array $config, $name='default') {
    $file = tempnam();
    if (!$fh = fopen($file, 'w')) { return false; }
    if (!fwrite($fh, var_export($config, true))) { return false; }
    fclose($fh);
    return $file;
  }
}

?>
