<?php
/**
* Collection of tests for the Scanner class
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
      // CSS 3 specification
      array(
        '*',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 '*'"
        )
      ),
      array(
        'E',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'"
        )
      ),
      // CSS 3 specification - attributes
      array(
        'E[foo]',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::SIMPLESELECTOR_ATTRIBUTE_START @1 '['",
          "TOKEN::STRING_CHARACTERS @2 'foo'",
          "TOKEN::SIMPLESELECTOR_ATTRIBUTE_END @5 ']'"
        )
      ),
      array(
        'E[foo="bar"]',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::SIMPLESELECTOR_ATTRIBUTE_START @1 '['",
          "TOKEN::STRING_CHARACTERS @2 'foo'",
          "TOKEN::SIMPLESELECTOR_ATTRIBUTE_OPERATOR @5 '='",
          "TOKEN::STRING_DOUBLE_QUOTE_START @6 '\"'",
          "TOKEN::STRING_CHARACTERS @7 'bar'",
          "TOKEN::STRING_DOUBLE_QUOTE_END @10 '\"'",
          "TOKEN::SIMPLESELECTOR_ATTRIBUTE_END @11 ']'"
        )
      ),
      array(
        'E[foo~="bar"]',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::SIMPLESELECTOR_ATTRIBUTE_START @1 '['",
          "TOKEN::STRING_CHARACTERS @2 'foo'",
          "TOKEN::SIMPLESELECTOR_ATTRIBUTE_OPERATOR @5 '~='",
          "TOKEN::STRING_DOUBLE_QUOTE_START @7 '\"'",
          "TOKEN::STRING_CHARACTERS @8 'bar'",
          "TOKEN::STRING_DOUBLE_QUOTE_END @11 '\"'",
          "TOKEN::SIMPLESELECTOR_ATTRIBUTE_END @12 ']'"
        )
      ),
      array(
        'E[foo^="bar"]',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::SIMPLESELECTOR_ATTRIBUTE_START @1 '['",
          "TOKEN::STRING_CHARACTERS @2 'foo'",
          "TOKEN::SIMPLESELECTOR_ATTRIBUTE_OPERATOR @5 '^='",
          "TOKEN::STRING_DOUBLE_QUOTE_START @7 '\"'",
          "TOKEN::STRING_CHARACTERS @8 'bar'",
          "TOKEN::STRING_DOUBLE_QUOTE_END @11 '\"'",
          "TOKEN::SIMPLESELECTOR_ATTRIBUTE_END @12 ']'"
        )
      ),
      array(
        'E[foo$="bar"]',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::SIMPLESELECTOR_ATTRIBUTE_START @1 '['",
          "TOKEN::STRING_CHARACTERS @2 'foo'",
          "TOKEN::SIMPLESELECTOR_ATTRIBUTE_OPERATOR @5 '$='",
          "TOKEN::STRING_DOUBLE_QUOTE_START @7 '\"'",
          "TOKEN::STRING_CHARACTERS @8 'bar'",
          "TOKEN::STRING_DOUBLE_QUOTE_END @11 '\"'",
          "TOKEN::SIMPLESELECTOR_ATTRIBUTE_END @12 ']'"
        )
      ),
      array(
        'E[foo*="bar"]',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::SIMPLESELECTOR_ATTRIBUTE_START @1 '['",
          "TOKEN::STRING_CHARACTERS @2 'foo'",
          "TOKEN::SIMPLESELECTOR_ATTRIBUTE_OPERATOR @5 '*='",
          "TOKEN::STRING_DOUBLE_QUOTE_START @7 '\"'",
          "TOKEN::STRING_CHARACTERS @8 'bar'",
          "TOKEN::STRING_DOUBLE_QUOTE_END @11 '\"'",
          "TOKEN::SIMPLESELECTOR_ATTRIBUTE_END @12 ']'"
        )
      ),
      array(
        'E[foo|="bar"]',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::SIMPLESELECTOR_ATTRIBUTE_START @1 '['",
          "TOKEN::STRING_CHARACTERS @2 'foo'",
          "TOKEN::SIMPLESELECTOR_ATTRIBUTE_OPERATOR @5 '|='",
          "TOKEN::STRING_DOUBLE_QUOTE_START @7 '\"'",
          "TOKEN::STRING_CHARACTERS @8 'bar'",
          "TOKEN::STRING_DOUBLE_QUOTE_END @11 '\"'",
          "TOKEN::SIMPLESELECTOR_ATTRIBUTE_END @12 ']'"
        )
      ),
      // CSS 3 specification - structural pseudo classes
      array(
        'E:root',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOCLASS @1 ':root'"
        )
      ),
      array(
        'E:nth-child(42)',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOCLASS @1 ':nth-child'",
          "TOKEN::PARENTHESES_START @11 '('",
          "TOKEN::NUMBER @12 '42'",
          "TOKEN::PARENTHESES_END @14 ')'",
        )
      ),
      array(
        'E:nth-last-child(42)',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOCLASS @1 ':nth-last-child'",
          "TOKEN::PARENTHESES_START @16 '('",
          "TOKEN::NUMBER @17 '42'",
          "TOKEN::PARENTHESES_END @19 ')'",
        )
      ),
      array(
        'E:nth-of-type(42)',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOCLASS @1 ':nth-of-type'",
          "TOKEN::PARENTHESES_START @13 '('",
          "TOKEN::NUMBER @14 '42'",
          "TOKEN::PARENTHESES_END @16 ')'",
        )
      ),
      array(
        'E:nth-last-of-type(42)',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOCLASS @1 ':nth-last-of-type'",
          "TOKEN::PARENTHESES_START @18 '('",
          "TOKEN::NUMBER @19 '42'",
          "TOKEN::PARENTHESES_END @21 ')'",
        )
      ),
      array(
        'E:first-child',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOCLASS @1 ':first-child'"
        )
      ),
      array(
        'E:last-child',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOCLASS @1 ':last-child'"
        )
      ),
      array(
        'E:first-of-type',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOCLASS @1 ':first-of-type'"
        )
      ),
      array(
        'E:last-of-type',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOCLASS @1 ':last-of-type'"
        )
      ),
      array(
        'E:only-child',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOCLASS @1 ':only-child'"
        )
      ),
      array(
        'E:only-of-type',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOCLASS @1 ':only-of-type'"
        )
      ),
      array(
        'E:empty',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOCLASS @1 ':empty'"
        )
      ),
      // CSS 3 specification - link pseudo classes
      array(
        'E:link',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOCLASS @1 ':link'"
        )
      ),
      array(
        'E:visited',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOCLASS @1 ':visited'"
        )
      ),
      // CSS 3 specification - user action pseudo classes
      array(
        'E:active',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOCLASS @1 ':active'"
        )
      ),
      array(
        'E:hover',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOCLASS @1 ':hover'"
        )
      ),
      array(
        'E:focus',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOCLASS @1 ':focus'"
        )
      ),
      // CSS 3 specification - target pseudo class
      array(
        'E:target',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOCLASS @1 ':target'"
        )
      ),
      // CSS 3 specification - language pseudo class
      array(
        'E:lang(fr)',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOCLASS @1 ':lang'",
          "TOKEN::PARENTHESES_START @6 '('",
          "TOKEN::SIMPLESELECTOR_TYPE @7 'fr'",
          "TOKEN::PARENTHESES_END @9 ')'",
        )
      ),
      // CSS 3 specification - ui element states pseudo classes
      array(
        'E:enabled',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOCLASS @1 ':enabled'"
        )
      ),
      array(
        'E:disabled',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOCLASS @1 ':disabled'"
        )
      ),
      array(
        'E:checked',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOCLASS @1 ':checked'"
        )
      ),
      // CSS 3 specification - pseudo elements
      array(
        'E::first-line',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOELEMENT @1 '::first-line'",
        )
      ),
      array(
        'E::first-letter',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOELEMENT @1 '::first-letter'",
        )
      ),
      array(
        'E::before',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOELEMENT @1 '::before'",
        )
      ),
      array(
        'E::after',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOELEMENT @1 '::after'",
        )
      ),
      // CSS 3 specification - class selector
      array(
        'E.warning',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::SIMPLESELECTOR_CLASS @1 '.warning'"
        )
      ),
      // CSS 3 specification - id selector
      array(
        'E#myid',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::SIMPE_SELECTOR_ID @1 '#myid'"
        )
      ),
      // CSS 3 specification - negation pseudo class
      array(
        'E:not(s)',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::PSEUDOCLASS @1 ':not'",
          "TOKEN::PARENTHESES_START @5 '('",
          "TOKEN::SIMPLESELECTOR_TYPE @6 's'",
          "TOKEN::PARENTHESES_END @7 ')'",
        )
      ),
      // CSS 3 specification - combinators
      array(
        'E F',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::WHITESPACE @1 ' '",
          "TOKEN::SIMPLESELECTOR_TYPE @2 'F'"
        )
      ),
      array(
        'E > F',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::SELECTOR_COMBINATOR @1 ' > '",
          "TOKEN::SIMPLESELECTOR_TYPE @4 'F'"
        )
      ),
      array(
        'E + F',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::SELECTOR_COMBINATOR @1 ' + '",
          "TOKEN::SIMPLESELECTOR_TYPE @4 'F'"
        )
      ),
      array(
        'E ~ F',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'E'",
          "TOKEN::SELECTOR_COMBINATOR @1 ' ~ '",
          "TOKEN::SIMPLESELECTOR_TYPE @4 'F'"
        )
      ),

      // individual
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
        'div#id.class1.class2:not(.title)',
        array(
          "TOKEN::SIMPLESELECTOR_TYPE @0 'div'",
          "TOKEN::SIMPE_SELECTOR_ID @3 '#id'",
          "TOKEN::SIMPLESELECTOR_CLASS @6 '.class1'",
          "TOKEN::SIMPLESELECTOR_CLASS @13 '.class2'",
          "TOKEN::PSEUDOCLASS @20 ':not'",
          "TOKEN::PARENTHESES_START @24 '('",
          "TOKEN::SIMPLESELECTOR_CLASS @25 '.title'",
          "TOKEN::PARENTHESES_END @31 ')'"
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