<?php

class PhpCssAstSelectorSimplePseudoClass extends PhpCssAstSelectorSimplePseudo {

  public $name = '';

  public function __construct($name) {
    $this->name = $name;
  }
}