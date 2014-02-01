<?php
namespace PhpCss {

  require_once(__DIR__.'/../bootstrap.php');
  require_once(__DIR__.'/Parser/Mocks.php');

  class ParserTest extends \PHPUnit_Framework_TestCase {

    /**
    * @covers PhpCss\Parser::__construct
    */
    public function testConstructor() {
      $tokens = array(new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0));
      $parser = $this->getParserFixture($tokens);
      $this->assertAttributeSame(
        $tokens, '_tokens', $parser
      );
    }

    /**
    * @covers PhpCss\Parser::read
    * @covers PhpCss\Parser::matchToken
    * @dataProvider provideDirectMatchingTokens
    */
    public function testReadMatch($expectedResult, $tokens, $allowedTokens) {
      $originalTokens = $tokens;
      array_shift($originalTokens);

      $parser = $this->getParserFixture($tokens);

      $result = $parser->read($allowedTokens);

      $this->assertSame($tokens[0], $result);
      $this->assertEquals($expectedResult, $result->type);
      $this->assertEquals($parser->_tokens, $originalTokens);
    }

    /**
    * @covers PhpCss\Parser::read
    * @covers PhpCss\Parser::matchToken
    * @covers PhpCss\Parser::handleMismatch
    * @dataProvider provideDirectMismatchingTokens
    */
    public function testReadMismatch($tokens, $allowedTokens) {
      $parser = $this->getParserFixture($tokens);
      $this->setExpectedException(Exception\Parser::CLASS);
      $result = $parser->read($allowedTokens);
    }

    /**
    * @covers PhpCss\Parser::lookahead
    * @dataProvider provideDirectMatchingTokens
    */
    public function testDirectLookaheadMatch($expectedResult, $tokens, $allowedTokens) {
      $originalTokens = $tokens;

      $parser = $this->getParserFixture($tokens);
      $result = $parser->lookahead($allowedTokens);

      $this->assertSame($tokens[0], $result);
      $this->assertEquals($expectedResult, $result->type);
      $this->assertEquals($parser->_tokens, $originalTokens);
    }

    /**
    * @covers PhpCss\Parser::lookahead
    * @dataProvider provideDirectMismatchingTokens
    */
    public function testDirectLookaheadMismatch($tokens, $allowedTokens) {
      $parser = $this->getParserFixture($tokens);
      $this->setExpectedException(Exception\Parser::CLASS);
      $result = $parser->lookahead($allowedTokens);
    }

    /**
    * @covers PhpCss\Parser::lookahead
    * @dataProvider provideLookaheadMatchingTokens
    */
    public function testLookaheadMatch($expectedResult, $tokens, $allowedTokens) {
      $originalTokens = $tokens;

      $parser = $this->getParserFixture($tokens);
      $result = $parser->lookahead($allowedTokens, 1);

      $this->assertSame($tokens[1], $result);
      $this->assertEquals($expectedResult, $result->type);
      $this->assertEquals($parser->_tokens, $originalTokens);
    }

    /**
    * @covers PhpCss\Parser::lookahead
    * @dataProvider provideLookaheadMismatchingTokens
    */
    public function testLookaheadMismatch($tokens, $allowedTokens) {
      $parser = $this->getParserFixture($tokens);
      $this->setExpectedException(Exception\Parser::CLASS);
      $result = $parser->lookahead($allowedTokens, 1);
    }

    /**
    * @covers PhpCss\Parser::endOfTokens
    */
    public function testEndOfTokensExpectingTrue() {
      $tokens = array();
      $parser = $this->getParserFixture($tokens);
      $this->assertTrue($parser->endOfTokens());
    }

