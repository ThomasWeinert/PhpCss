<?php


class PhpCssParserMockDelegate extends PhpCssParserMock {

  public function parse() {
    return 'Delegated!';
  }

}