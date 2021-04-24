<?php

namespace PhpCss\Ast\Selector\Simple {

  use PhpCss\Ast;

  class Id extends Ast\Selector\Simple {

    public $id = '';

    public function __construct(string $id = '') {
      $this->id = $id;
    }
  }
}
