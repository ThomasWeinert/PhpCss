<?php

class PhpCssParserDefault extends PhpCssParser {

  private $_expectedTokens = array(
    PhpCssScannerToken::WHITESPACE,
    PhpCssScannerToken::TYPE_SELECTOR,
    PhpCssScannerToken::ID_SELECTOR,
    PhpCssScannerToken::CLASS_SELECTOR,
    PhpCssScannerToken::PSEUDO_CLASS,
    PhpCssScannerToken::ATTRIBUTE_SELECTOR_START
  );

  public function parse() {
    $list = $this->createSequenceList();
    while (!$this->endOfTokens()) {
      $currentToken = $this->lookahead($this->_expectedTokens);
      if ($currentToken->type == phpCssScannerToken::WHITESPACE) {
        $this->read(phpCssScannerToken::WHITESPACE);
        continue;
      }
      $list[] = $this->delegate('PhpCssParserSequence');
    }
    return $list;
  }

  private function createSequenceList() {
    return new PhpCssAstSelectorSequenceList();
  }
}
