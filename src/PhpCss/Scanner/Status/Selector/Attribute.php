<?php
/**
* Scan attribute selectors
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2014 PhpCss Team
*/

namespace PhpCss\Scanner\Status\Selector {

  use PhpCss\Scanner;

  /**
   * Scan attribute selectors
   */
  class Attribute extends Scanner\Status {

    /**
    * single char tokens
    * @var array
    */
    private $_tokenChars = array(
      Scanner\Token::ATTRIBUTE_SELECTOR_END => ']',
      Scanner\Token::SINGLEQUOTE_STRING_START => "'",
      Scanner\Token::DOUBLEQUOTE_STRING_START => '"'
    );

    /**
    * patterns for more complex tokens
    * @var array
    */
    private $_tokenPatterns = array(
      Scanner\Token::ATTRIBUTE_OPERATOR => Scanner\Patterns::ATTRIBUTE_OPERATOR,
      Scanner\Token::WHITESPACE => Scanner\Patterns::WHITESPACE,
      Scanner\Token::NUMBER => Scanner\Patterns::NUMBER,
      Scanner\Token::IDENTIFIER => Scanner\Patterns::IDENTIFIER
    );

    /**
    * Try to get token in buffer at offset position.
    *
    * @param string $buffer
    * @param integer $offset
    * @return Scanner\Token
    */
    public function getToken($buffer, $offset): ?Scanner\Token {
      if ($token = $this->matchCharacters($buffer, $offset, $this->_tokenChars)) {
        return $token;
      }
      if ($token = $this->matchPatterns($buffer, $offset, $this->_tokenPatterns)) {
        return $token;
      }
      return NULL;
    }

    /**
    * Check if token ends status
    *
    * @param Scanner\Token $token
    * @return boolean
    */
    public function isEndToken(Scanner\Token $token): bool {
      return $token->type === Scanner\Token::ATTRIBUTE_SELECTOR_END;
    }

    /**
    * Get new (sub)status if needed.
    *
    * @param Scanner\Token $token
    * @return Scanner\Status|NULL
    */
    public function getNewStatus(Scanner\Token $token): ?Scanner\Status {
      switch ($token->type) {
      case Scanner\Token::SINGLEQUOTE_STRING_START :
        return new Scanner\Status\Text\Single();
      case Scanner\Token::DOUBLEQUOTE_STRING_START :
        return new Scanner\Status\Text\Double();
      }
      return NULL;
    }
  }
}
