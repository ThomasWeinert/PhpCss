<?php

class PhpCssAstSelectorSimplePseudoClassLanguage extends PhpCssAstSelectorSimplePseudo {

  public $language = '';

  public function __construct($language) {
    $this->language = $language;
  }

}