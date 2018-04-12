<?php

namespace PhpCss\Ast\Value {

  use PhpCss;

  class Literal extends PhpCss\Ast {

    public $value;

    public function __construct($value) {
      $this->value = $value;
    }
  }
}
