<?php

namespace PhpCss\Ast\Selector\Simple {

  use PhpCss\Ast;

  class Attribute extends Ast\Selector\Simple {

    public const MATCH_EXISTS = 0;
    public const MATCH_PREFIX = 1;
    public const MATCH_SUFFIX = 2;
    public const MATCH_SUBSTRING = 3;
    public const MATCH_EQUALS = 4;
    public const MATCH_INCLUDES = 5;
    public const MATCH_DASHMATCH = 6;

    public $name = '';
    public $match = self::MATCH_EXISTS;
    public $literal;

    public function __construct(
      string $name = '', int $match = self::MATCH_EXISTS, string $literal = ''
    ) {
      $this->name = $name;
      $this->match = $match;
      $this->literal = $literal;
    }
  }
}
