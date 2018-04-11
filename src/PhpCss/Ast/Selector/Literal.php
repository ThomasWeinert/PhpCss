<?php

namespace PhpCss\Ast\Selector {

  use PhpCss;

  class Literal extends PhpCss\Ast {

    public $value;

    public function __construct($value) {
      $this->value = $value;
    }
  }
}
