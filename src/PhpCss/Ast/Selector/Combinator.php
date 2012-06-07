<?php

abstract class PhpCssAstSelectorCombinator extends PhpCssAstSelector {

  public $sequence;

  public function __construct(PhpCssAstSelectorSequence $sequence = NULL) {
    $this->sequence = $sequence;
  }

  public function accept(PhpCssAstVisitor $visitor) {
    $visitor->visitEnter($this);
    $this->sequence->accept($visitor);
    $visitor->visitLeave($this);
  }
}