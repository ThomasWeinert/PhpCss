<?php
/**
* Collection of tests for the Scanner class
*
* @version $Id: ScannerTest.php 430 2010-03-29 15:53:43Z subjective $
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2010 Bastian Feder, Thomas Weinert
*
* @package PhpCss
* @subpackage Tests
*/

/**
* Load necessary files
*/
require_once(dirname(__FILE__).'/TestCase.php');
require_once(dirname(__FILE__).'/../../src/PhpCss/Scanner.php');

/**
* Test class for PhpCssScanner.
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssScannerTest extends PhpCssTestCase {

  /**
  * @covers PhpCssScanner::__construct
  */
  public function testConstructor() {
    $status = $this->getMock('PhpCssScannerStatus');
    $scanner = new PhpCssScanner($status);
    $this->assertAttributeSame(
      $status, '_status', $scanner
    );
  }

  /**
  * @covers PhpCssScanner::scan
  * @covers PhpCssScanner::_next
  */
  public function testScanWithSingleValidToken() {
    $token = $this->getTokenMockObjectFixture(6);
    $status = $this->getStatusMockObjectFixture(
      // getToken() returns this elements
      array($token, NULL),
      // isEndToken() returns FALSE
      FALSE
    );
    $status
      ->expects($this->once())
      ->method('getNewStatus')
      ->with($this->equalTo($token))
      ->will($this->returnValue(FALSE));

    $scanner = new PhpCssScanner($status);
    $tokens = array();
    $scanner->scan($tokens, 'SAMPLE');
    $this->assertEquals(
      array($token),
      $tokens
    );
  }

  /**
  * @covers PhpCssScanner::scan
  * @covers PhpCssScanner::_next
  */
  public function testScanWithEndToken() {
    $token = $this->getTokenMockObjectFixture(6);
    $status = $this->getStatusMockObjectFixture(
      // getToken() returns this elements
      array($token),
      // isEndToken() returns TRUE
      TRUE
    );

    $scanner = new PhpCssScanner($status);
    $tokens = array();
    $scanner->scan($tokens, 'SAMPLE');
    $this->assertEquals(
      array($token),
      $tokens
    );
  }

  /**
  * @covers PhpCssScanner::scan
  * @covers PhpCssScanner::_next
  */
  public function testScanWithInvalidToken() {
    $status = $this->getStatusMockObjectFixture(
      array(NULL) // getToken() returns this elements
    );
    $scanner = new PhpCssScanner($status);
    $tokens = array();
    try {
      $scanner->scan($tokens, 'SAMPLE');
      $this->fail('An expected exception has not been occured.');
    } catch (UnexpectedValueException $e) {
    }
  }

  /**
  * @covers PhpCssScanner::scan
  * @covers PhpCssScanner::_next
  * @covers PhpCssScanner::_delegate
  */
  public function testScanWithSubStatus() {
    $tokenOne = $this->getTokenMockObjectFixture(6);
    $tokenTwo = $this->getTokenMockObjectFixture(4);
    $subStatus = $this->getStatusMockObjectFixture(
      // getToken() returns this elements
      array($tokenTwo),
      // isEndToken() returns TRUE
      TRUE
    );
    $status = $this->getStatusMockObjectFixture(
      // getToken() returns this elements
      array($tokenOne, NULL),
      // isEndToken() returns FALSE
      FALSE
    );
    $status
      ->expects($this->once())
      ->method('getNewStatus')
      ->with($this->equalTo($tokenOne))
      ->will($this->returnValue($subStatus));

    $scanner = new PhpCssScanner($status);
    $tokens = array();
    $scanner->scan($tokens, 'SAMPLETEST');
    $this->assertEquals(
      array($tokenOne, $tokenTwo),
      $tokens
    );
  }


  /**
  * This is more an integration test, but it fits in here....
  * @covers stdClass
  * @dataProvider selectorsDataProvider
  */
  public function testScannerWithSelectors($string, $expected) {
    $scanner = new PhpCssScanner(new PhpCssScannerStatusSelector());
    $tokens = array();
    $scanner->scan($tokens, $string);
    $this->assertTokenListEqualsStringList(
      $expected,
      $tokens
    );
  }

  /*****************************
  * Data provider
  *****************************/

  public static function selectorsDataProvider() {
    return array(
      array(
        "test",
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'test'"
        )
      ),
      array(
        "test'string'",
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'test'",
          "TOKEN::STRING_SINGLE_QUOTE_START @4 '\''",
          "TOKEN::STRING_CHARACTERS @5 'string'",
          "TOKEN::STRING_SINGLE_QUOTE_END @11 '\''"
        )
      ),
      array(
        'div#id.class1.class2:has(span.title)',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'div'",
          "TOKEN::SIMPE_SELECTOR_ID @3 '#id'",
          "TOKEN::SIMPLESELECTOR_CLASS @6 '.class1'",
          "TOKEN::SIMPLESELECTOR_CLASS @13 '.class2'",
          "TOKEN::PSEUDOCLASS @20 ':has'",
          "TOKEN::PSEUDOCLASS_PARAMETERS_START @24 '('",
          "TOKEN::SIMPLESELECTOR_TYPE @25 'span'",
          "TOKEN::SIMPLESELECTOR_CLASS @29 '.title'",
          "TOKEN::PSEUDOCLASS_PARAMETERS_END @35 ')'"
        )
      ),
      array(
        "div > span",
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'div'",
          "TOKEN::SELECTOR_COMBINATOR @3 ' > '",
          "TOKEN::SIMPLESELECTOR_TYPE @6 'span'"
        )
      ),
      array(
        "div span",
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'div'",
          "TOKEN::WHITESPACE @3 ' '",
          "TOKEN::SIMPLESELECTOR_TYPE @4 'span'"
        )
      ),
    );
  }


  /*****************************
  * Individual assertions
  *****************************/

  public function assertTokenListEqualsStringList($expected, $tokens) {
    $string = array();
    foreach ($tokens as $token) {
      $strings[] = (string)$token;
    }
    $this->assertEquals(
      $expected,
      $strings
    );
  }

  /******************************
  * Fixtures
  ******************************/

  private function getTokenMockObjectFixture($length) {
    $token = $this->getMock('PhpCssScannerToken');
    $token
      ->expects($this->any())
      ->method('__get')
      ->will($this->returnValue($length));
    return $token;
  }

  private function getStatusMockObjectFixture($tokens, $isEndToken = NULL) {
    $status = $this->getMock('PhpCssScannerStatus');
    if (count($tokens) > 0) {
      $status
        ->expects($this->exactly(count($tokens)))
        ->method('getToken')
        ->with(
          $this->isType('string'),
          $this->isType('integer')
         )
        ->will(
          call_user_func_array(
            array($this, 'onConsecutiveCalls'),
            $tokens
          )
        );
    }
    if (!is_null($isEndToken)) {
      $status
        ->expects($this->any())
        ->method('isEndToken')
        ->with($this->isInstanceOf('PhpCssScannerToken'))
        ->will($this->returnValue($isEndToken));
    }
    return $status;
  }
}