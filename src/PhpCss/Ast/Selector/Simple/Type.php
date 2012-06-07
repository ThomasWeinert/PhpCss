<?php

class PhpCssAstSelectorSimpleType extends PhpCssAstSelectorSimple {

  public $namespacePrefix = '*';
  public $elementName = '';

  public function __construct($name, $prefix = '*') {
    $this->namespacePrefix = $prefix;
    $this->elementName = $name;
  }
}