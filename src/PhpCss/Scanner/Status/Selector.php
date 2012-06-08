<?php
/**
* PhpCss Scanner Status Selector
*
* @version $Id: Default.php 429 2010-03-29 08:05:32Z subjective $
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
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
    PhpCssScannerToken::COMBINATOR => '(\s*[>+~]\s*)S',
    PhpCssScannerToken::WHITESPACE => '([\r\n\t ]+)S',
    PhpCssScannerToken::NUMBER => '([\d]+)S',
    PhpCssScannerToken::TYPE_SELECTOR => '([^\r\n\t .,#:()[\\]\\\'"]+)S',
    PhpCssScannerToken::CLASS_SELECTOR => '(\.[^\r\n\t .,#:()[\\]\\\'"]+)S',
    PhpCssScannerToken::ID_SELECTOR => '(#[^\r\n\t .,#:()[\\]\\\'"]+)S',
    PhpCssScannerToken::PSEUDO_CLASS => '(:[^\r\n\t .,#:()[\\]\\\'"]+)S',
    PhpCssScannerToken::PSEUDO_ELEMENT => '(::[^\r\n\t .,#:()[\\]\\\'"]+)S'
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