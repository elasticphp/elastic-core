<?php

class Container {
  const ROOT = 'default';

  protected static $_instances = array();

  public static function instance($profile=null) {
    if ($profile === null) {
      $profile = Container::ROOT;
    }

    if (!isset(Container::$_instances[Container::ROOT])) {
      Container::$_instances[Container::ROOT] = new Container(Container::ROOT, null);
    }

    if ($profile != Container::ROOT) {
      if (!isset(Container::$_instances[$profile])) {
        $instance = Container::instance()->child($profile);
      } else {
        $instance = Container::$_instances[$profile];
      }
    } else {
      $instance = Container::$_instances[Container::ROOT];
    }

    return $instance;
  }

  public static function factory($component, $profile=null, $options=array()) {
    return Container::instance($profile)->component($component, $options);
  }

  public static function __callstatic($method, $arguments) {
    $event = Event::factory('container.static_method_not_found', array('method' => $method, 'arguments' => $arguments))->notify_one();
    if (!$event->is_processed()) {
      throw new Exception('Static method not found: '.$method);
    }
  }

  protected $_profile;
  protected $_parent;
  protected $_factories = array();
  protected $_scopes = array();
  protected $_defaults = array();
  protected $_implementations = array();
  protected $_postinit_actions = array();
  protected $_objects = array();

  public function __construct($profile, Container $parent=null) {
    $this->_profile = $profile;
    $this->_parent = ($parent === null) ? null : $parent->get_profile();
    return $this;
  }
  
  public function __call($method, $arguments) {
    $event = Container::factory('\Elastic\System\Dispatcher\Event', null, array('name' => 'container.method_not_found', 'arguments' => array('method' => $method, 'arguments' => $arguments)));
    Container::factory('\Elastic\System\Dispatcher')->notify_until($event);
    if (!$event->is_processed()) {
      throw new Exception();
    }
    return $event->get_return_value();
  }

  public function get_profile() {
    return $this->_profile;
  }

  public function get_parent() {
    return ($this->_parent === null) ? null : Container::instance($this->_parent);
  }

  public function get_factory($name) {
    if (isset($this->_factories[$name])) {
      return $this->_factories[$name];
    } elseif (($this->_parent !== null) && ($factory = $this->get_parent()->get_factory($name)) && ($factory !== false)) {
      return $factory;
    } else {
      return false;
    }
  }

  public function set_factory($name, $method, Array $args=array()) {
    $this->_factories[$name] = array('name' => $name, 'method' => $method, 'args' => $args);
  }

  public function get_implementation($interface) {
    if (!is_string($interface)) {
      return false;
    }

    if (isset($this->_implementations[strtolower($interface)])) {
      return $this->_implementations[strtolower($interface)];
    } elseif (($this->_parent !== null) && ($implementation = $this->get_parent()->get_implementation($interface))) {
      return $implementation;
    } else {
      return false;
    }
  }

  public function set_implementation($interface, $implementation) {
    $this->_implementations[strtolower($interface)] = $implementation;
  }

  public function set_default($class, $method, $parameter, $value) {
    if (!isset($this->_defaults[$class])) {
      $this->_defaults[$class] = array();
    }

    if (!isset($this->_defaults[$class][$method])) {
      $this->_defaults[$class][$method] = array();
    }

    $this->_defaults[$class][$method][$parameter] = $value;
  }

  public function get_default($class=null, $method=null, $parameter=null) {
    if (is_string($class)) {
      if (isset($this->_defaults[$class])) {
        if (is_string($method)) {
          if (isset($this->_defaults[$class][$method])) {
            if (is_string($parameter)) {
              if (isset($this->_defaults[$class][$method][$parameter])) {
                return $this->_defaults[$class][$method][$parameter];
              } else {
                return false;
              }
            } else {
              return $this->_defaults[$class][$method];
            }
          } else {
            return false;
          }
        } else {
          return $this->_defaults[$class];
        }
      } else {
        return false;
      }
    } else {
      return $this->_defaults;
    }
  }

  public function get_postinit_action($name) {
    return isset($this->_postinit_actions[$name]) ? $this->_postinit_actions[$name] : false;
  }

