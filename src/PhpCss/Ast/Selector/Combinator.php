<?php
namespace PhpCss\Ast\Selector  {

  use PhpCss\Ast;

  abstract class Combinator extends Ast\Selector {

    public $sequence;

    public function __construct(Sequence $sequence = NULL) {
      $this->sequence = $sequence;
    }

    public function accept(Ast\Visitor $visitor) {
      $visitor->visitEnter($this);
      $visitor->visit($this);
      $this->sequence->accept($visitor);
      $visitor->visitLeave($this);
    }
  }
}
