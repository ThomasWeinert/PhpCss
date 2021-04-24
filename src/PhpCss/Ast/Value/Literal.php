<?php

namespace PhpCss\Ast\Value {

  use PhpCss;

  class Literal extends PhpCss\Ast\Node {

    public $value;

    public function __construct(string $value) {
      $this->value = $value;
    }
  }
}
