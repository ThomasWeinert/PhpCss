<?php

class PhpCssAstSelectorSimplePseudoElement extends PhpCssAstSelectorSimplePseudo {

  public $name = '';

  public function __construct($name) {
    $this->name = $name;
  }
}