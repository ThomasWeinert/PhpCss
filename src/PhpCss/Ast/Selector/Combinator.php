<?php

abstract class PhpCssAstSelectorCombinator extends PhpCssAstSelector {

  public $sequence;

  public function __construct(PhpCssAstSelectorSequence $sequence = NULL) {
    $this->sequence = $sequence;
  }
}