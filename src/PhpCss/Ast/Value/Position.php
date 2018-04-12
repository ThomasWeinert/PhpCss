<?php

namespace PhpCss\Ast\Value {

  use PhpCss;

  class Position extends PhpCss\Ast {

    public $repeat = 0;
    public $add = 0;

    public function __construct($repeat, $add) {
      $this->repeat = $repeat;
      $this->add = $add;
    }
  }
}