    /**
    * @covers PhpCss\Parser::endOfTokens
    */
    public function testEndOfTokensExpectingFalse() {
      $tokens = array(new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0));
      $parser = $this->getParserFixture($tokens);
      $this->assertFalse($parser->endOfTokens());
    }

    /**
    * @covers PhpCss\Parser::endOfTokens
    */
    public function testEndOfTokensWithPositionExpectingTrue() {
      $tokens = array(new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0));
      $parser = $this->getParserFixture($tokens);
      $this->assertTrue($parser->endOfTokens(2));
    }

    /**
    * @covers PhpCss\Parser::endOfTokens
    */
    public function testEndOfTokensWithPositionExpectingFalse() {
      $tokens = array(
        new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
        new Scanner\Token(Scanner\Token::CLASS_SELECTOR, '.bar', 0)
      );
      $parser = $this->getParserFixture($tokens);
      $this->assertFalse($parser->endOfTokens(1));
    }

    /**
    * @covers PhpCss\Parser::lookahead
    */
    public function testLookAheadAllowingEndOfTokens() {
      $parser = $this->getParserFixture(array());
      $this->assertEquals(
        new Scanner\Token(Scanner\Token::ANY, '', 0),
        $parser->lookahead(Scanner\Token::IDENTIFIER, 0, TRUE)
      );
    }

    /**
    * @covers PhpCss\Parser::lookahead
    */
    public function testLookAheadWithPositionAllowingEndOfTokens() {
      $tokens = array(
        new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0)
      );
      $parser = $this->getParserFixture($tokens);
      $this->assertEquals(
        new Scanner\Token(Scanner\Token::ANY, '', 0),
        $parser->lookahead(Scanner\Token::IDENTIFIER, 1, TRUE)
      );
    }

    /**
    * @covers PhpCss\Parser::ignore
    */
    public function testIgnoreExpectingTrue() {
      $tokens = array(
        new Scanner\Token(Scanner\Token::WHITESPACE, ' ', 0),
        new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 1)
      );
      $parser = $this->getParserFixture($tokens);
      $this->assertTrue(
        $parser->ignore(Scanner\Token::WHITESPACE)
      );
      $this->assertTrue($parser->endOfTokens(1));
    }

    /**
    * @covers PhpCss\Parser::ignore
    */
    public function testIgnoreMultipleTokensExpectingTrue() {
      $tokens = array(
        new Scanner\Token(Scanner\Token::WHITESPACE, ' ', 0),
        new Scanner\Token(Scanner\Token::WHITESPACE, ' ', 1),
        new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 2)
      );
      $parser = $this->getParserFixture($tokens);
      $this->assertTrue(
        $parser->ignore(
          Scanner\Token::WHITESPACE
        )
      );
      $this->assertTrue($parser->endOfTokens(1));
    }

    /**
    * @covers PhpCss\Parser::ignore
    */
    public function testIgnoreExpectingFalse() {
      $tokens = array(
        new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0)
      );
      $parser = $this->getParserFixture($tokens);
      $this->assertFalse(
        $parser->ignore(Scanner\Token::WHITESPACE)
      );
      $this->assertTrue($parser->endOfTokens(1));
    }

    /**
    * @covers PhpCss\Parser::delegate
    */
    public function testDelegate() {
      $parser = $this->getParserFixture();
      $this->assertEquals(
        'Delegated!',
        $parser->delegate(Parser\MockDelegate::CLASS)
      );
    }

    /*****************************
    * Fixtures
    *****************************/

    public function getParserFixture(array $tokens = array()) {
      return new Parser\Mock($tokens);
    }

    public function getParserFixtureWithReference(array &$tokens) {
      return new Parser\Mock($tokens);
    }

    /*****************************
    * Data Provider
    *****************************/

    public static function provideDirectMatchingTokens() {
      return array(
        'one token, one token type' => array(
          Scanner\Token::IDENTIFIER, // expected token type
          array(new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0)), // token list
          array(Scanner\Token::IDENTIFIER), // allowed token types
        ),
        'one token, one token type as string' => array(
          Scanner\Token::IDENTIFIER, // expected token type
          array(new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0)), // token list
          Scanner\Token::IDENTIFIER, // allowed token types
        ),
        'one token, two token types' =>  array(
          Scanner\Token::IDENTIFIER,
          array(new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0)),
          array(Scanner\Token::CLASS_SELECTOR, Scanner\Token::IDENTIFIER),
        ),
        'two tokens, one token type' => array(
          Scanner\Token::IDENTIFIER,
          array(
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
            new Scanner\Token(Scanner\Token::CLASS_SELECTOR, '.bar', 0)
          ),
          array(Scanner\Token::IDENTIFIER),
        ),
        'two tokens, two token types' => array(
          Scanner\Token::IDENTIFIER,
          array(
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
            new Scanner\Token(Scanner\Token::CLASS_SELECTOR, '.bar', 0)
          ),
          array(Scanner\Token::IDENTIFIER, Scanner\Token::CLASS_SELECTOR),
        ),
        'two tokens, any token type' => array(
          Scanner\Token::IDENTIFIER,
          array(
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
            new Scanner\Token(Scanner\Token::CLASS_SELECTOR, '.bar', 0)
          ),
          array(Scanner\Token::ANY),
        ),
        'two tokens, any token type as skalar' => array(
          Scanner\Token::IDENTIFIER,
          array(
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
            new Scanner\Token(Scanner\Token::CLASS_SELECTOR, '.bar', 0)
          ),
          Scanner\Token::ANY,
        )
      );
    }

    public static function provideDirectMismatchingTokens() {
      return array(
        'one token, one token type' => array(
          array(new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0)), // token list
          array(Scanner\Token::CLASS_SELECTOR), // allowed token types
        ),
        'one token, two token types' => array(
          array(new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0)),
          array(Scanner\Token::CLASS_SELECTOR, Scanner\Token::ID_SELECTOR),
        ),
        'two tokens, one token type' => array(
          array(
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
            new Scanner\Token(Scanner\Token::CLASS_SELECTOR, '.bar', 0)
          ),
          array(Scanner\Token::CLASS_SELECTOR),
        ),
        'two tokens, two token types' => array(
          array(
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
            new Scanner\Token(Scanner\Token::CLASS_SELECTOR, '.bar', 0)
          ),
          array(Scanner\Token::CLASS_SELECTOR, Scanner\Token::ID_SELECTOR),
        ),
        'empty tokens, one token type' => array(
          array(),
          array(Scanner\Token::IDENTIFIER),
        ),
        'empty tokens, special any token type' => array(
          array(),
          array(Scanner\Token::ANY),
        )
      );
    }

    public static function provideLookaheadMatchingTokens() {
      return array(
        array(
          Scanner\Token::CLASS_SELECTOR,
          array(
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
            new Scanner\Token(Scanner\Token::CLASS_SELECTOR, '.bar', 0)
          ),
          array(Scanner\Token::CLASS_SELECTOR)
        ),
        array(
          Scanner\Token::CLASS_SELECTOR,
          array(
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
            new Scanner\Token(Scanner\Token::CLASS_SELECTOR, '.bar', 0)
          ),
          array(Scanner\Token::CLASS_SELECTOR, Scanner\Token::IDENTIFIER)
        ),
        array(
          Scanner\Token::CLASS_SELECTOR,
          array(
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
            new Scanner\Token(Scanner\Token::CLASS_SELECTOR, '.bar', 0)
          ),
          array(Scanner\Token::ANY)
        ),
        array(
          Scanner\Token::CLASS_SELECTOR,
          array(
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
            new Scanner\Token(Scanner\Token::CLASS_SELECTOR, '.bar', 0)
          ),
          Scanner\Token::ANY
        )
      );
    }

    public static function provideLookaheadMismatchingTokens() {
      return array(
        array(
          array(
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
          ),
          array(Scanner\Token::IDENTIFIER)
        ),
        array(
          array(
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
          ),
          array(Scanner\Token::IDENTIFIER, Scanner\Token::CLASS_SELECTOR)
        ),
        array(
          array(
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
            new Scanner\Token(Scanner\Token::CLASS_SELECTOR, 'foo', 0),
          ),
          array(Scanner\Token::IDENTIFIER)
        ),
        array(
          array(
            new Scanner\Token(Scanner\Token::IDENTIFIER, 'foo', 0),
            new Scanner\Token(Scanner\Token::CLASS_SELECTOR, 'foo', 0),
          ),
          array(Scanner\Token::IDENTIFIER, Scanner\Token::ID_SELECTOR)
        )
      );
    }
  }
}