<?php

class PhpCssAstSelectorSequence extends PhpCssAstSelector {

  public $simples = array();
  public $combination = NULL;

  public function __construct(array $simples = array(), PhpCssAstSelectorCombination $combination = NULL) {
    $this->simples = $simples;
    $this->combination = $combination;
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
      if (isset($this->combination)) {
        $this->combination->accept($visitor);
      }
      return $visitor->visitLeave($this);
    }
    return NULL;
  }
}