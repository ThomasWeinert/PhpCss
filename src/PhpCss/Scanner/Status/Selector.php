<?php
/**
* Scanner Status "Selector"
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2014 PhpCss Team
*/

namespace PhpCss\Scanner\Status {

  use PhpCss\Scanner;

  /**
  * Scanner Status Selector recognizes token of a css selector sequence.
  */
  class Selector extends Scanner\Status {

    /**
    * single char tokens
    * @var array
    */
    protected $_tokenChars = array(
      Scanner\Token::SEPARATOR => ',',
      Scanner\Token::ATTRIBUTE_SELECTOR_START => '[',
      Scanner\Token::PARENTHESES_START => '(',
      Scanner\Token::PARENTHESES_END => ')',
      Scanner\Token::SINGLEQUOTE_STRING_START => "'",
      Scanner\Token::DOUBLEQUOTE_STRING_START => '"'
    );

    /**
    * patterns for more complex tokens
    * @var array
    */
    protected $_tokenPatterns = array(
      Scanner\Token::CLASS_SELECTOR => Scanner\Patterns::CLASS_SELECTOR,
      Scanner\Token::ID_SELECTOR => Scanner\Patterns::ID_SELECTOR,
      Scanner\Token::PSEUDO_CLASS => Scanner\Patterns::PSEUDO_CLASS,
      Scanner\Token::PSEUDO_CLASS_POSITION => Scanner\Patterns::PSEUDO_CLASS_POSITION,
      Scanner\Token::PSEUDO_ELEMENT => Scanner\Patterns::PSEUDO_ELEMENT,
      Scanner\Token::IDENTIFIER => Scanner\Patterns::IDENTIFIER,
      Scanner\Token::COMBINATOR => Scanner\Patterns::COMBINATOR,
      Scanner\Token::WHITESPACE => Scanner\Patterns::WHITESPACE,
      Scanner\Token::NUMBER => Scanner\Patterns::NUMBER
    );

    /**
    * Try to get token in buffer at offset position.
    *
    * @param string $buffer
    * @param integer $offset
    * @return Scanner\Token
    */
    public function getToken($buffer, $offset) {
      $char = substr($buffer, $offset, 1);
      foreach ($this->_tokenChars as $type => $expectedChar) {
        if ($char === $expectedChar) {
          return new Scanner\Token(
            $type, $char, $offset
          );
        }
      }
      foreach ($this->_tokenPatterns as $type => $pattern) {
        $tokenString = $this->matchPattern(
          $buffer, $offset, $pattern
        );
        if (!empty($tokenString)) {
          return new Scanner\Token(
            $type, $tokenString, $offset
          );
        }
      }
      return NULL;
    }

    /**
    * Check if token ends status
    *
    * @param Scanner\Token $token
    * @return boolean
    */
    public function isEndToken(Scanner\Token $token) {
      return FALSE;
    }

    /**
    * Get new (sub)status if needed.
    *
    * @param Scanner\Token $token
    * @return Scanner\Status
    */
    public function getNewStatus(Scanner\Token $token) {
      switch ($token->type) {
      case Scanner\Token::SINGLEQUOTE_STRING_START :
        return new Scanner\Status\Text\Single();
      case Scanner\Token::DOUBLEQUOTE_STRING_START :
        return new Scanner\Status\Text\Double();
      case Scanner\Token::ATTRIBUTE_SELECTOR_START :
        return new Scanner\Status\Selector\Attribute();
      }
      return NULL;
    }
  }
}
