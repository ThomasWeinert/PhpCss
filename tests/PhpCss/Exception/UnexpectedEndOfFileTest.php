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
PhpCssTestCase::includePhpCssFile('/Exception/UnexpectedEndOfFile.php');

/**
* Test class for PhpCssParser.
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssExceptionUnexpectedEndOfFileTest extends PhpCssTestCase {

  /**
  * @covers PhpCssExceptionUnexpectedEndOfFile::__construct
  */
  public function testConstructor() {
    $e = new PhpCssExceptionUnexpectedEndOfFile(
      array(PhpCssScannerToken::STRING_CHARACTERS)
    );
    $this->assertAttributeEquals(
      array(PhpCssScannerToken::STRING_CHARACTERS), 'expectedTokens', $e
    );
    $this->assertEquals(
      'Parse error: Unexpected end of file was found while one of STRING_CHARACTERS was expected.',
      $e->getMessage()
    );
  }
}