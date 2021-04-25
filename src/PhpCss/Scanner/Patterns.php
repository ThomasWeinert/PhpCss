<?php
/**
* An abstract class providing token regex pattern for repeated usage.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2014 PhpCss Team
*/

namespace PhpCss\Scanner {
  /**
  * An abstract class providing token regex pattern for repeated usage.
  */
  abstract class Patterns {

    public const WHITESPACE = '([\r\n\t ]+)S';
    public const NUMBER = '(-?[\d]+)S';
    public const IDENTIFIER = '(
      (
        ( # namespace ncname
          [A-Z_a-z\\x{C0}-\\x{D6}\\x{D8}-\\x{F6}\\x{F8}-\\x{2FF}\\x{370}-\\x{37D}\\x{37F}-\\x{1FFF}\\x{200C}-\\x{200D}\\x{2070}-\\x{218F}\\x{2C00}-\\x{2FEF}\\x{3001}-\\x{D7FF}\\x{F900}-\\x{FDCF}\\x{FDF0}-\\x{FFFD}\\x{10000}-\\x{EFFFF}]
          [-A-Z_a-z0-9\\x{C0}-\\x{D6}\\x{D8}-\\x{F6}\\x{F8}-\\x{2FF}\\x{370}-\\x{37D}\\x{37F}-\\x{1FFF}\\x{200C}-\\x{200D}\\x{2070}-\\x{218F}\\x{2C00}-\\x{2FEF}\\x{3001}-\\x{D7FF}\\x{F900}-\\x{FDCF}\\x{FDF0}-\\x{FFFD}\\x{10000}-\\x{EFFFF}\\x{B7}\\x{300}-\\x{36F}\\x{203F}-\\x{2040}]*
        |
          \\* # the * is a joker for any namespace
        )
      \\| # a pipe separates namespace from element
      )?
      ( # element ncname
        [A-Z_a-z\\x{C0}-\\x{D6}\\x{D8}-\\x{F6}\\x{F8}-\\x{2FF}\\x{370}-\\x{37D}\\x{37F}-\\x{1FFF}\\x{200C}-\\x{200D}\\x{2070}-\\x{218F}\\x{2C00}-\\x{2FEF}\\x{3001}-\\x{D7FF}\\x{F900}-\\x{FDCF}\\x{FDF0}-\\x{FFFD}\\x{10000}-\\x{EFFFF}]
        [-A-Z_a-z0-9\\x{C0}-\\x{D6}\\x{D8}-\\x{F6}\\x{F8}-\\x{2FF}\\x{370}-\\x{37D}\\x{37F}-\\x{1FFF}\\x{200C}-\\x{200D}\\x{2070}-\\x{218F}\\x{2C00}-\\x{2FEF}\\x{3001}-\\x{D7FF}\\x{F900}-\\x{FDCF}\\x{FDF0}-\\x{FFFD}\\x{10000}-\\x{EFFFF}\\x{B7}\\x{300}-\\x{36F}\\x{203F}-\\x{2040}]*
      |
        \\* # the * is a joker for any element
      )
    )uxS';

    public const COMBINATOR = '(\s*[>+~]\s*)S';

    public const CLASS_SELECTOR = '(\.[^\r\n\t >+~.,#:()[\\]\\\'"]+)S';
    public const ID_SELECTOR = '(#[^\r\n\t >+~.,#:()[\\]\\\'"]+)S';
    public const PSEUDO_CLASS = '(:[^\r\n\t >+~.,#:()[\\]\\\'"]+)S';
    public const PSEUDO_ELEMENT = '(::[^\r\n\t .,#:()[\\]\\\'"]+)S';

    public const ATTRIBUTE_OPERATOR = '([~^$*|]?=)S';
    public const PSEUDO_CLASS_POSITION = '(\s*(([-+]?(\\d+)?n\\s*[-+]\\s*\\d+)|\\d+n)\s*)';
  }
}
