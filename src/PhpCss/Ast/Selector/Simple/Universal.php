<?php

namespace PhpCss\Ast\Selector\Simple {

  use PhpCss\Ast;

  class Universal extends Ast\Selector\Simple {

    public $namespacePrefix = '*';

    public function __construct($prefix) {
      $this->namespacePrefix = $prefix;
    }
  }
}