  public function get_postinit_actions() {
    return $this->_postinit_actions;
  }

  public function set_postinit_action($name, $class, $method, $args=array()) {
    if (!is_string($name)) {
      return false;
    }

    $this->_postinit_actions[$name] = array('name' => $name, 'class' => $class, 'method' => $method, 'args' => $args);
    return true;
  }

  public function get_scope($class) {
    if (!is_string($class)) {
      return false;
    }

    return isset($this->_scopes[strtolower($class)]) ? $this->_scopes[strtolower($class)] : false;
  }

  public function get_scopes() {
    return $this->_scopes;
  }

  public function set_scope($class, $scope) {
    if (!is_string($class)) {
      return false;
    }

    if (($scope === 'prototype') || ($scope === 'singleton')) {
      $this->_scopes[strtolower($class)] = array('class' => $class, 'scope' => $scope, 'profile' => $this->_profile);
      return true;
    } else {
      return false;
    }
  }

  public function get_object($class) {
    if (!is_string($class)) {
      return false;
    }
    return isset($this->_objects[strtolower($class)]) ? $this->_objects[strtolower($class)] : false;
  }

  public function get_objects() {
    return $this->_objects;
  }

  public function set_object($class, $object) {
    if (!is_string($class)) {
      return false;
    }

    $this->_objects[strtolower($class)] = $object;
    return true;
  }

  public function child($profile) {
    if (!is_string($profile)) {
      return false;
    }

    $profile = $this->_profile.'/'.$profile;
    if (!isset(Container::$_instances[$profile])) {
      Container::$_instances[$profile] = new Container($profile, $this);
    }

    return Container::$_instances[$profile];
  }

  public function get_parameters($what, $options=array()) {
    if (is_array($what)) {
      $fref = new ReflectionMethod($what[0], $what[1]);
    } elseif (is_string($what) || is_callable($what, 'Closure')) {
      $fref = new ReflectionFunction($what);
    } else {
      return false;
    }

    $args = array();
    if ($fref->getNumberOfParameters() > 0) {
      if ($this->_parent !== null) {
        $args = Container::$_instances[$this->_parent]->get_parameters($what, $options);
      }

      foreach ($fref->getParameters() as $pref) {
        $arg = null;
        $set = false;

        if (isset($options[strtolower($pref->getName())])) {
          $arg = $options[strtolower($pref->getName())];
          $set = true;
        } elseif(is_object($fref->getDeclaringClass()) && ($_arg = $this->get_default($fref->getDeclaringClass()->getName(), $fref->getName(), $pref->getName()))) {
          $arg = $_arg;
          $set = true;
        } elseif (is_object($pref->getClass()) && ($pcref = $pref->getClass()) && ($_arg = $this->component($pcref->getName()))) {
          $arg = $_arg;
          $set = true;
        }

        if (!$set) {
          if ($pref->isDefaultValueAvailable()) {
            $arg = $pref->getDefaultValue();
            $set = true;
          } elseif ($pref->allowsNull()) {
            $arg = null;
            $set = true;
          } elseif ($pref->isOptional()) {
            $set = true;
          }
        }

        if ($set) {
          if (is_string($arg)) {
            if ($arg === '{{container}}') {
              $arg = $this;
            } elseif (preg_match('#^{{component(?::(.*))?:(.*)}}$#', $arg, $matches)) {
              if (!strlen($matches[1])) { $matches[1] = $this->_profile; }
              $arg = Container::factory($matches[2], $matches[1]);
            }
          }

          $args[$pref->getPosition()] = $arg;
        }
      }
    }

    return ($args === null) ? array() : $args;
  }

