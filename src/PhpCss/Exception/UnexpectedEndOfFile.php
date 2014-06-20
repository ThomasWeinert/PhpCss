<?php
/**
* Exception thrown if an unexpected end of file is detected.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2014 PhpCss Team
*/

namespace PhpCss\Exception {

  use PhpCss;

  /**
  * Exception thrown if an unexpected end of file is detected.
  */
  class UnexpectedEndOfFile extends Parser {

    /**
     * @param array(PhpCss\Scanner\Token) $expectedTokens
     */
    public function __construct(array $expectedTokens) {
      $this->_expectedTokens = $expectedTokens;

      $expectedTokenStrings = array();
      foreach ($expectedTokens as $expectedToken) {
        $expectedTokenStrings[] = PhpCss\Scanner\Token::typeToString($expectedToken);
      }

      parent::__construct(
        'Parse error: Unexpected end of file was found while one of '.
        implode(", ", $expectedTokenStrings).' was expected.'
      );
    }
  }
}
