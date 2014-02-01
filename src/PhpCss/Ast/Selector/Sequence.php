<?php
namespace PhpCss\Ast\Selector  {

  use PhpCss\Ast;

  class Sequence extends Ast\Selector {

    public $simples = array();
    public $combinator = NULL;

    public function __construct(array $simples = array(), Combinator $combinator = NULL) {
      $this->simples = $simples;
      $this->combinator = $combinator;
    }

    /**
     * Accept visitors, because this element has children, enter and leave are called.
     *
     * @param Ast\Visitor $visitor
     * @return null|void
     */
    public function accept(Ast\Visitor $visitor) {
      if ($visitor->visitEnter($this)) {
        foreach ($this->simples as $simple) {
          $simple->accept($visitor);
        }
        if (isset($this->combinator)) {
          $this->combinator->accept($visitor);
        }
        return $visitor->visitLeave($this);
      }
      return NULL;
    }
  }
}