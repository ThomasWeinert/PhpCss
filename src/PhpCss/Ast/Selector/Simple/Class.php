<?php

class PhpCssAstSelectorSimpleClass extends PhpCssAstSelectorSimple {

  public $className = '';

  public function __construct($className) {
    $this->className = $className;
  }
}