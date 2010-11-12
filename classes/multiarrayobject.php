<?php

class MultiArrayObject extends ArrayObject {
  function __construct(Array $array=null, $flags=0, $iterator_class='ArrayIterator') {
    if ($array === null) { $array = array(); }
    $objects = array();

    foreach($array AS $k => $v) {
      if(is_array($v)) {
        $objects[$k] = new MultiArrayObject($v, $flags, $iterator_class);
      } else {
        $objects[$k] = $v;
      }
    }

    return parent::__construct($objects, $flags, $iterator_class);
  }

  public function offsetSet($name, $value) {
    if(is_array($value)) {
      $value = new MultiArrayObject($value, $this->getFlags(), $this->getIteratorClass());
    }

    return parent::offsetSet($name, $value);
  }

  public function __set($name, $value) {
    $this->offsetSet($name, $value);
  }

  public function array_copy() {
    $arr = array();
    foreach ($this as $k => $v) {
      if (is_array($v) || is_a($v, 'MultiArrayObject')) {
        $arr[$k] = $v->array_copy();
      } else {
        $arr[$k] = $v;
      }
    }
    return $arr;
  }
}

?>