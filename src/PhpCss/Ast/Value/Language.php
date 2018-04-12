<?php
namespace PhpCss\Ast\Value {

  use PhpCss;

  class Language extends PhpCss\Ast {

    public $language = '';

    public function __construct($language) {
      $this->language = $language;
    }
  }
}
