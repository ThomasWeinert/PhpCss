<?php

namespace PhpCss {

  use PHPUnit\Framework\TestCase;

  require_once(__DIR__.'/../bootstrap.php');
  require_once(__DIR__.'/Parser/Mocks.php');

  class ParserTest extends TestCase {

    /**
     * @covers \PhpCss\Parser::__construct
     */
    public function testConstructor(): void {
      $tokens = [new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0)];
      $parser = $this->getParserFixture($tokens);
      $this->assertSame(
        $tokens, $parser->getTokens()
      );
    }

    /**
     * @covers       \PhpCss\Parser::read
     * @covers       \PhpCss\Parser::matchToken
     * @dataProvider provideDirectMatchingTokens
     */
    public function testReadMatch($expectedResult, $tokens, $allowedTokens): void {
      $originalTokens = $tokens;
      array_shift($originalTokens);

      $parser = $this->getParserFixture($tokens);

      $result = $parser->read($allowedTokens);

      $this->assertSame($tokens[0], $result);
      $this->assertEquals($expectedResult, $result->type);
      $this->assertEquals($parser->_tokens, $originalTokens);
    }

    /**
     * @covers       \PhpCss\Parser::read
     * @covers       \PhpCss\Parser::matchToken
     * @covers       \PhpCss\Parser::handleMismatch
     * @dataProvider provideDirectMismatchingTokens
     */
    public function testReadMismatch($tokens, $allowedTokens): void {
      $parser = $this->getParserFixture($tokens);
      $this->expectException(Exception\ParserException::CLASS);
      $parser->read($allowedTokens);
    }

    /**
     * @covers       \PhpCss\Parser::lookahead
     * @dataProvider provideDirectMatchingTokens
     */
    public function testDirectLookaheadMatch($expectedResult, $tokens, $allowedTokens): void {
      $originalTokens = $tokens;

      $parser = $this->getParserFixture($tokens);
      $result = $parser->lookahead($allowedTokens);

      $this->assertSame($tokens[0], $result);
      $this->assertEquals($expectedResult, $result->type);
      $this->assertEquals($parser->_tokens, $originalTokens);
    }

    /**
     * @covers       \PhpCss\Parser::lookahead
     * @dataProvider provideDirectMismatchingTokens
     */
    public function testDirectLookaheadMismatch($tokens, $allowedTokens): void {
      $parser = $this->getParserFixture($tokens);
      $this->expectException(Exception\ParserException::CLASS);
      $parser->lookahead($allowedTokens);
    }

    /**
     * @covers       \PhpCss\Parser::lookahead
     * @dataProvider provideLookaheadMatchingTokens
     */
    public function testLookaheadMatch($expectedResult, $tokens, $allowedTokens): void {
      $originalTokens = $tokens;

      $parser = $this->getParserFixture($tokens);
      $result = $parser->lookahead($allowedTokens, 1);

      $this->assertSame($tokens[1], $result);
      $this->assertEquals($expectedResult, $result->type);
      $this->assertEquals($parser->_tokens, $originalTokens);
    }

    /**
     * @covers       \PhpCss\Parser::lookahead
     * @dataProvider provideLookaheadMismatchingTokens
     */
    public function testLookaheadMismatch($tokens, $allowedTokens): void {
      $parser = $this->getParserFixture($tokens);
      $this->expectException(Exception\ParserException::CLASS);
      $parser->lookahead($allowedTokens, 1);
    }

    /**
     * @covers \PhpCss\Parser::endOfTokens
     */
    public function testEndOfTokensExpectingTrue(): void {
      $tokens = [];
      $parser = $this->getParserFixture($tokens);
      $this->assertTrue($parser->endOfTokens());
    }

