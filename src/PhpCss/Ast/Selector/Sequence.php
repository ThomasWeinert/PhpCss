<?php

class PhpCssAstSelectorSequence extends PhpCssAstSelector {

  public $simples;
  public $combination;

  public function __construct(array $simples = array(), PhpCssAstSelectorCombination $combination = NULL) {
    $this->simples = $simples;
    $this->combination = $combination;
  }
}