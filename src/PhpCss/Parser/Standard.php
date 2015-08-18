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

    const ALLOW_RELATIVE_SELECTORS = 1;

    private $options = 0;

    public function __construct(array &$tokens, $options = 0) {
      parent::__construct($tokens);
      $this->_options = $options;
    }

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
    * @see PhpCss\Parser::parse()
    * @return Ast\Selector\Group
    */
    public function parse() {
      $expectedTokens = $this->_expectedTokens;
      if (($this->_options & self::ALLOW_RELATIVE_SELECTORS) === self::ALLOW_RELATIVE_SELECTORS) {
        $expectedTokens[] = Scanner\Token::COMBINATOR;
      }
      $group = new Ast\Selector\Group();
      $this->ignore(Scanner\Token::WHITESPACE);
      while (!$this->endOfTokens()) {
        $currentToken = $this->lookahead($expectedTokens);
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
