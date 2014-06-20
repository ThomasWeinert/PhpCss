<?php

namespace PhpCss\Ast\Selector\Simple {

  use PhpCss\Ast;

  class PseudoClass extends Ast\Selector\Simple {

    public $name = '';
    public $parameter = NULL;

    public function __construct($name, $parameter = NULL) {
      $this->name = $name;
      $this->parameter = $parameter;
    }

    public function accept(Ast\Visitor $visitor) {
      if ($this->parameter instanceOf Ast) {
        if ($visitor->visitEnter($this)) {
          $this->parameter->accept($visitor);
          $visitor->visitLeave($this);
        }
      } else {
        $visitor->visit($this);
      }
    }
  }
}
