<?php
namespace PhpCss\Ast\Value {

  use PhpCss;

  class Language extends PhpCss\Ast\Node {

    public $language = '';

    public function __construct(string $language) {
      $this->language = $language;
    }
  }
}
