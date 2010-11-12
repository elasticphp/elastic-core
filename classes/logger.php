<?php

class Logger {
  const ERROR = 1;
  const WARNING = 2;
  const NOTICE = 4;
  const DEBUG = 8;

  protected $_writer;

  public function __construct(Logger_iWriter $writer) {
    $this->_writer = $writer;
  }

  public function writer(Logger_iWriter $writer=null) {
    if ($writer === null) {
      return $this->_writer;
    } else {
      $this->_writer = $writer;
      return true;
    }
  }

  public function entry($message, $subject, $level) {
    if (!is_string($message)) {
      trigger_error('log expects a string for the message argument', E_USER_WARNING);
      return false;
    }

    if (!is_string($subject)) {
      trigger_error('log expects a string for the subject argument', E_USER_WARNING);
      return false;
    }

    if (!is_integer($level)) {
      trigger_error('log expects an integer for the level argument', E_USER_WARNING);
      return false;
    }

    $this->_writer->write($message, $subject, $level);
  }
}

?>