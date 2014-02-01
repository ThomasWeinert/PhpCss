<?php
/**
* Default parsing status, expecting a group of selector sequences
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2014 PhpCss Team
*/

namespace PhpCss\Parser {

  use PhpCss;
  use PhpCss\Ast;
  use PhpCss\Scanner;

  /**
  * Default parsing status, expecting a group of selector sequences
  *
  * @package PhpCss
  * @subpackage Parser
  */
  class Standard extends PhpCss\Parser {

    /**
    * Tokens that start a sequence, if anything except whitespaces
    * is found it delegates to the sequence parser
    *
    * @var array
    */
    private $_expectedTokens = array(
      Scanner\Token::WHITESPACE,
      Scanner\Token::IDENTIFIER,
      Scanner\Token::ID_SELECTOR,
      Scanner\Token::CLASS_SELECTOR,
      Scanner\Token::PSEUDO_CLASS,
      Scanner\Token::PSEUDO_ELEMENT,
      Scanner\Token::ATTRIBUTE_SELECTOR_START
    );

    /**
    * Start parsing looking for anything valid except whitespaces, add
    * returned sequences to the group
    *
    * @see PhpCssParser::parse()
    * @return PhpCssAstSelectorGroup
    */
    public function parse() {
      $group = new Ast\Selector\Group();
      $this->ignore(Scanner\Token::WHITESPACE);
      while (!$this->endOfTokens()) {
        $currentToken = $this->lookahead($this->_expectedTokens);
        if ($currentToken->type == Scanner\Token::WHITESPACE) {
          $this->read(Scanner\Token::WHITESPACE);
          continue;
        }
        $group[] = $this->delegate(Sequence::CLASS);
      }
      return $group;
    }
  }
}
