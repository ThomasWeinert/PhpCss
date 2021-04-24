<?php
namespace PhpCss {

  require_once(__DIR__.'/../bootstrap.php');

  class ScannerTest extends \PHPUnit\Framework\TestCase {

    /**
    * @covers \PhpCss\Scanner::__construct
    */
    public function testConstructor() {
      $status = $this->createMock(Scanner\Status::CLASS);
      /**
       * @var Scanner\Status $status
       */
      $scanner = new Scanner($status);
      $this->assertSame(
        $status, $scanner->getStatus()
      );
    }

    /**
    * @covers \PhpCss\Scanner::scan
    * @covers \PhpCss\Scanner::_next
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

      $scanner = new Scanner($status);
      $tokens = array();
      $scanner->scan($tokens, 'SAMPLE');
      $this->assertEquals(
        array($token),
        $tokens
      );
    }

    /**
    * @covers \PhpCss\Scanner::scan
    * @covers \PhpCss\Scanner::_next
    */
    public function testScanWithEndToken() {
      $token = $this->getTokenMockObjectFixture(6);
      $status = $this->getStatusMockObjectFixture(
        // getToken() returns this elements
        array($token),
        // isEndToken() returns TRUE
        TRUE
      );

      $scanner = new Scanner($status);
      $tokens = array();
      $scanner->scan($tokens, 'SAMPLE');
      $this->assertEquals(
        array($token),
        $tokens
      );
    }

    /**
    * @covers \PhpCss\Scanner::scan
    * @covers \PhpCss\Scanner::_next
    */
    public function testScanWithInvalidToken() {
      $status = $this->getStatusMockObjectFixture(
        array(NULL) // getToken() returns this elements
      );
      $scanner = new Scanner($status);
      $tokens = array();
      $this->expectException(
        Exception\InvalidCharacterException::CLASS,
        'Invalid char "S" for status "Mock_PhpCssScannerStatus" at offset #0 in "SAMPLE"'
      );
      $scanner->scan($tokens, 'SAMPLE');
    }

    /**
    * @covers \PhpCss\Scanner::scan
    * @covers \PhpCss\Scanner::_next
    */
    public function testScanWithInvalidTokenUnicode() {
      $status = $this->getStatusMockObjectFixture(
        array(NULL) // getToken() returns this elements
      );
      $scanner = new Scanner($status);
      $tokens = array();
      $this->expectException(
        Exception\InvalidCharacterException::CLASS,
        'Invalid char "Ä" for status "Mock_PhpCssScannerStatus" at offset #0 in "ÄÖÜ"'
      );
      $scanner->scan($tokens, 'ÄÖÜ');
    }

