<?php

namespace PhpCss\Ast\Selector\Simple {

  use PhpCss\Ast;

  class ClassName extends Ast\Selector\Simple {

    public $className = '';

    public function __construct(string $className) {
      $this->className = $className;
    }
  }
}
