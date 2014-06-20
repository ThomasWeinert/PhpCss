<?php

namespace PhpCss\Ast\Selector\Simple {

  use PhpCss\Ast;

  class Attribute extends Ast\Selector\Simple {

    const MATCH_EXISTS = 0;
    const MATCH_PREFIX = 1;
    const MATCH_SUFFIX = 2;
    const MATCH_SUBSTRING = 3;
    const MATCH_EQUALS = 4;
    const MATCH_INCLUDES = 5;
    const MATCH_DASHMATCH = 6;

    public $name = '';
    public $match = self::MATCH_EXISTS;
    public $literal;

    public function __construct($name = '', $match = self::MATCH_EXISTS, $literal = '') {
      $this->name = $name;
      $this->match = $match;
      $this->literal = $literal;
    }
  }
}