    /**
    * @covers \PhpCss\Scanner::scan
    * @covers \PhpCss\Scanner::_next
    * @covers \PhpCss\Scanner::_delegate
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

      $scanner = new Scanner($status);
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
      $scanner = new Scanner(new Scanner\Status\Selector());
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
        // Unicode
        array(
          'äää.ööö',
          array(
            "TOKEN::IDENTIFIER @0 'äää'",
            "TOKEN::SIMPLESELECTOR_CLASS @6 '.ööö'"
          )
        ),
        // CSS 3 specification
        array(
          '*',
          array(
            "TOKEN::IDENTIFIER @0 '*'"
          )
        ),
        array(
          'E',
          array(
            "TOKEN::IDENTIFIER @0 'E'"
          )
        ),
        // CSS 3 specification - attributes
        array(
          'E[foo]',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_START @1 '['",
            "TOKEN::IDENTIFIER @2 'foo'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_END @5 ']'"
          )
        ),
        array(
          'E[foo="bar"]',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_START @1 '['",
            "TOKEN::IDENTIFIER @2 'foo'",
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
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_START @1 '['",
            "TOKEN::IDENTIFIER @2 'foo'",
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
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_START @1 '['",
            "TOKEN::IDENTIFIER @2 'foo'",
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
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_START @1 '['",
            "TOKEN::IDENTIFIER @2 'foo'",
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
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_START @1 '['",
            "TOKEN::IDENTIFIER @2 'foo'",
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
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_START @1 '['",
            "TOKEN::IDENTIFIER @2 'foo'",
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
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':root'"
          )
        ),
        array(
          'E:nth-child(42)',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':nth-child'",
            "TOKEN::PARENTHESES_START @11 '('",
            "TOKEN::NUMBER @12 '42'",
            "TOKEN::PARENTHESES_END @14 ')'",
          )
        ),
        array(
          'E:nth-last-child(42)',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':nth-last-child'",
            "TOKEN::PARENTHESES_START @16 '('",
            "TOKEN::NUMBER @17 '42'",
            "TOKEN::PARENTHESES_END @19 ')'",
          )
        ),
        array(
          'E:nth-of-type(42)',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':nth-of-type'",
            "TOKEN::PARENTHESES_START @13 '('",
            "TOKEN::NUMBER @14 '42'",
            "TOKEN::PARENTHESES_END @16 ')'",
          )
        ),
        array(
          'E:nth-last-of-type(42)',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':nth-last-of-type'",
            "TOKEN::PARENTHESES_START @18 '('",
            "TOKEN::NUMBER @19 '42'",
            "TOKEN::PARENTHESES_END @21 ')'",
          )
        ),
        array(
          'E:first-child',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':first-child'"
          )
        ),
        array(
          'E:last-child',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':last-child'"
          )
        ),
        array(
          'E:first-of-type',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':first-of-type'"
          )
        ),
        array(
          'E:last-of-type',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':last-of-type'"
          )
        ),
        array(
          'E:only-child',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':only-child'"
          )
        ),
        array(
          'E:only-of-type',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':only-of-type'"
          )
        ),
        array(
          'E:empty',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':empty'"
          )
        ),
        // CSS 3 specification - link pseudo classes
        array(
          'E:link',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':link'"
          )
        ),
        array(
          'E:visited',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':visited'"
          )
        ),
        // CSS 3 specification - user action pseudo classes
        array(
          'E:active',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':active'"
          )
        ),
        array(
          'E:hover',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':hover'"
          )
        ),
        array(
          'E:focus',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':focus'"
          )
        ),
        // CSS 3 specification - target pseudo class
        array(
          'E:target',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':target'"
          )
        ),
        // CSS 3 specification - language pseudo class
        array(
          'E:lang(fr)',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':lang'",
            "TOKEN::PARENTHESES_START @6 '('",
            "TOKEN::IDENTIFIER @7 'fr'",
            "TOKEN::PARENTHESES_END @9 ')'",
          )
        ),
        // CSS 3 specification - ui element states pseudo classes
        array(
          'E:enabled',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':enabled'"
          )
        ),
        array(
          'E:disabled',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':disabled'"
          )
        ),
        array(
          'E:checked',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':checked'"
          )
        ),
        // CSS 3 specification - pseudo elements
        array(
          'E::first-line',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOELEMENT @1 '::first-line'",
          )
        ),
        array(
          'E::first-letter',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOELEMENT @1 '::first-letter'",
          )
        ),
        array(
          'E::before',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOELEMENT @1 '::before'",
          )
        ),
        array(
          'E::after',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOELEMENT @1 '::after'",
          )
        ),
        // CSS 3 specification - class selector
        array(
          'E.warning',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::SIMPLESELECTOR_CLASS @1 '.warning'"
          )
        ),
        // CSS 3 specification - id selector
        array(
          'E#myid',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::SIMPLE_SELECTOR_ID @1 '#myid'"
          )
        ),
        // CSS 3 specification - negation pseudo class
        array(
          'E:not(s)',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':not'",
            "TOKEN::PARENTHESES_START @5 '('",
            "TOKEN::IDENTIFIER @6 's'",
            "TOKEN::PARENTHESES_END @7 ')'",
          )
        ),
        // CSS 3 specification - combinators
        array(
          'E F',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::WHITESPACE @1 ' '",
            "TOKEN::IDENTIFIER @2 'F'"
          )
        ),
        array(
          'E > F',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::SELECTOR_COMBINATOR @1 ' > '",
            "TOKEN::IDENTIFIER @4 'F'"
          )
        ),
        array(
          'E + F',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::SELECTOR_COMBINATOR @1 ' + '",
            "TOKEN::IDENTIFIER @4 'F'"
          )
        ),
        array(
          'E ~ F',
          array(
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::SELECTOR_COMBINATOR @1 ' ~ '",
            "TOKEN::IDENTIFIER @4 'F'"
          )
        ),
        // CSS 3 specification - nth parameters
        array(
          "tr:nth-child(2n+1)",
          array(
            "TOKEN::IDENTIFIER @0 'tr'",
            "TOKEN::PSEUDOCLASS @2 ':nth-child'",
            "TOKEN::PARENTHESES_START @12 '('",
            "TOKEN::PSEUDOCLASS_POSITION @13 '2n+1'",
            "TOKEN::PARENTHESES_END @17 ')'"
          )
        ),
        array(
          "tr:nth-child(odd)",
          array(
            "TOKEN::IDENTIFIER @0 'tr'",
            "TOKEN::PSEUDOCLASS @2 ':nth-child'",
            "TOKEN::PARENTHESES_START @12 '('",
            "TOKEN::IDENTIFIER @13 'odd'",
            "TOKEN::PARENTHESES_END @16 ')'"
          )
        ),
        array(
          "tr:nth-child(2n+0)",
          array(
            "TOKEN::IDENTIFIER @0 'tr'",
            "TOKEN::PSEUDOCLASS @2 ':nth-child'",
            "TOKEN::PARENTHESES_START @12 '('",
            "TOKEN::PSEUDOCLASS_POSITION @13 '2n+0'",
            "TOKEN::PARENTHESES_END @17 ')'"
          )
        ),
        array(
          "tr:nth-child(even)",
          array(
            "TOKEN::IDENTIFIER @0 'tr'",
            "TOKEN::PSEUDOCLASS @2 ':nth-child'",
            "TOKEN::PARENTHESES_START @12 '('",
            "TOKEN::IDENTIFIER @13 'even'",
            "TOKEN::PARENTHESES_END @17 ')'"
          )
        ),
        array(
          "tr:nth-child(10n-1)",
          array(
            "TOKEN::IDENTIFIER @0 'tr'",
            "TOKEN::PSEUDOCLASS @2 ':nth-child'",
            "TOKEN::PARENTHESES_START @12 '('",
            "TOKEN::PSEUDOCLASS_POSITION @13 '10n-1'",
            "TOKEN::PARENTHESES_END @18 ')'"
          )
        ),
        array(
          "tr:nth-child(10n+9)",
          array(
            "TOKEN::IDENTIFIER @0 'tr'",
            "TOKEN::PSEUDOCLASS @2 ':nth-child'",
            "TOKEN::PARENTHESES_START @12 '('",
            "TOKEN::PSEUDOCLASS_POSITION @13 '10n+9'",
            "TOKEN::PARENTHESES_END @18 ')'"
          )
        ),

        // individual
        array(
          "test",
          array(
            "TOKEN::IDENTIFIER @0 'test'"
          )
        ),
        array(
          "test'string'",
          array(
            "TOKEN::IDENTIFIER @0 'test'",
            "TOKEN::STRING_SINGLE_QUOTE_START @4 '\\''",
            "TOKEN::STRING_CHARACTERS @5 'string'",
            "TOKEN::STRING_SINGLE_QUOTE_END @11 '\\''"
          )
        ),
        array(
          'div#id.class1.class2:not(.title)',
          array(
            "TOKEN::IDENTIFIER @0 'div'",
            "TOKEN::SIMPLE_SELECTOR_ID @3 '#id'",
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
            "TOKEN::IDENTIFIER @0 'div'",
            "TOKEN::SELECTOR_COMBINATOR @3 ' > '",
            "TOKEN::IDENTIFIER @6 'span'"
          )
        ),
        array(
          "div span",
          array(
            "TOKEN::IDENTIFIER @0 'div'",
            "TOKEN::WHITESPACE @3 ' '",
            "TOKEN::IDENTIFIER @4 'span'"
          )
        ),
        array(
          ':nth-child(3n)',
          array(
            "TOKEN::PSEUDOCLASS @0 ':nth-child'",
            "TOKEN::PARENTHESES_START @10 '('",
            "TOKEN::PSEUDOCLASS_POSITION @11 '3n'",
            "TOKEN::PARENTHESES_END @13 ')'"
          )
        ),
        array(
          ':nth-child(10n-1)',
          array(
            "TOKEN::PSEUDOCLASS @0 ':nth-child'",
            "TOKEN::PARENTHESES_START @10 '('",
            "TOKEN::PSEUDOCLASS_POSITION @11 '10n-1'",
            "TOKEN::PARENTHESES_END @16 ')'"
          )
        ),
        array(
          ':nth-child( -n+ 6)',
          array(
            "TOKEN::PSEUDOCLASS @0 ':nth-child'",
            "TOKEN::PARENTHESES_START @10 '('",
            "TOKEN::PSEUDOCLASS_POSITION @11 ' -n+ 6'",
            "TOKEN::PARENTHESES_END @17 ')'"
          )
        ),
        array(
          ':nth-child( +3n - 2 )',
          array(
            "TOKEN::PSEUDOCLASS @0 ':nth-child'",
            "TOKEN::PARENTHESES_START @10 '('",
            "TOKEN::PSEUDOCLASS_POSITION @11 ' +3n - 2 '",
            "TOKEN::PARENTHESES_END @20 ')'"
          )
        ),
        array(
          '> p',
          array(
            "TOKEN::SELECTOR_COMBINATOR @0 '> '",
            "TOKEN::IDENTIFIER @2 'p'"
          )
        ),
        array(
          ' + p',
          array(
            "TOKEN::SELECTOR_COMBINATOR @0 ' + '",
            "TOKEN::IDENTIFIER @3 'p'"
          )
        ),
        array(
          'p:not(:scope)',
          array(
            "TOKEN::IDENTIFIER @0 'p'",
            "TOKEN::PSEUDOCLASS @1 ':not'",
            "TOKEN::PARENTHESES_START @5 '('",
            "TOKEN::PSEUDOCLASS @6 ':scope'",
            "TOKEN::PARENTHESES_END @12 ')'"
          )
        ),
        array(
          'div:contains("text")',
          array(
            "TOKEN::IDENTIFIER @0 'div'",
            "TOKEN::PSEUDOCLASS @3 ':contains'",
            "TOKEN::PARENTHESES_START @12 '('",
            "TOKEN::STRING_DOUBLE_QUOTE_START @13 '\"'",
            "TOKEN::STRING_CHARACTERS @14 'text'",
            "TOKEN::STRING_DOUBLE_QUOTE_END @18 '\"'",
            "TOKEN::PARENTHESES_END @19 ')'"
          )
        ),
        array(
          'div:gt(-2)',
          array(
            "TOKEN::IDENTIFIER @0 'div'",
            "TOKEN::PSEUDOCLASS @3 ':gt'",
            "TOKEN::PARENTHESES_START @6 '('",
            "TOKEN::NUMBER @7 '-2'",
            "TOKEN::PARENTHESES_END @9 ')'"
          )
        )
      );
    }


    /*****************************
    * Individual assertions
    *****************************/

    public function assertTokenListEqualsStringList($expected, $tokens) {
      $strings = array();
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

    /**
     * @param integer $length
     * @return Scanner\Token|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getTokenMockObjectFixture($length) {
      $token = $this->createMock(Scanner\Token::CLASS);
      $token
        ->expects($this->any())
        ->method('__get')
        ->will($this->returnValue($length));
      return $token;
    }

    /**
     * @param array $tokens
     * @param null $isEndToken
     * @return Scanner\Status|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getStatusMockObjectFixture($tokens, $isEndToken = NULL) {
      $status = $this
        ->getMockBuilder(Scanner\Status::CLASS)
        ->setMockClassName('Mock_PhpCssScannerStatus')
        ->getMock();
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
          ->with($this->isInstanceOf(Scanner\Token::CLASS))
          ->will($this->returnValue($isEndToken));
      }
      return $status;
    }
  }
}
