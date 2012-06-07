<?php

class PhpCssAstSelectorSequence extends PhpCssAstSelector {

  public $simples = array();
  public $combinator = NULL;

  public function __construct(array $simples = array(), PhpCssAstSelectorCombinator $combinator = NULL) {
    $this->simples = $simples;
    $this->combinator = $combinator;
  }

  /**
  * Accept visitors, because this element has children, enter and leave are called.
  *
  * @param PhpCssAstVisitor $visitor
  */
  public function accept(PhpCssAstVisitor $visitor) {
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