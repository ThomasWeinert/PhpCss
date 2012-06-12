<?php

class PhpCssAstSelectorSimplePseudoClassPosition extends PhpCssAst {

  public $repeat = 0;
  public $add = 0;

  public function __construct($repeat, $add) {
    $this->repeat = $repeat;
    $this->add = $add;
  }
}