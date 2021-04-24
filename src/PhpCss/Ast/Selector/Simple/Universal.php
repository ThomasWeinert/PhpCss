<?php

namespace PhpCss\Ast\Selector\Simple {

  use PhpCss\Ast;

  class Universal extends Ast\Selector\Simple {

    public $namespacePrefix = '*';

    public function __construct(string $prefix) {
      $this->namespacePrefix = $prefix;
    }
  }
}
