<?php

class Dispatcher {
  protected static $_listeners;

  public static function listen($event, $listener) {
    if (!isset(self::$_listeners[$event])) {
      self::$_listeners[$event] = array();
    }

    self::$_listeners[$event][] = $listener;
  }

  public static function notify($listener, Event $event) {
    if (is_a($listener, 'Closure') || is_string($listener)) {
      if ($fref = new ReflectionFunction($listener)) {
        $fref->invokeArgs(array_merge(array('event' => $event), $event->get_data()));
      }
    } elseif (is_array($listener) && is_callable($listener)) {
      if ($mref = new ReflectionMethod($listener[0], $listener[1])) {
        $mref->invokeArgs($mref->isStatic() ? null : $listener[0], array_merge(array('event' => $event), $event->get_data()));
      }
    } else {
      throw new Exception("Couldn't process listener: Expected an object, string or array (with 2 or more elements) but got ".gettype($listener).".");
    }
  }

  public static function notify_one(Event $event) {
    if (isset(self::$_listeners[$event->get_name()])) {
      foreach (self::$_listeners[$event->get_name()] as $listener) {
        if (self::notify($listener, $event)) {
          break;
        }
      }
    }
  }

  public static function notify_all(Event $event) {
    if (isset(self::$_listeners[$event->get_name()])) {
      foreach (self::$_listeners[$event->get_name()] as $listener) {
        self::notify($listener, $event);
      }
    }
  }
}

?>