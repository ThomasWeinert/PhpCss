<?php
namespace PhpCss\Ast\Selector\Simple\PseudoClass {

  use PhpCss\Ast\Selector\Simple\PseudoClass;

  class Language extends PseudoClass {

    public $language = '';

    public function __construct($language) {
      $this->language = $language;
    }
  }
}