<?php
/**
* Exception thrown if a token is encountered which was not expected.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2014 PhpCss Team
*/

namespace PhpCss\Exception {

  use PhpCss;

  /**
  * Exception thrown if a token is encountered which was not expected.
  */
  class TokenMismatch extends Token {

    /**
     * @param PhpCss\Scanner\Token $encounteredToken
     * @param array $expectedTokens
     */
    public function __construct(PhpCss\Scanner\Token $encounteredToken, array $expectedTokens) {
      $this->_expectedTokens = $expectedTokens;

      $expectedTokenStrings = array();
      foreach ($expectedTokens as $expectedToken) {
        $expectedTokenStrings[] = PhpCss\Scanner\Token::typeToString($expectedToken);
      }

      parent::__construct(
        $encounteredToken,
       'Parse error: Found '.(string)$encounteredToken .
       ' while one of '.implode(", ", $expectedTokenStrings).' was expected.'
      );
    }
  }
}
