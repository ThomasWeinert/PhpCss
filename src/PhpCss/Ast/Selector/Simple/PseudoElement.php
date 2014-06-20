<?php

namespace PhpCss\Ast\Selector\Simple {

  use PhpCss\Ast;

  class PseudoElement extends Ast\Selector\Simple {

    public $name = '';

    public function __construct($name) {
      $this->name = $name;
    }
  }
}
