<?php
/**
* PhpCss Scanner Status Selector
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2012 PhpCss Team
*
* @package PhpCss
* @subpackage Scanner
*/

/**
* PhpCss Scanner Status Selector recognizes token of a css selector sequence.
*
* @package PhpCss
* @subpackage Scanner
*/
class PhpCssScannerStatusSelector extends PhpCssScannerStatus {

  /**
  * single char tokens
  * @var array
  */
  protected $_tokenChars = array(
    PhpCssScannerToken::SEPARATOR => ',',
    PhpCssScannerToken::ATTRIBUTE_SELECTOR_START => '[',
    PhpCssScannerToken::PARENTHESES_START => '(',
    PhpCssScannerToken::PARENTHESES_END => ')',
    PhpCssScannerToken::SINGLEQUOTE_STRING_START => "'",
    PhpCssScannerToken::DOUBLEQUOTE_STRING_START => '"'
  );

  /**
  * patterns for more complex tokens
  * @var array
  */
  protected $_tokenPatterns = array(
    PhpCssScannerToken::COMBINATOR => PhpCssScannerPatterns::COMBINATOR,
    PhpCssScannerToken::CLASS_SELECTOR => PhpCssScannerPatterns::CLASS_SELECTOR,
    PhpCssScannerToken::ID_SELECTOR => PhpCssScannerPatterns::ID_SELECTOR,
    PhpCssScannerToken::PSEUDO_CLASS => PhpCssScannerPatterns::PSEUDO_CLASS,
    PhpCssScannerToken::PSEUDO_CLASS_POSITION => PhpCssScannerPatterns::PSEUDO_CLASS_POSITION,
    PhpCssScannerToken::PSEUDO_ELEMENT => PhpCssScannerPatterns::PSEUDO_ELEMENT,
    PhpCssScannerToken::WHITESPACE => PhpCssScannerPatterns::WHITESPACE,
    PhpCssScannerToken::NUMBER => PhpCssScannerPatterns::NUMBER,
    PhpCssScannerToken::IDENTIFIER => PhpCssScannerPatterns::IDENTIFIER
  );

  /**
  * Try to get token in buffer at offset position.
  *
  * @param string $buffer
  * @param integer $offset
  * @return FluentDOMSelectorCssToken
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
    return FALSE;
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
    case PhpCssScannerToken::ATTRIBUTE_SELECTOR_START :
      return new PhpCssScannerStatusSelectorAttribute();
    }
    return NULL;
  }
}