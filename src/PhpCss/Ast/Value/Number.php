<?php
namespace PhpCss\Ast\Value {

  use PhpCss;

  class Number extends PhpCss\Ast\Node {

    public $value = '';

    public function __construct(string $value) {
      $this->value = $value;
    }
  }
}
