<?php

class PhpCssAstSelectorSimpleId extends PhpCssAstSelectorSimple {

  public $id = '';

  public function __construct($id = '') {
    $this->id = '';
  }
}