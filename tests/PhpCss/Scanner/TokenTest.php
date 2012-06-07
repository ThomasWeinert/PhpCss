<?php
/**
* Collection of test for the PhpCssScannerToken class
*
* @version $Id: TokenTest.php 429 2010-03-29 08:05:32Z subjective $
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2010 Bastian Feder, Thomas Weinert
*
* @package PhpCss
* @subpackage Tests
*/

/**
* Load necessary files
*/
require_once(dirname(__FILE__).'/../TestCase.php');

/**
* Test class for PhpCssScannerToken.
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssScannerTokenTest extends PhpCssTestCase {

	/**
  * @covers PhpCssScannerToken::__construct
  * @covers PhpCssScannerToken::__get
	*/
  public function testAttributeTypeRead() {
    $token = new PhpCssScannerToken(PhpCssScannerToken::STRING_CHARACTERS, 'hello', 42);
    $this->assertEquals(
      PhpCssScannerToken::STRING_CHARACTERS,
      $token->type
    );
  }

  /**
  * @covers PhpCssScannerToken::__construct
  * @covers PhpCssScannerToken::__get
  */
  public function testAttributeContentRead() {
    $token = new PhpCssScannerToken(PhpCssScannerToken::STRING_CHARACTERS, 'hello', 42);
    $this->assertEquals(
      'hello',
      $token->content
    );
  }

  /**
  * @covers PhpCssScannerToken::__construct
  * @covers PhpCssScannerToken::__get
  */
  public function testAttributeLengthRead() {
    $token = new PhpCssScannerToken(PhpCssScannerToken::STRING_CHARACTERS, 'hello', 42);
    $this->assertEquals(
      5,
      $token->length
    );
  }


  /**
  * @covers PhpCssScannerToken::__construct
  * @covers PhpCssScannerToken::__get
  */
  public function testAttributePositionRead() {
    $token = new PhpCssScannerToken(PhpCssScannerToken::STRING_CHARACTERS, 'hello', 42);
    $this->assertEquals(
      42,
      $token->position
    );
  }

  /**
  * @covers PhpCssScannerToken::__construct
  * @covers PhpCssScannerToken::__get
  */
  public function testAttributeInvalidReadExpectingException() {
    $token = new PhpCssScannerToken(PhpCssScannerToken::STRING_CHARACTERS, 'hello', 42);
    try {
      $dummy = $token->invalidAttribute;
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $expected) {
    }
  }

  /**
  * @covers PhpCssScannerToken::__set
  */
  public function testAttributeWriteExpectingException() {
    $token = new PhpCssScannerToken(PhpCssScannerToken::STRING_CHARACTERS, 'hello', 42);
    try {
      $token->anyAttribute = 'fail';
      $this->fail('An expected exception has not been raised.');
    } catch (BadMethodCallException $expected) {
    }
  }

  /**
  * @covers PhpCssScannerToken::__toString
  * @covers PhpCssScannerToken::quoteContent
  */
  public function testToString() {
    $token = new PhpCssScannerToken(PhpCssScannerToken::STRING_CHARACTERS, 'hello', 42);
    $this->assertEquals(
      "TOKEN::STRING_CHARACTERS @42 'hello'",
      (string)$token
    );
  }

  /**
  * @covers PhpCssScannerToken::typeToString
  */
  public function testTypeToString() {
    $this->assertEquals(
      'STRING_CHARACTERS',
      PhpCssScannerToken::typeToString(PhpCssScannerToken::STRING_CHARACTERS)
    );
  }
}