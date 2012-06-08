<?php
/**
* PhpCssScannerStatusStringDouble checks for tokens in a double quoted string.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2012 PhpCss Team
*
* @package PhpCss
* @subpackage Scanner
*/

/**
* PhpCssScannerStatusStringDouble checks for tokens in a double quoted string.
*
* @package PhpCss
* @subpackage Scanner
*/
class PhpCssScannerStatusStringDouble extends PhpCssScannerStatus {

  /**
  * Try to get token in buffer at offset position.
  *
  * @param string $buffer
  * @param integer $offset
  * @return PhpCssScannerToken
  */
  public function getToken($buffer, $offset) {
    if ('"' === substr($buffer, $offset, 1)) {
      return new PhpCssScannerToken(
        PhpCssScannerToken::DOUBLEQUOTE_STRING_END, '"', $offset
      );
    } else {
      $tokenString = substr($buffer, $offset, 2);
      if ('\\"' == $tokenString ||
          '\\\\' == $tokenString) {
        return new PhpCssScannerToken(
           PhpCssScannerToken::STRING_ESCAPED_CHARACTER, $tokenString, $offset
        );
      } else {
        $tokenString = $this->matchPattern(
          $buffer, $offset, '([^\\\\"]+)S'
        );
        if (!empty($tokenString)) {
          return new PhpCssScannerToken(
            PhpCssScannerToken::STRING_CHARACTERS, $tokenString, $offset
          );
        }
      }
    }
    return NULL;
  }

  /**
  * Check if token ends status
  *
  * @param PhpCssScannerToken $token
  * @return boolean
  */
  public function isEndToken($token) {
    return (
      $token->type == PhpCssScannerToken::DOUBLEQUOTE_STRING_END
    );
  }

  /**
  * Get new (sub)status if needed.
  *
  * @param PhpCssScannerToken $token
  * @return NULL
  */
  public function getNewStatus($token) {
    return NULL;
  }
}