<?php

class PhpCssParserSequence extends PhpCssParser {

  public function parse() {
    $sequence = new PhpCssAstSelectorSequence();
    $token = $this->read(
      array(
		    PhpCssScannerToken::TYPE_SELECTOR,
		    PhpCssScannerToken::ID_SELECTOR,
		    PhpCssScannerToken::CLASS_SELECTOR
      )
    );
    while (isset($token)) {
      if ($selector = $this->createSelector($token)) {
        $sequence->simples[] = $selector;
      }
    	if ($this->endOfTokens()) {
    		$token = NULL;
    		continue;
    	}
	    $token = $this->read(
	      array(
			    PhpCssScannerToken::ID_SELECTOR,
			    PhpCssScannerToken::CLASS_SELECTOR
	      )
      );
    }
    return $sequence;
  }

  private function createSelector($token) {
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
}

