<?php
/**
* Collection of tests for the Parser class
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2012 PhpCss Team
*
* @package PhpCss
* @subpackage Tests
*/

/**
* Load necessary files
*/
require_once(dirname(__FILE__).'/../TestCase.php');

PhpCssTestCase::includePhpCssFile('/Exception.php');
PhpCssTestCase::includePhpCssFile('/Exception/TokenMismatch.php');

/**
* Test class for PhpCssParser.
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssExceptionTokenMismatchTest extends PhpCssTestCase {

  /**
  * @covers PhpCssExceptionTokenMismatch::__construct
  */
  public function testConstructor() {
    $expectedToken = new PhpCssScannerToken(
      PhpCssScannerToken::TYPE_SELECTOR, 'sample', 42
    );
    $e = new PhpCssExceptionTokenMismatch(
      $expectedToken, array(PhpCssScannerToken::STRING_CHARACTERS)
    );
    $this->assertAttributeEquals(
      $expectedToken, 'encounteredToken', $e
    );
    $this->assertAttributeEquals(
      array(PhpCssScannerToken::STRING_CHARACTERS), 'expectedTokens', $e
    );
    $this->assertEquals(
      'Parse error: Found TOKEN::SIMPLESELECTOR_TYPE @42 \'sample\' while one of STRING_CHARACTERS was expected.',
      $e->getMessage()
    );
  }
}