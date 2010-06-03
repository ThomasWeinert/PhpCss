<?php
/**
* PhpCssScannerStatusSelectorAttribute checks for tokens of a attribute selector.
*
* @version $Id: Attributes.php 429 2010-03-29 08:05:32Z subjective $
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package PhpCss
* @subpackage Scanner
*/

/**
* PhpCssScannerStatusSelectorAttribute checks for tokens of a attribute selector.
*
* @package PhpCss
* @subpackage Scanner
*/
class PhpCssScannerStatusSelectorAttribute extends PhpCssScannerStatus {

  /**
  * single char tokens
  * @var array
  */
  protected $_tokenChars = array(
    PhpCssScannerToken::ATTRIBUTE_SELECTOR_END => ']'
  );

  /**
  * patterns for more complex tokens
  * @var array
  */
  protected $_tokenPatterns = array(
    PhpCssScannerToken::WHITESPACE => '([\r\n\t ]+)S',
    PhpCssScannerToken::ATTRIBUTE_NAME => '([a-z]+)S'
  );

  /**
  * Try to get token in buffer at offset position.
  *
  * @param string $buffer
  * @param integer $offset
  * @return PhpCssScannerToken
  */
  public function getToken($buffer, $offset) {
    $char = substr($buffer, $offset, 1);
    foreach ($this->_tokenChars as $type => $expectedChar) {
      if ($char === $expectedChar) {
        return new PhpCssScannerToken(
          $type, $char, $offset
        );
      }
    }
    foreach ($this->_tokenPatterns as $type => $pattern) {
      $tokenString = $this->matchPattern(
        $buffer, $offset, $pattern
      );
      if (!empty($tokenString)) {
        return new PhpCssScannerToken(
          $type, $tokenString, $offset
        );
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
    return $token->type == PhpCssScannerToken::ATTRIBUTE_SELECTOR_END;
  }

  /**
  * Get new (sub)status if needed.
  *
  * @param PhpCssScannerToken $token
  * @return PhpCssScannerStatus
  */
  public function getNewStatus($token) {
    switch ($token->type) {
    case PhpCssScannerToken::SINGLEQUOTE_STRING_START :
      return new PhpCssScannerStatusStringSingle();
    case PhpCssScannerToken::DOUBLEQUOTE_STRING_START :
      return new PhpCssScannerStatusStringDouble();
    }
    return NULL;
  }
}