  public function component($component, $options=array()) {
    if ($factory = $this->get_factory($component)) {
      $factory['args'] = array_replace($factory['args'], $options);

      if (is_array($factory['method'])) {
        if (is_string($factory['method'][0]) && preg_match('#^{{component(?::(.*))?:(.*)}}$#', $factory['method'][0], $matches)) {
          if (!strlen($matches[1])) {
            $matches[1] = $this->_profile;
          }

          $factory['method'][0] = Container::factory($matches[2], $matches[1], $factory['args']);
        }
      } elseif (is_string($factory['method']) && preg_match('#^{{component(?::(.*))?:(.*)}}$#', $factory['method'], $matches)) {
        if (!strlen($matches[1])) {
          $matches[1] = $this->_profile;
        }

        return Container::factory($matches[2], $matches[1], $factory['args']);
      }

      if (is_a($factory['method'], 'Closure') || is_string($factory['method'])) {
        $fref = new ReflectionFunction($factory['method']);
        return $fref->invokeArgs($this->get_parameters($factory['method'], $factory['args']));
      } elseif (is_array($factory['method'])) {
        $fref = new ReflectionMethod($factory['method'][0], $factory['method'][1]);
        return $fref->invokeArgs(
          ($fref->isStatic() ? null : $factory['method'][0]),
          $this->get_parameters(array($factory['method'][0], $factory['method'][1]), ($factory['args'] === null) ? array() : $factory['args'])
        );
      }
    }

    if (!$cref = new ReflectionClass($component)) {
      return false;
    }

    if ($cref->isInterface()) {
      if ($implementation = $this->get_implementation($component)) {
        return $this->component($implementation);
      } else {
        return false;
      }
    }

    $scopes = $this->_scopes;
    for ($parent = $this->get_parent(); $parent !== null; $parent = $parent->get_parent()) {
      $scopes = array_replace($parent->get_scopes(), $scopes);
    }

    $scope = false;
    foreach ($scopes as $scope_) {
      if (($scope_['class'] !== null) && (strtolower($cref->getName()) !== strtolower($scope_['class'])) && !$cref->isSubclassOf($scope_['class'])) {
        continue;
      }

      if ($scope_['scope'] == 'singleton') {
        $scope = $scope_;

        if (($object = Container::instance($scope_['profile'])->get_object($scope_['class']))) {
          return $object;
        }
      }
    }

    if ($constructor = $cref->getConstructor()) {
      $args = $this->get_parameters(array($cref->getName(), $constructor->getName()), $options);
      $object = $cref->newInstanceArgs($args);
    } else {
      $object = $cref->newInstance();
    }

    $postinit_actions = $this->_postinit_actions;
    for ($parent = $this->get_parent(); $parent !== null; $parent = $parent->get_parent()) {
      $postinit_actions = array_replace($parent->get_postinit_actions(), $postinit_actions);
    }

    foreach ($postinit_actions as $postinit_action) {
      if (($postinit_action['class'] !== null) && (strtolower($cref->getName()) !== strtolower($postinit_action['class'])) && !$cref->isSubclassOf($postinit_action['class'])) { continue; }

      foreach ($postinit_action['args'] as &$arg) {
        if (is_string($arg)) {
          if ($arg === '{{object}}') {
            $arg = $object;
          } elseif ($arg === '{{container}}') {
            $arg = $this;
          } elseif (preg_match('#^{{component(?::(.*))?:(.*)}}$#', $arg, $matches)) {
            if (!strlen($matches[1])) { $matches[1] = $this->_profile; }
            $arg = Container::factory($matches[2], $matches[1]);
          }
        }
      }

      if (is_a($postinit_action['method'], 'Closure') || is_string($postinit_action['method'])) {
        $fref = new ReflectionFunction($postinit_action['method']);
        $fref->invokeArgs($this->get_parameters($postinit_action['method'], $postinit_action['args']));
      } elseif (is_array($postinit_action['method'])) {
        if ($postinit_action['method'][0] === '{{object}}') {
          $postinit_action['method'][0] = $object;
        }

        $fref = new ReflectionMethod($postinit_action['method'][0], $postinit_action['method'][1]);
        $fref->invokeArgs(
          ($fref->isStatic() ? null : $postinit_action['method'][0]),
          $this->get_parameters(array($postinit_action['method'][0], $postinit_action['method'][1]), $postinit_action['args'])
        );
      }
    }

    if (($scope !== false) && ($scope['scope'] == 'singleton')) {
      Container::instance($scope['profile'])->set_object(strtolower($scope['class']), $object);
    }

    return $object;
  }
}

?>
