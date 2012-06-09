<?php
/**
* The attribute parser parses a simple attribute selector.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2012 PhpCss Team
*
* @package PhpCss
* @subpackage Parser
*/

/**
* The attribute parser parses a simple attribute selector.
*
* The attribute value can be an string if a string start is found it delgates to a string
* parser.
*
* @package PhpCss
* @subpackage Parser
*/
class PhpCssParserAttribute extends PhpCssParser {

  private $_operators = array(
    '^=' => PhpCssAstSelectorSimpleAttribute::MATCH_PREFIX,
    '$=' => PhpCssAstSelectorSimpleAttribute::MATCH_SUFFIX,
    '*=' => PhpCssAstSelectorSimpleAttribute::MATCH_SUBSTRING,
    '=' => PhpCssAstSelectorSimpleAttribute::MATCH_EQUALS,
    '~=' => PhpCssAstSelectorSimpleAttribute::MATCH_INCLUDES,
    '|=' => PhpCssAstSelectorSimpleAttribute::MATCH_DASHMATCH
  );

  public function parse() {
    $token = $this->read(PhpCssScannerToken::IDENTIFIER);
    $attribute = new PhpCssAstSelectorSimpleAttribute($token->content);
    $token = $this->read(
      array(
        PhpCssScannerToken::ATTRIBUTE_OPERATOR,
        PhpCssScannerToken::ATTRIBUTE_SELECTOR_END
      )
    );
    if ($token->type == PhpCssScannerToken::ATTRIBUTE_OPERATOR) {
      $attribute->match = $this->_operators[$token->content];
      $token = $this->read(
        array(
          PhpCssScannerToken::NUMBER,
          PhpCssScannerToken::IDENTIFIER,
          PhpCssScannerToken::SINGLEQUOTE_STRING_START,
          PhpCssScannerToken::DOUBLEQUOTE_STRING_START
        )
      );
      switch ($token->type) {
      case PhpCssScannerToken::NUMBER :
      case PhpCssScannerToken::IDENTIFIER :
        $attribute->literal = $token->content;
        break;
      case PhpCssScannerToken::SINGLEQUOTE_STRING_START :
      case PhpCssScannerToken::DOUBLEQUOTE_STRING_START :
        $attribute->literal = $this->delegate('PhpCssParserString');
        break;
      }
      $this->read(PhpCssScannerToken::ATTRIBUTE_SELECTOR_END);
    }
    return $attribute;
  }
}
