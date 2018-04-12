<?php
namespace PhpCss\Ast\Selector\Simple\PseudoClass {

  use PhpCss\Ast\Selector\Simple;

  class Language extends Simple {

    public $language = '';

    public function __construct($language) {
      $this->language = $language;
    }
  }
}
