<?php

class Elastic {
  protected static $_modules = array();
  protected static $_config = array();

  public static function find_file($file, $dir=null, $preserve_case=true, $array=false) {
    if ($array === true) {
      $return = array();
    } else {
      $return = false;
    }

    foreach (array_merge(array(APP_ROOT), array_reverse(array_values(Elastic::$_modules)), array(SYS_ROOT)) as $path) {
      $found = ($preserve_case === false) ? Elastic::file_iexists($path.DS.$file, false, true) : (file_exists($path.DS.$file) ? $path.DS.$file : false);

      if ($found && (($dir === null) || (($dir === true) && is_dir($found)) || (($dir === false) && !is_dir($found)))) {
        $found = realpath($found);

        if ($array === false) {
          $return = $found;
          break;
        } else {
          $return[] = $found;
        }
      }
    }

    //printf("Returning for query %s (%s/%s): %s\n", $file, $dir?'y':'n', $preserve_case?'y':'n', var_export($return, true));

    return $return;
  }

  public static function set_option($key, $value) {
    Elastic::$_config[$key] = $value;
    return true;
  }

  public static function get_option($key) {
    return isset(Elastic::$_config[$key]) ? Elastic::$_config[$key] : false;
  }

  public static function remove_option($key) {
    if (isset(Elastic::$_config[$key])) {
      unset(Elastic::$_config[$key]);
    }

    return true;
  }

  public static function modules(Array $modules) {
    foreach ($modules as $k => $v) {
      Elastic::module($k, $v);
    }
  }

  public static function module($name, $path=null) {
    if (is_string($path)) {
      Elastic::$_modules[$name] = $path;

      if (file_exists(Elastic::$_modules[$name].DS.'init'.PHP_EXT)) {
        include Elastic::$_modules[$name].DS.'init'.PHP_EXT;
      }

      return true;
    } elseif ($path === false) {
      if (isset(Elastic::$_modules[$name])) {
        unset(Elastic::$_modules[$name]);
      }

      return true;
    } elseif (isset(Elastic::$_modules[$name])) {
      return Elastic::$_modules[$name];
    } else {
      return false;
    }
  }

  public static function file_iexists($file, $boolean=true, $replace=true) {
    if ($replace === true) {
      $file = str_replace(array('/', '\\'), DS, $file);
    }

    $first = substr($file, 0, strpos($file, DS));
    if (realpath($first.DS) === realpath($first.DS.'..')) {
      $found = $first.DS;
      $file = preg_replace('#^'.$first.'#', '', $file);
    } else {
      $found = DOC_ROOT.DS;
    }

    foreach (explode(DS, $file) as $part) {
      if ($part === '') {
        continue;
      }

      $break = true;
      foreach (glob($found.'*') as $potential) {
        while (($pos = strpos($potential, DS)) !== false) {
          $potential = substr($potential, $pos+1);
        }

        if (strtolower($potential) === strtolower($part)) {
          $found .= $potential;

          if (is_dir($found)) {
            $found .= DS;
          }

          $break = false;
          break;
        }
      }

      if ($break) { return false; }
    }

    if ($boolean) {
      return true;
    } else {
      return $found;
    }
  }
}

?>
