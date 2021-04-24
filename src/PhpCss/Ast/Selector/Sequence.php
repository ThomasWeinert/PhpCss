<?php

namespace PhpCss\Ast\Selector {

  use PhpCss\Ast;

  class Sequence extends Ast\Selector {

    /**
     * @var array(Simple)
     */
    public $simples = [];
    public $combinator = NULL;

    /**
     * @param Simple[] $simples
     * @param Combinator|NULL $combinator
     */
    public function __construct(array $simples = [], Combinator $combinator = NULL) {
      $this->simples = $simples;
      $this->combinator = $combinator;
    }

    /**
     * Accept visitors, because this element has children, enter and leave are called.
     *
     * @param Ast\Visitor $visitor
     * @return null|void
     */
    public function accept(Ast\Visitor $visitor): void {
      if ($visitor->visitEnter($this)) {
        foreach ($this->simples as $simple) {
          /**
           * @var Simple $simple
           */
          $simple->accept($visitor);
        }
        if (isset($this->combinator)) {
          $this->combinator->accept($visitor);
        }
        $visitor->visitLeave($this);
      }
    }
  }
}