    /**
     * @covers \PhpCss\Parser::endOfTokens
     */
    public function testEndOfTokensExpectingFalse(): void {
      $tokens = [new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0)];
      $parser = $this->getParserFixture($tokens);
      $this->assertFalse($parser->endOfTokens());
    }

    /**
     * @covers \PhpCss\Parser::endOfTokens
     */
    public function testEndOfTokensWithPositionExpectingTrue(): void {
      $tokens = [new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0)];
      $parser = $this->getParserFixture($tokens);
      $this->assertTrue($parser->endOfTokens(2));
    }

    /**
     * @covers \PhpCss\Parser::endOfTokens
     */
    public function testEndOfTokensWithPositionExpectingFalse(): void {
      $tokens = [
        new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
        new Scanner\Token(Scanner\Token::CLASS_SELECTOR, '.bar', 0),
      ];
      $parser = $this->getParserFixture($tokens);
      $this->assertFalse($parser->endOfTokens(1));
    }

    /**
     * @covers \PhpCss\Parser::lookahead
     */
    public function testLookAheadAllowingEndOfTokens(): void {
      $parser = $this->getParserFixture([]);
      $this->assertEquals(
        new Scanner\Token(Scanner\Token::ANY, '', 0),
        $parser->lookahead(Scanner\Token::IDENTIFIER, 0, TRUE)
      );
    }

    /**
     * @covers \PhpCss\Parser::lookahead
     */
    public function testLookAheadWithPositionAllowingEndOfTokens(): void {
      $tokens = [
        new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
      ];
      $parser = $this->getParserFixture($tokens);
      $this->assertEquals(
        new Scanner\Token(Scanner\Token::ANY, '', 0),
        $parser->lookahead(Scanner\Token::IDENTIFIER, 1, TRUE)
      );
    }

    /**
     * @covers \PhpCss\Parser::ignore
     */
    public function testIgnoreExpectingTrue(): void {
      $tokens = [
        new Scanner\Token(Scanner\Token::WHITESPACE, ' ', 0),
        new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 1),
      ];
      $parser = $this->getParserFixture($tokens);
      $this->assertTrue(
        $parser->ignore(Scanner\Token::WHITESPACE)
      );
      $this->assertTrue($parser->endOfTokens(1));
    }

    /**
     * @covers \PhpCss\Parser::ignore
     */
    public function testIgnoreMultipleTokensExpectingTrue(): void {
      $tokens = [
        new Scanner\Token(Scanner\Token::WHITESPACE, ' ', 0),
        new Scanner\Token(Scanner\Token::WHITESPACE, ' ', 1),
        new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 2),
      ];
      $parser = $this->getParserFixture($tokens);
      $this->assertTrue(
        $parser->ignore(
          Scanner\Token::WHITESPACE
        )
      );
      $this->assertTrue($parser->endOfTokens(1));
    }

    /**
     * @covers \PhpCss\Parser::ignore
     */
    public function testIgnoreExpectingFalse(): void {
      $tokens = [
        new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
      ];
      $parser = $this->getParserFixture($tokens);
      $this->assertFalse(
        $parser->ignore(Scanner\Token::WHITESPACE)
      );
      $this->assertTrue($parser->endOfTokens(1));
    }

    /**
     * @covers \PhpCss\Parser::delegate
     */
    public function testDelegate(): void {
      $parser = $this->getParserFixture();
      $node = $parser->delegate(Parser\MockDelegate::CLASS);
      $this->assertEquals('Delegated!', $node->value);
    }

    /*****************************
     * Fixtures
     *****************************/

    public function getParserFixture(array $tokens = []): Parser\Mock {
      return new Parser\Mock($tokens);
    }

    public function getParserFixtureWithReference(array &$tokens): Parser\Mock {
      return new Parser\Mock($tokens);
    }

    /*****************************
     * Data Provider
     *****************************/

    public static function provideDirectMatchingTokens(): array {
      return [
        'one token, one token type' => [
          Scanner\Token::IDENTIFIER, // expected token type
          [new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0)], // token list
          [Scanner\Token::IDENTIFIER], // allowed token types
        ],
        'one token, one token type as string' => [
          Scanner\Token::IDENTIFIER, // expected token type
          [new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0)], // token list
          Scanner\Token::IDENTIFIER, // allowed token types
        ],
        'one token, two token types' => [
          Scanner\Token::IDENTIFIER,
          [new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0)],
          [Scanner\Token::CLASS_SELECTOR, Scanner\Token::IDENTIFIER],
        ],
        'two tokens, one token type' => [
          Scanner\Token::IDENTIFIER,
          [
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
            new Scanner\Token(Scanner\Token::CLASS_SELECTOR, '.bar', 0),
          ],
          [Scanner\Token::IDENTIFIER],
        ],
        'two tokens, two token types' => [
          Scanner\Token::IDENTIFIER,
          [
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
            new Scanner\Token(Scanner\Token::CLASS_SELECTOR, '.bar', 0),
          ],
          [Scanner\Token::IDENTIFIER, Scanner\Token::CLASS_SELECTOR],
        ],
        'two tokens, any token type' => [
          Scanner\Token::IDENTIFIER,
          [
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
            new Scanner\Token(Scanner\Token::CLASS_SELECTOR, '.bar', 0),
          ],
          [Scanner\Token::ANY],
        ],
        'two tokens, any token type as skalar' => [
          Scanner\Token::IDENTIFIER,
          [
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
            new Scanner\Token(Scanner\Token::CLASS_SELECTOR, '.bar', 0),
          ],
          Scanner\Token::ANY,
        ],
      ];
    }

    public static function provideDirectMismatchingTokens(): array {
      return [
        'one token, one token type' => [
          [new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0)], // token list
          [Scanner\Token::CLASS_SELECTOR], // allowed token types
        ],
        'one token, two token types' => [
          [new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0)],
          [Scanner\Token::CLASS_SELECTOR, Scanner\Token::ID_SELECTOR],
        ],
        'two tokens, one token type' => [
          [
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
            new Scanner\Token(Scanner\Token::CLASS_SELECTOR, '.bar', 0),
          ],
          [Scanner\Token::CLASS_SELECTOR],
        ],
        'two tokens, two token types' => [
          [
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
            new Scanner\Token(Scanner\Token::CLASS_SELECTOR, '.bar', 0),
          ],
          [Scanner\Token::CLASS_SELECTOR, Scanner\Token::ID_SELECTOR],
        ],
        'empty tokens, one token type' => [
          [],
          [Scanner\Token::IDENTIFIER],
        ],
        'empty tokens, special any token type' => [
          [],
          [Scanner\Token::ANY],
        ],
      ];
    }

    public static function provideLookaheadMatchingTokens(): array {
      return [
        [
          Scanner\Token::CLASS_SELECTOR,
          [
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
            new Scanner\Token(Scanner\Token::CLASS_SELECTOR, '.bar', 0),
          ],
          [Scanner\Token::CLASS_SELECTOR],
        ],
        [
          Scanner\Token::CLASS_SELECTOR,
          [
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
            new Scanner\Token(Scanner\Token::CLASS_SELECTOR, '.bar', 0),
          ],
          [Scanner\Token::CLASS_SELECTOR, Scanner\Token::IDENTIFIER],
        ],
        [
          Scanner\Token::CLASS_SELECTOR,
          [
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
            new Scanner\Token(Scanner\Token::CLASS_SELECTOR, '.bar', 0),
          ],
          [Scanner\Token::ANY],
        ],
        [
          Scanner\Token::CLASS_SELECTOR,
          [
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
            new Scanner\Token(Scanner\Token::CLASS_SELECTOR, '.bar', 0),
          ],
          Scanner\Token::ANY,
        ],
      ];
    }

    public static function provideLookaheadMismatchingTokens(): array {
      return [
        [
          [
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
          ],
          [Scanner\Token::IDENTIFIER],
        ],
        [
          [
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
          ],
          [Scanner\Token::IDENTIFIER, Scanner\Token::CLASS_SELECTOR],
        ],
        [
          [
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
            new Scanner\Token(Scanner\Token::CLASS_SELECTOR, 'foo', 0),
          ],
          [Scanner\Token::IDENTIFIER],
        ],
        [
          [
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
            new Scanner\Token(Scanner\Token::CLASS_SELECTOR, 'foo', 0),
          ],
          [Scanner\Token::IDENTIFIER, Scanner\Token::ID_SELECTOR],
        ],
      ];
    }
  }
}
