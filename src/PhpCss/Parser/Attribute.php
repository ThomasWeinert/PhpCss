<?php
/**
* The attribute parser parses a simple attribute selector.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2014 PhpCss Team
*/
namespace PhpCss\Parser {

  use PhpCss;
  use PhpCss\Ast;
  use PhpCss\Scanner;

  /**
  * The attribute parser parses a simple attribute selector.
  *
  * The attribute value can be an string if a string start is found it delegates to a string
  * parser.
  */
  class Attribute extends PhpCss\Parser {

    private $_operators = array(
      '^=' => Ast\Selector\Simple\Attribute::MATCH_PREFIX,
      '$=' => Ast\Selector\Simple\Attribute::MATCH_SUFFIX,
      '*=' => Ast\Selector\Simple\Attribute::MATCH_SUBSTRING,
      '=' => Ast\Selector\Simple\Attribute::MATCH_EQUALS,
      '~=' => Ast\Selector\Simple\Attribute::MATCH_INCLUDES,
      '|=' => Ast\Selector\Simple\Attribute::MATCH_DASHMATCH
    );

    public function parse() {
      $token = $this->read(Scanner\Token::IDENTIFIER);
      $attribute = new Ast\Selector\Simple\Attribute($token->content);
      $token = $this->read(
        array(
          Scanner\Token::ATTRIBUTE_OPERATOR,
          Scanner\Token::ATTRIBUTE_SELECTOR_END
        )
      );
      if ($token->type == Scanner\Token::ATTRIBUTE_OPERATOR) {
        $attribute->match = $this->_operators[$token->content];
        $token = $this->read(
          array(
            Scanner\Token::NUMBER,
            Scanner\Token::IDENTIFIER,
            Scanner\Token::SINGLEQUOTE_STRING_START,
            Scanner\Token::DOUBLEQUOTE_STRING_START
          )
        );
        switch ($token->type) {
        case Scanner\Token::NUMBER :
        case Scanner\Token::IDENTIFIER :
          $attribute->literal = $token->content;
          break;
        case Scanner\Token::SINGLEQUOTE_STRING_START :
        case Scanner\Token::DOUBLEQUOTE_STRING_START :
          $attribute->literal = $this->delegate(Text::CLASS);
          break;
        }
        $this->read(Scanner\Token::ATTRIBUTE_SELECTOR_END);
      }
      return $attribute;
    }
  }
}
