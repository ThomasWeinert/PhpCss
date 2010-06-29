<?php

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class PhpCssParserMockDelegate extends PhpCssParserMock {

  public function parse() {
    return 'Delegated!';
  }

}