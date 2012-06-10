<?php
/**
* Proxy class, allowing testing of the abstract PhpCssParser class
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2012 PhpCss Team
*/


/**
* Proxy class, allowing testing of the abstract PhpCssParser class
*/
class PhpCssParserMock extends PhpCssParser {

  public $_tokens;

  public function parse() {
    // Nothing to do here
  }

  /**
  * This function can be made public for testing because it is supposed to
  * be used by every subparser and we just expose it to be able to call it
  * during test without different mocks.
  */
  public function read($expectedTokens) {
    return parent::read($expectedTokens);
  }

  /**
  * This function can be made public for testing because it is supposed to
  * be used by every subparser and we just expose it to be able to call it
  * during test without different mocks.
  */
  public function lookahead($expectedTokens, $position = 0, $allowEndOfTokens = false) {
    return parent::lookahead($expectedTokens, $position, $allowEndOfTokens);
  }

  /**
  * This function can be made public for testing because it is supposed to
  * be used by every subparser and we just expose it to be able to call it
  * during test without different mocks.
  */
  public function endOfTokens($position = 0) {
    return parent::endOfTokens($position);
  }

  /**
  * This function can be made public for testing because it is supposed to
  * be used by every subparser and we just expose it to be able to call it
  * during test without different mocks.
  */
  public function ignore($expectedTokens) {
    return parent::ignore($expectedTokens);
  }

  /**
   * This function can be made public for testing because it is supposed to
   * be used by every subparser and we just expose it to be able to call it
   * during test without different mocks.
   */
  public function delegate($subparser) {
    return parent::delegate($subparser);
  }
}