<?php

namespace PhpCss\Ast\Selector\Simple {

  use PhpCss\Ast;

  class Type extends Ast\Selector\Simple {

    public $namespacePrefix = '';
    public $elementName = '';

    public function __construct($name, $prefix = '') {
      $this->namespacePrefix = $prefix;
      $this->elementName = $name;
    }
  }
}
