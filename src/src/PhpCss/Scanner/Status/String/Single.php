<?php
/**
* PhpCssScannerStatusStringSingle checks for tokens in a single quoted string.
*
* @version $Id: Single.php 429 2010-03-29 08:05:32Z subjective $
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2010 Bastian Feder, Thomas Weinert
*
* @package PhpCss
* @subpackage Scanner
*/

/**
* PhpCssScannerStatusStringSingle checks for tokens in a single quoted string.
*
* @package PhpCss
* @subpackage Scanner
*/
class PhpCssScannerStatusStringSingle extends PhpCssScannerStatus {

  /**
  * Try to get token in buffer at offset position.
  *
  * @param string $buffer
  * @param integer $offset
  * @return PhpCssScannerToken
  */
  public function getToken($buffer, $offset) {
    if ("'" === substr($buffer, $offset, 1)) {
      return new PhpCssScannerToken(
        PhpCssScannerToken::SINGLEQUOTE_STRING_END, "'", $offset
      );
    } else {
      $tokenString = substr($buffer, $offset, 2);
      if ("\\'" == $tokenString ||
          '\\\\' == $tokenString) {
        return new PhpCssScannerToken(
           PhpCssScannerToken::STRING_ESCAPED_CHARACTER, $tokenString, $offset
        );
      } else {
        $tokenString = $this->matchPattern(
          $buffer, $offset, '([^\\\\\']+)S'
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
      $token->type == PhpCssScannerToken::SINGLEQUOTE_STRING_END
    );
  }

  /**
  * Get new (sub)status if needed.
  *
  * Returns alway NULL, because a string never has a sub status
  *
  * @param PhpCssScannerToken $token
  * @return PhpCssScannerToken
  */
  public function getNewStatus($token) {
    return NULL;
  }
}