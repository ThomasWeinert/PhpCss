<?php
/**
* The Sequence parser parses a list of simple selector tokens into the AST.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2012 PhpCss Team
*
* @package PhpCss
* @subpackage Parser
*/

/**
* The Sequence parser parses a list of simple selector tokens into the AST.
*
* It delegates to separate parsers for pseude classes and attributes.
*
* A css combinator delegates to a new instance of this class.
*
* @package PhpCss
* @subpackage Parser
*/
class PhpCssParserSequence extends PhpCssParser {

  /**
  * Parse the token stream for a simple selector sequence,
  * after the first element the typeselector is not allowed any more,
  * but a combinator is possible.
  */
  public function parse() {
    $sequence = new PhpCssAstSelectorSequence();
    $token = $this->lookahead(
      array(
        PhpCssScannerToken::IDENTIFIER,
        PhpCssScannerToken::ID_SELECTOR,
        PhpCssScannerToken::CLASS_SELECTOR,
        PhpCssScannerToken::PSEUDO_CLASS,
        PhpCssScannerToken::PSEUDO_ELEMENT,
        PhpCssScannerToken::ATTRIBUTE_SELECTOR_START
      )
    );
    while (isset($token)) {
      if ($selector = $this->createSelector($token)) {
        $this->read($token->type);
        $sequence->simples[] = $selector;
      }
      switch ($token->type) {
      case PhpCssScannerToken::SEPARATOR :
        $this->read(PhpCssScannerToken::SEPARATOR);
        return $sequence;
      case PhpCssScannerToken::PSEUDO_CLASS :
        $sequence->simples[] = $this->delegate('PhpCssParserPseudoClass');
        break;
      case PhpCssScannerToken::PSEUDO_ELEMENT :
        $sequence->simples[] = $this->createPseudoElement($token);
        $this->read($token->type);
        break;
      case PhpCssScannerToken::ATTRIBUTE_SELECTOR_START :
        $this->read($token->type);
        $sequence->simples[] = $this->delegate('PhpCssParserAttribute');
        break;
      case PhpCssScannerToken::COMBINATOR :
      case PhpCssScannerToken::WHITESPACE :
        $this->read($token->type);
        $sequence->combinator = $this->createCombinator(
          $token, $this->delegate(get_class($this))
        );
        return $sequence;
      }
      if ($this->endOfTokens()) {
        $token = NULL;
        continue;
      }
      $token = $this->lookahead(
        array(
          PhpCssScannerToken::ID_SELECTOR,
          PhpCssScannerToken::CLASS_SELECTOR,
          PhpCssScannerToken::PSEUDO_CLASS,
          PhpCssScannerToken::PSEUDO_ELEMENT,
          PhpCssScannerToken::ATTRIBUTE_SELECTOR_START,
          PhpCssScannerToken::COMBINATOR,
          PhpCssScannerToken::WHITESPACE,
          PhpCssScannerToken::SEPARATOR
        )
      );
    }
    return $sequence;
  }

  private function createSelector(PhpCssScannerToken $token) {
    switch ($token->type) {
    case PhpCssScannerToken::IDENTIFIER :
      if (FALSE !== strpos($token->content, '|')) {
        list($prefix, $name) = explode('|', $token->content);
      } else {
        $prefix = '*';
        $name = $token->content;
      }
      if ($name == '*') {
        return new PhpCssAstSelectorSimpleUniversal($prefix);
      } else {
        return new PhpCssAstSelectorSimpleType($name, $prefix);
      }
    case PhpCssScannerToken::ID_SELECTOR :
      return new PhpCssAstSelectorSimpleId(substr($token->content, 1));
    case PhpCssScannerToken::CLASS_SELECTOR :
      return new PhpCssAstSelectorSimpleClass(substr($token->content, 1));
    }
    return NULL;
  }

  private function createCombinator(
    PhpCssScannerToken $token,
    PhpCssAstSelectorSequence $sequence
  ) {
    switch (trim($token->content)) {
    case '>' :
      return new PhpCssAstSelectorCombinatorChild($sequence);
    case '+' :
      return new PhpCssAstSelectorCombinatorNext($sequence);
    case '~' :
      return new PhpCssAstSelectorCombinatorFollower($sequence);
    default :
      return new PhpCssAstSelectorCombinatorDescendant($sequence);
    }
  }

  private function createPseudoElement($token) {
    $name = substr($token->content, 2);
    switch ($name) {
    case 'first-line' :
    case 'first-letter' :
    case 'after' :
    case 'before' :
      return new PhpCssAstSelectorSimplePseudoElement($name);
    }
    throw new PhpCssExceptionUnknownPseudoElement($token);
  }
}

