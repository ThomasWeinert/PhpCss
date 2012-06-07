<?php

class PhpCssParserSequence extends PhpCssParser {

  private $_expectedTokens = array(
    PhpCssScannerToken::TYPE_SELECTOR,
    PhpCssScannerToken::ID_SELECTOR,
    PhpCssScannerToken::CLASS_SELECTOR
  );

  public function parse() {
    $sequence = $this->createSequence();
    while (!$this->endOfTokens()) {
      $currentToken = $this->read($this->_expectedTokens);
      switch ($currentToken->type) {
      case PhpCssScannerToken::TYPE_SELECTOR :
        $sequence->simples[] = $this->createSelectorSimpleType($currentToken);
        break;
      case PhpCssScannerToken::ID_SELECTOR :
        $sequence->simples[] = $this->createSelectorSimpleId($currentToken);
        break;
      case PhpCssScannerToken::CLASS_SELECTOR :
        $sequence->simples[] = $this->createSelectorSimpleClass($currentToken);
        break;
      }
    }
    return $sequence;
  }

  /**
  *
  * @return PhpCssAstSelectorSequence
  */
  private function createSequence() {
    return new PhpCssAstSelectorSequence();
  }


  private function createSelectorSimpleType(PhpCssScannerToken $token) {
    if (FALSE !== strpos($token->content, '|')) {
      list($prefix, $name) = explode('|', $token->content);
    } else {
      $prefix = '*';
      $name = $token->content;
    }
    return new PhpCssAstSelectorSimpleType($name, $prefix);
  }

  private function createSelectorSimpleId(PhpCssScannerToken $token) {
    return new PhpCssAstSelectorSimpleId(substr($token->content, 1));
  }

  private function createSelectorSimpleClass(PhpCssScannerToken $token) {
    return new PhpCssAstSelectorSimpleClass(substr($token->content, 1));
  }
}

