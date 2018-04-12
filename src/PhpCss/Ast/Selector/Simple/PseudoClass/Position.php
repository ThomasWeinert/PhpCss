<?php

namespace PhpCss\Ast\Selector\Simple\PseudoClass {

  use PhpCss\Ast\Selector\Simple;

  class Position extends Simple {

    public $repeat = 0;
    public $add = 0;

    public function __construct($repeat, $add) {
      $this->repeat = $repeat;
      $this->add = $add;
    }
  }
}
