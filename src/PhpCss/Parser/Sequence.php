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
    $token = $this->read(
      array(
		    PhpCssScannerToken::TYPE_SELECTOR,
		    PhpCssScannerToken::ID_SELECTOR,
		    PhpCssScannerToken::CLASS_SELECTOR,
			  PhpCssScannerToken::PSEUDO_CLASS,
			  PhpCssScannerToken::ATTRIBUTE_SELECTOR_START
      )
    );
    while (isset($token)) {
      if ($selector = $this->createSelector($token)) {
        $sequence->simples[] = $selector;
      }
      switch ($token->type) {
      case PhpCssScannerToken::PSEUDO_CLASS :
        throw new LogicException('Implementation incomplete');
        break;
      case PhpCssScannerToken::ATTRIBUTE_SELECTOR_START :
        throw new LogicException('Implementation incomplete');
        break;
      case PhpCssScannerToken::COMBINATOR :
        $sequence->combinator = $this->createCombinator(
          $token, $this->delegate(get_class($this))
        );
        return $sequence;
      }
    	if ($this->endOfTokens()) {
    		$token = NULL;
    		continue;
    	}
	    $token = $this->read(
	      array(
			    PhpCssScannerToken::ID_SELECTOR,
			    PhpCssScannerToken::CLASS_SELECTOR,
			    PhpCssScannerToken::PSEUDO_CLASS,
			    PhpCssScannerToken::ATTRIBUTE_SELECTOR_START,
			    PhpCssScannerToken::COMBINATOR
	      )
      );
    }
    return $sequence;
  }

  private function createSelector(PhpCssScannerToken $token) {
    switch ($token->type) {
    case PhpCssScannerToken::TYPE_SELECTOR :
	    if (FALSE !== strpos($token->content, '|')) {
	      list($prefix, $name) = explode('|', $token->content);
	    } else {
	      $prefix = '*';
	      $name = $token->content;
	    }
	    return new PhpCssAstSelectorSimpleType($name, $prefix);
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
      return new PhpCssAstSelectorCombinatorFollowing($sequence);
    default :
      return new PhpCssAstSelectorCombinatorDescendant($sequence);
    }
  }
}

