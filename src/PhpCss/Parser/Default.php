<?php
/**
* Default parsing status, expecting a group of selector sequences
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2012 PhpCss Team
*
* @package PhpCss
* @subpackage Parser
*/

/**
* Default parsing status, expecting a group of selector sequences
*
* @package PhpCss
* @subpackage Parser
*/
class PhpCssParserDefault extends PhpCssParser {

  /**
  * Tokens that start a sequence, if anything except whitespaces
  * is found it delegates to the sequence parser
  *
  * @var array
  */
  private $_expectedTokens = array(
    phpCssScannerToken::WHITESPACE,
    PhpCssScannerToken::IDENTIFIER,
    PhpCssScannerToken::ID_SELECTOR,
    PhpCssScannerToken::CLASS_SELECTOR,
    PhpCssScannerToken::PSEUDO_CLASS,
    PhpCssScannerToken::PSEUDO_ELEMENT,
    PhpCssScannerToken::ATTRIBUTE_SELECTOR_START
  );

  /**
  * Start parsing looking for anything valid except whitespaces, add
  * returned sequences to the group
  *
  * @see PhpCssParser::parse()
  * @return PhpCssAstSelectorGroup
  */
  public function parse() {
    $group = new PhpCssAstSelectorGroup();
    $this->ignore(PhpCssScannerToken::WHITESPACE);
    while (!$this->endOfTokens()) {
      $currentToken = $this->lookahead($this->_expectedTokens);
      if ($currentToken->type == phpCssScannerToken::WHITESPACE) {
        $this->read(phpCssScannerToken::WHITESPACE);
        continue;
      }
      $group[] = $this->delegate('PhpCssParserSequence');
    }
    return $group;
  }
}
