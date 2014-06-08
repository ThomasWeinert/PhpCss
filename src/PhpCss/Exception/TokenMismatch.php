<?php
/**
* Exception thrown if a token is encountered which wasn't expected.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2014 PhpCss Team
*/

namespace PhpCss\Exception {

  use PhpCss;

  /**
  * Exception thrown if a token is encountered which wasn't expected.
  */
  class TokenMismatch extends Token {

    public function __construct(PhpCss\Scanner\Token $encounteredToken, array $expectedTokens) {
      $this->_encounteredToken = $encounteredToken;
      $this->_expectedTokens = $expectedTokens;

      $expectedTokenStrings = array();
      foreach ($expectedTokens as $expectedToken) {
        $expectedTokenStrings[] = PhpCss\Scanner\Token::typeToString($expectedToken);
      }

      parent::__construct(
       'Parse error: Found '.(string)$encounteredToken .
       ' while one of '.implode(", ", $expectedTokenStrings).' was expected.'
      );
    }
  }
}