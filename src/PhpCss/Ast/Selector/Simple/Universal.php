<?php

class PhpCssAstSelectorSimpleUniversal extends PhpCssAstSelectorSimple {

  public $namespacePrefix = '*';

  public function __construct($prefix) {
    $this->namespacePrefix = $prefix;
  }
}