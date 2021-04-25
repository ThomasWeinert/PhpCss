<?php

namespace PhpCss {

  use PHPUnit\Framework\MockObject\MockObject;
  use PHPUnit\Framework\TestCase;

  require_once(__DIR__.'/../bootstrap.php');

  class ScannerTest extends TestCase {

    /**
     * @covers \PhpCss\Scanner::__construct
     */
    public function testConstructor(): void {
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
    public function testScanWithSingleValidToken(): void {
      $token = $this->getTokenMockObjectFixture(6);
      $status = $this->getStatusMockObjectFixture(
      // getToken() returns this elements
        [$token, NULL],
        // isEndToken() returns FALSE
        FALSE
      );
      $status
        ->expects($this->once())
        ->method('getNewStatus')
        ->with($this->equalTo($token))
        ->willReturn(NULL);

      $scanner = new Scanner($status);
      $tokens = [];
      $scanner->scan($tokens, 'SAMPLE');
      $this->assertEquals(
        [$token],
        $tokens
      );
    }

    /**
     * @covers \PhpCss\Scanner::scan
     * @covers \PhpCss\Scanner::_next
     */
    public function testScanWithEndToken(): void {
      $token = $this->getTokenMockObjectFixture(6);
      $status = $this->getStatusMockObjectFixture(
      // getToken() returns this elements
        [$token],
        // isEndToken() returns TRUE
        TRUE
      );

      $scanner = new Scanner($status);
      $tokens = [];
      $scanner->scan($tokens, 'SAMPLE');
      $this->assertEquals(
        [$token],
        $tokens
      );
    }

    /**
     * @covers \PhpCss\Scanner::scan
     * @covers \PhpCss\Scanner::_next
     */
    public function testScanWithInvalidToken(): void {
      $status = $this->getStatusMockObjectFixture(
        [NULL] // getToken() returns this elements
      );
      $scanner = new Scanner($status);
      $tokens = [];
      $this->expectException(Exception\InvalidCharacterException::CLASS);
      $this->expectExceptionMessage(
        'Invalid char "S" for status "Mock_PhpCssScannerStatus" at offset #0 in "SAMPLE"'
      );
      $scanner->scan($tokens, 'SAMPLE');
    }

    /**
     * @covers \PhpCss\Scanner::scan
     * @covers \PhpCss\Scanner::_next
     */
    public function testScanWithInvalidTokenUnicode(): void {
      $status = $this->getStatusMockObjectFixture(
        [NULL] // getToken() returns this elements
      );
      $scanner = new Scanner($status);
      $tokens = [];
      $this->expectException(Exception\InvalidCharacterException::CLASS);
      $this->expectExceptionMessage(
        'Invalid char "Ä" for status "Mock_PhpCssScannerStatus" at offset #0 in "ÄÖÜ"'
      );
      $scanner->scan($tokens, 'ÄÖÜ');
    }

    /**
     * @covers \PhpCss\Scanner::scan
     * @covers \PhpCss\Scanner::_next
     * @covers \PhpCss\Scanner::_delegate
     */
    public function testScanWithSubStatus(): void {
      $tokenOne = $this->getTokenMockObjectFixture(6);
      $tokenTwo = $this->getTokenMockObjectFixture(1);
      $tokenThree = $this->getTokenMockObjectFixture(4);
      $subStatus = $this->getStatusMockObjectFixture(
      // getToken() returns elements
        [$tokenTwo, $tokenThree],
        // isEndToken() returns TRUE for last token
        TRUE
      );
      $status = $this->getStatusMockObjectFixture(
      // getToken() returns elements
        [$tokenOne, NULL],
        // isEndToken() returns FALSE
        FALSE
      );
      $status
        ->expects($this->once())
        ->method('getNewStatus')
        ->with($this->equalTo($tokenOne))
        ->willReturn($subStatus);

      $scanner = new Scanner($status);
      $tokens = [];
      $scanner->scan($tokens, 'SAMPLE_TEST');
      $this->assertEquals(
        [$tokenOne, $tokenTwo, $tokenThree],
        $tokens
      );
    }


    /**
     * This is more an integration test, but it fits in here....
     * @covers \stdClass
     * @dataProvider selectorsDataProvider
     */
    public function testScannerWithSelectors($string, $expected): void {
      $scanner = new Scanner(new Scanner\Status\Selector());
      $tokens = [];
      $scanner->scan($tokens, $string);
      $this->assertTokenListEqualsStringList(
        $expected,
        $tokens
      );
    }

    /*****************************
     * Data provider
     *****************************/

    public static function selectorsDataProvider(): array {
      return [
        // Unicode
        [
          'äää.ööö',
          [
            "TOKEN::IDENTIFIER @0 'äää'",
            "TOKEN::SIMPLESELECTOR_CLASS @6 '.ööö'",
          ],
        ],
        // CSS 3 specification
        [
          '*',
          [
            "TOKEN::IDENTIFIER @0 '*'",
          ],
        ],
        [
          'E',
          [
            "TOKEN::IDENTIFIER @0 'E'",
          ],
        ],
        // CSS 3 specification - attributes
        [
          'E[foo]',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_START @1 '['",
            "TOKEN::IDENTIFIER @2 'foo'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_END @5 ']'",
          ],
        ],
        [
          'E[foo="bar"]',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_START @1 '['",
            "TOKEN::IDENTIFIER @2 'foo'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_OPERATOR @5 '='",
            "TOKEN::STRING_DOUBLE_QUOTE_START @6 '\"'",
            "TOKEN::STRING_CHARACTERS @7 'bar'",
            "TOKEN::STRING_DOUBLE_QUOTE_END @10 '\"'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_END @11 ']'",
          ],
        ],
        [
          'E[foo~="bar"]',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_START @1 '['",
            "TOKEN::IDENTIFIER @2 'foo'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_OPERATOR @5 '~='",
            "TOKEN::STRING_DOUBLE_QUOTE_START @7 '\"'",
            "TOKEN::STRING_CHARACTERS @8 'bar'",
            "TOKEN::STRING_DOUBLE_QUOTE_END @11 '\"'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_END @12 ']'",
          ],
        ],
        [
          'E[foo^="bar"]',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_START @1 '['",
            "TOKEN::IDENTIFIER @2 'foo'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_OPERATOR @5 '^='",
            "TOKEN::STRING_DOUBLE_QUOTE_START @7 '\"'",
            "TOKEN::STRING_CHARACTERS @8 'bar'",
            "TOKEN::STRING_DOUBLE_QUOTE_END @11 '\"'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_END @12 ']'",
          ],
        ],
        [
          'E[foo$="bar"]',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_START @1 '['",
            "TOKEN::IDENTIFIER @2 'foo'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_OPERATOR @5 '$='",
            "TOKEN::STRING_DOUBLE_QUOTE_START @7 '\"'",
            "TOKEN::STRING_CHARACTERS @8 'bar'",
            "TOKEN::STRING_DOUBLE_QUOTE_END @11 '\"'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_END @12 ']'",
          ],
        ],
        [
          'E[foo*="bar"]',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_START @1 '['",
            "TOKEN::IDENTIFIER @2 'foo'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_OPERATOR @5 '*='",
            "TOKEN::STRING_DOUBLE_QUOTE_START @7 '\"'",
            "TOKEN::STRING_CHARACTERS @8 'bar'",
            "TOKEN::STRING_DOUBLE_QUOTE_END @11 '\"'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_END @12 ']'",
          ],
        ],
        [
          'E[foo|="bar"]',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_START @1 '['",
            "TOKEN::IDENTIFIER @2 'foo'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_OPERATOR @5 '|='",
            "TOKEN::STRING_DOUBLE_QUOTE_START @7 '\"'",
            "TOKEN::STRING_CHARACTERS @8 'bar'",
            "TOKEN::STRING_DOUBLE_QUOTE_END @11 '\"'",
            "TOKEN::SIMPLESELECTOR_ATTRIBUTE_END @12 ']'",
          ],
        ],
        // CSS 3 specification - structural pseudo classes
        [
          'E:root',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':root'",
          ],
        ],
        [
          'E:nth-child(42)',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':nth-child'",
            "TOKEN::PARENTHESES_START @11 '('",
            "TOKEN::NUMBER @12 '42'",
            "TOKEN::PARENTHESES_END @14 ')'",
          ],
        ],
        [
          'E:nth-last-child(42)',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':nth-last-child'",
            "TOKEN::PARENTHESES_START @16 '('",
            "TOKEN::NUMBER @17 '42'",
            "TOKEN::PARENTHESES_END @19 ')'",
          ],
        ],
        [
          'E:nth-of-type(42)',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':nth-of-type'",
            "TOKEN::PARENTHESES_START @13 '('",
            "TOKEN::NUMBER @14 '42'",
            "TOKEN::PARENTHESES_END @16 ')'",
          ],
        ],
        [
          'E:nth-last-of-type(42)',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':nth-last-of-type'",
            "TOKEN::PARENTHESES_START @18 '('",
            "TOKEN::NUMBER @19 '42'",
            "TOKEN::PARENTHESES_END @21 ')'",
          ],
        ],
        [
          'E:first-child',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':first-child'",
          ],
        ],
        [
          'E:last-child',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':last-child'",
          ],
        ],
        [
          'E:first-of-type',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':first-of-type'",
          ],
        ],
        [
          'E:last-of-type',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':last-of-type'",
          ],
        ],
        [
          'E:only-child',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':only-child'",
          ],
        ],
        [
          'E:only-of-type',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':only-of-type'",
          ],
        ],
        [
          'E:empty',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':empty'",
          ],
        ],
        // CSS 3 specification - link pseudo classes
        [
          'E:link',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':link'",
          ],
        ],
        [
          'E:visited',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':visited'",
          ],
        ],
        // CSS 3 specification - user action pseudo classes
        [
          'E:active',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':active'",
          ],
        ],
        [
          'E:hover',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':hover'",
          ],
        ],
        [
          'E:focus',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':focus'",
          ],
        ],
        // CSS 3 specification - target pseudo class
        [
          'E:target',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':target'",
          ],
        ],
        // CSS 3 specification - language pseudo class
        [
          'E:lang(fr)',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':lang'",
            "TOKEN::PARENTHESES_START @6 '('",
            "TOKEN::IDENTIFIER @7 'fr'",
            "TOKEN::PARENTHESES_END @9 ')'",
          ],
        ],
        // CSS 3 specification - ui element states pseudo classes
        [
          'E:enabled',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':enabled'",
          ],
        ],
        [
          'E:disabled',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':disabled'",
          ],
        ],
        [
          'E:checked',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':checked'",
          ],
        ],
        // CSS 3 specification - pseudo elements
        [
          'E::first-line',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOELEMENT @1 '::first-line'",
          ],
        ],
        [
          'E::first-letter',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOELEMENT @1 '::first-letter'",
          ],
        ],
        [
          'E::before',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOELEMENT @1 '::before'",
          ],
        ],
        [
          'E::after',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOELEMENT @1 '::after'",
          ],
        ],
        // CSS 3 specification - class selector
        [
          'E.warning',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::SIMPLESELECTOR_CLASS @1 '.warning'",
          ],
        ],
        // CSS 3 specification - id selector
        [
          'E#myid',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::SIMPLE_SELECTOR_ID @1 '#myid'",
          ],
        ],
        // CSS 3 specification - negation pseudo class
        [
          'E:not(s)',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::PSEUDOCLASS @1 ':not'",
            "TOKEN::PARENTHESES_START @5 '('",
            "TOKEN::IDENTIFIER @6 's'",
            "TOKEN::PARENTHESES_END @7 ')'",
          ],
        ],
        // CSS 3 specification - combinators
        [
          'E F',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::WHITESPACE @1 ' '",
            "TOKEN::IDENTIFIER @2 'F'",
          ],
        ],
        [
          'E > F',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::SELECTOR_COMBINATOR @1 ' > '",
            "TOKEN::IDENTIFIER @4 'F'",
          ],
        ],
        [
          'E + F',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::SELECTOR_COMBINATOR @1 ' + '",
            "TOKEN::IDENTIFIER @4 'F'",
          ],
        ],
        [
          'E ~ F',
          [
            "TOKEN::IDENTIFIER @0 'E'",
            "TOKEN::SELECTOR_COMBINATOR @1 ' ~ '",
            "TOKEN::IDENTIFIER @4 'F'",
          ],
        ],
        // CSS 3 specification - nth parameters
        [
          "tr:nth-child(2n+1)",
          [
            "TOKEN::IDENTIFIER @0 'tr'",
            "TOKEN::PSEUDOCLASS @2 ':nth-child'",
            "TOKEN::PARENTHESES_START @12 '('",
            "TOKEN::PSEUDOCLASS_POSITION @13 '2n+1'",
            "TOKEN::PARENTHESES_END @17 ')'",
          ],
        ],
        [
          "tr:nth-child(odd)",
          [
            "TOKEN::IDENTIFIER @0 'tr'",
            "TOKEN::PSEUDOCLASS @2 ':nth-child'",
            "TOKEN::PARENTHESES_START @12 '('",
            "TOKEN::IDENTIFIER @13 'odd'",
            "TOKEN::PARENTHESES_END @16 ')'",
          ],
        ],
        [
          "tr:nth-child(2n+0)",
          [
            "TOKEN::IDENTIFIER @0 'tr'",
            "TOKEN::PSEUDOCLASS @2 ':nth-child'",
            "TOKEN::PARENTHESES_START @12 '('",
            "TOKEN::PSEUDOCLASS_POSITION @13 '2n+0'",
            "TOKEN::PARENTHESES_END @17 ')'",
          ],
        ],
        [
          "tr:nth-child(even)",
          [
            "TOKEN::IDENTIFIER @0 'tr'",
            "TOKEN::PSEUDOCLASS @2 ':nth-child'",
            "TOKEN::PARENTHESES_START @12 '('",
            "TOKEN::IDENTIFIER @13 'even'",
            "TOKEN::PARENTHESES_END @17 ')'",
          ],
        ],
        [
          "tr:nth-child(10n-1)",
          [
            "TOKEN::IDENTIFIER @0 'tr'",
            "TOKEN::PSEUDOCLASS @2 ':nth-child'",
            "TOKEN::PARENTHESES_START @12 '('",
            "TOKEN::PSEUDOCLASS_POSITION @13 '10n-1'",
            "TOKEN::PARENTHESES_END @18 ')'",
          ],
        ],
        [
          "tr:nth-child(10n+9)",
          [
            "TOKEN::IDENTIFIER @0 'tr'",
            "TOKEN::PSEUDOCLASS @2 ':nth-child'",
            "TOKEN::PARENTHESES_START @12 '('",
            "TOKEN::PSEUDOCLASS_POSITION @13 '10n+9'",
            "TOKEN::PARENTHESES_END @18 ')'",
          ],
        ],

        // individual
        [
          "test",
          [
            "TOKEN::IDENTIFIER @0 'test'",
          ],
        ],
        [
          "test'string'",
          [
            "TOKEN::IDENTIFIER @0 'test'",
            "TOKEN::STRING_SINGLE_QUOTE_START @4 '\\''",
            "TOKEN::STRING_CHARACTERS @5 'string'",
            "TOKEN::STRING_SINGLE_QUOTE_END @11 '\\''",
          ],
        ],
        [
          'div#id.class1.class2:not(.title)',
          [
            "TOKEN::IDENTIFIER @0 'div'",
            "TOKEN::SIMPLE_SELECTOR_ID @3 '#id'",
            "TOKEN::SIMPLESELECTOR_CLASS @6 '.class1'",
            "TOKEN::SIMPLESELECTOR_CLASS @13 '.class2'",
            "TOKEN::PSEUDOCLASS @20 ':not'",
            "TOKEN::PARENTHESES_START @24 '('",
            "TOKEN::SIMPLESELECTOR_CLASS @25 '.title'",
            "TOKEN::PARENTHESES_END @31 ')'",
          ],
        ],
        [
          "div > span",
          [
            "TOKEN::IDENTIFIER @0 'div'",
            "TOKEN::SELECTOR_COMBINATOR @3 ' > '",
            "TOKEN::IDENTIFIER @6 'span'",
          ],
        ],
        [
          "div span",
          [
            "TOKEN::IDENTIFIER @0 'div'",
            "TOKEN::WHITESPACE @3 ' '",
            "TOKEN::IDENTIFIER @4 'span'",
          ],
        ],
        [
          ':nth-child(3n)',
          [
            "TOKEN::PSEUDOCLASS @0 ':nth-child'",
            "TOKEN::PARENTHESES_START @10 '('",
            "TOKEN::PSEUDOCLASS_POSITION @11 '3n'",
            "TOKEN::PARENTHESES_END @13 ')'",
          ],
        ],
        [
          ':nth-child(10n-1)',
          [
            "TOKEN::PSEUDOCLASS @0 ':nth-child'",
            "TOKEN::PARENTHESES_START @10 '('",
            "TOKEN::PSEUDOCLASS_POSITION @11 '10n-1'",
            "TOKEN::PARENTHESES_END @16 ')'",
          ],
        ],
        [
          ':nth-child( -n+ 6)',
          [
            "TOKEN::PSEUDOCLASS @0 ':nth-child'",
            "TOKEN::PARENTHESES_START @10 '('",
            "TOKEN::PSEUDOCLASS_POSITION @11 ' -n+ 6'",
            "TOKEN::PARENTHESES_END @17 ')'",
          ],
        ],
        [
          ':nth-child( +3n - 2 )',
          [
            "TOKEN::PSEUDOCLASS @0 ':nth-child'",
            "TOKEN::PARENTHESES_START @10 '('",
            "TOKEN::PSEUDOCLASS_POSITION @11 ' +3n - 2 '",
            "TOKEN::PARENTHESES_END @20 ')'",
          ],
        ],
        [
          '> p',
          [
            "TOKEN::SELECTOR_COMBINATOR @0 '> '",
            "TOKEN::IDENTIFIER @2 'p'",
          ],
        ],
        [
          ' + p',
          [
            "TOKEN::SELECTOR_COMBINATOR @0 ' + '",
            "TOKEN::IDENTIFIER @3 'p'",
          ],
        ],
        [
          'p:not(:scope)',
          [
            "TOKEN::IDENTIFIER @0 'p'",
            "TOKEN::PSEUDOCLASS @1 ':not'",
            "TOKEN::PARENTHESES_START @5 '('",
            "TOKEN::PSEUDOCLASS @6 ':scope'",
            "TOKEN::PARENTHESES_END @12 ')'",
          ],
        ],
        [
          'div:contains("text")',
          [
            "TOKEN::IDENTIFIER @0 'div'",
            "TOKEN::PSEUDOCLASS @3 ':contains'",
            "TOKEN::PARENTHESES_START @12 '('",
            "TOKEN::STRING_DOUBLE_QUOTE_START @13 '\"'",
            "TOKEN::STRING_CHARACTERS @14 'text'",
            "TOKEN::STRING_DOUBLE_QUOTE_END @18 '\"'",
            "TOKEN::PARENTHESES_END @19 ')'",
          ],
        ],
        [
          'div:gt(-2)',
          [
            "TOKEN::IDENTIFIER @0 'div'",
            "TOKEN::PSEUDOCLASS @3 ':gt'",
            "TOKEN::PARENTHESES_START @6 '('",
            "TOKEN::NUMBER @7 '-2'",
            "TOKEN::PARENTHESES_END @9 ')'",
          ],
        ],
      ];
    }


    /*****************************
     * Individual assertions
     *****************************/

    public function assertTokenListEqualsStringList(array $expected, array $tokens): void {
      $strings = [];
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
     * @return Scanner\Token|MockObject
     */
    private function getTokenMockObjectFixture(int $length): Scanner\Token {
      $token = $this->createMock(Scanner\Token::CLASS);
      $token
        ->method('__get')
        ->willReturn($length);
      return $token;
    }

    /**
     * @param array $tokens
     * @param bool|null $isEndToken
     * @return Scanner\Status|MockObject
     */
    private function getStatusMockObjectFixture(array $tokens, bool $isEndToken = NULL): Scanner\Status {
      $status = $this
        ->getMockBuilder(Scanner\Status::CLASS)
        ->setMockClassName('Mock_PhpCssScannerStatus')
        ->getMock();
      $calls = count($tokens);
      if (count($tokens) > 0) {
        $status
          ->expects($this->exactly($calls))
          ->method('getToken')
          ->with(
            $this->isType('string'),
            $this->isType('integer')
          )
          ->will(
            call_user_func_array(
              [$this, 'onConsecutiveCalls'],
              $tokens
            )
          );
      }
      if (!is_null($isEndToken)) {
        $returns = array_fill(0, $calls, FALSE);
        $returns[$calls - 1] = $isEndToken;
        $status
          ->method('isEndToken')
          ->with($this->isInstanceOf(Scanner\Token::CLASS))
          ->will(
            call_user_func_array(
              [$this, 'onConsecutiveCalls'],
              $returns
            )
          );
      }
      return $status;
    }
  }
}
