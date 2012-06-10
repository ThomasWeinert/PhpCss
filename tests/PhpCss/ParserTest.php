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
require_once(dirname(__FILE__).'/TestCase.php');
require_once(dirname(__FILE__).'/Parser/Mock.php');
require_once(dirname(__FILE__).'/Parser/Mock/Delegate.php');

/**
* Test class for PhpCssParser.
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssParserTest extends PhpCssTestCase {

  /**
  * @covers PhpCssParser::__construct
  */
  public function testConstructor() {
    $tokens = array(new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 0));
    $parser = $this->getParserFixture($tokens);
    $this->assertAttributeSame(
      $tokens, '_tokens', $parser
    );
  }

  /**
  * @covers PhpCssParser::read
  * @covers PhpCssParser::matchToken
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
  * @covers PhpCssParser::read
  * @covers PhpCssParser::matchToken
  * @covers PhpCssParser::handleMismatch
  * @dataProvider provideDirectMismatchingTokens
  */
  public function testReadMismatch($tokens, $allowedTokens) {
    $parser = $this->getParserFixture($tokens);
    try {
      $result = $parser->read($allowedTokens);
      $this->fail('The expected exception PhpCssExceptionParser has not been thrown.');
    } catch(PhpCssExceptionParser $e) {
    }
  }

  /**
  * @covers PhpCssParser::lookahead
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
  * @covers PhpCssParser::lookahead
  * @dataProvider provideDirectMismatchingTokens
  */
  public function testDirectLookaheadMismatch($tokens, $allowedTokens) {
    $parser = $this->getParserFixture($tokens);

    try {
      $result = $parser->lookahead($allowedTokens);
      $this->fail('The expected exception PhpCssExceptionParser has not been thrown.');
    } catch(PhpCssExceptionParser $e) {
    }
  }

  /**
  * @covers PhpCssParser::lookahead
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
  * @covers PhpCssParser::lookahead
  * @dataProvider provideLookaheadMismatchingTokens
  */
  public function testLookaheadMismatch($tokens, $allowedTokens) {
    $parser = $this->getParserFixture($tokens);

    try {
      $result = $parser->lookahead($allowedTokens, 1);
      $this->fail('The expected exception PhpCssExceptionParser has not been thrown.');
    } catch(PhpCssExceptionParser $e) {
    }
  }

  /**
  * @covers PhpCssParser::endOfTokens
  */
  public function testEndOfTokensExpectingTrue() {
    $tokens = array();
    $parser = $this->getParserFixture($tokens);
    $this->assertTrue($parser->endOfTokens());
  }

  /**
  * @covers PhpCssParser::endOfTokens
  */
  public function testEndOfTokensExpectingFalse() {
    $tokens = array(new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 0));
    $parser = $this->getParserFixture($tokens);
    $this->assertFalse($parser->endOfTokens());
  }

  /**
  * @covers PhpCssParser::endOfTokens
  */
  public function testEndOfTokensWithPositionExpectingTrue() {
    $tokens = array(new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 0));
    $parser = $this->getParserFixture($tokens);
    $this->assertTrue($parser->endOfTokens(2));
  }

  /**
  * @covers PhpCssParser::endOfTokens
  */
  public function testEndOfTokensWithPositionExpectingFalse() {
    $tokens = array(
      new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 0),
      new PhpCssScannerToken(PhpCssScannerToken::CLASS_SELECTOR, '.bar', 0)
    );
    $parser = $this->getParserFixture($tokens);
    $this->assertFalse($parser->endOfTokens(1));
  }

  /**
  * @covers PhpCssParser::lookahead
  */
  public function testLookAheadAllowingEndOfTokens() {
    $parser = $this->getParserFixture(array());
    $this->assertEquals(
      new PhpCssScannerToken(PhpCssScannerToken::ANY, '', 0),
      $parser->lookahead(PhpCssScannerToken::IDENTIFIER, 0, TRUE)
    );
  }

  /**
  * @covers PhpCssParser::lookahead
  */
  public function testLookAheadWithPositionAllowingEndOfTokens() {
    $tokens = array(
      new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 0)
    );
    $parser = $this->getParserFixture($tokens);
    $this->assertEquals(
      new PhpCssScannerToken(PhpCssScannerToken::ANY, '', 0),
      $parser->lookahead(PhpCssScannerToken::IDENTIFIER, 1, TRUE)
    );
  }

  /**
  * @covers PhpCssParser::ignore
  */
  public function testIgnoreExpectingTrue() {
    $tokens = array(
      new PhpCssScannerToken(PhpCssScannerToken::WHITESPACE, ' ', 0),
      new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 1)
    );
    $parser = $this->getParserFixture($tokens);
    $this->assertTrue(
      $parser->ignore(PhpCssScannerToken::WHITESPACE)
    );
  }

  /**
  * @covers PhpCssParser::ignore
  */
  public function testIgnoreMultipleTokensExpectingTrue() {
    $tokens = array(
      new PhpCssScannerToken(PhpCssScannerToken::WHITESPACE, ' ', 0),
      new PhpCssScannerToken(PhpCssScannerToken::WHITESPACE, ' ', 1),
      new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 2)
    );
    $parser = $this->getParserFixture($tokens);
    $this->assertTrue(
      $parser->ignore(
        PhpCssScannerToken::WHITESPACE
      )
    );
  }

  /**
  * @covers PhpCssParser::ignore
  */
  public function testIgnoreExpectingFalse() {
    $tokens = array(
      new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 0)
    );
    $parser = $this->getParserFixture($tokens);
    $this->assertFalse(
      $parser->ignore(PhpCssScannerToken::WHITESPACE)
    );
  }

  /**
  * @covers PhpCssParser::delegate
  */
  public function testDelegate() {
    $parser = $this->getParserFixture();
    $this->assertEquals(
      'Delegated!',
      $parser->delegate('PhpCssParserMockDelegate')
    );
  }

  /*****************************
  * Fixtures
  *****************************/

  public function getParserFixture(array $tokens = array()) {
    return new PhpCssParserMock($tokens);
  }

  public function getParserFixtureWithReference(array &$tokens) {
    return new PhpCssParserMock($tokens);
  }

  /*****************************
  * Data Provider
  *****************************/

  public static function provideDirectMatchingTokens() {
    return array(
      'one token, one token type' => array(
        PhpCssScannerToken::IDENTIFIER, // expected token type
        array(new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 0)), // token list
        array(PhpCssScannerToken::IDENTIFIER), // allowed token types
      ),
      'one token, one token type as string' => array(
        PhpCssScannerToken::IDENTIFIER, // expected token type
        array(new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 0)), // token list
        PhpCssScannerToken::IDENTIFIER, // allowed token types
      ),
      'one token, two token types' =>  array(
        PhpCssScannerToken::IDENTIFIER,
        array(new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 0)),
        array(PhpCssScannerToken::CLASS_SELECTOR, PhpCssScannerToken::IDENTIFIER),
      ),
      'two tokens, one token type' => array(
        PhpCssScannerToken::IDENTIFIER,
        array(
          new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 0),
          new PhpCssScannerToken(PhpCssScannerToken::CLASS_SELECTOR, '.bar', 0)
        ),
        array(PhpCssScannerToken::IDENTIFIER),
      ),
      'two tokens, two token types' => array(
        PhpCssScannerToken::IDENTIFIER,
        array(
          new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 0),
          new PhpCssScannerToken(PhpCssScannerToken::CLASS_SELECTOR, '.bar', 0)
        ),
        array(PhpCssScannerToken::IDENTIFIER, PhpCssScannerToken::CLASS_SELECTOR),
      ),
      'two tokens, any token type' => array(
        PhpCssScannerToken::IDENTIFIER,
        array(
          new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 0),
          new PhpCssScannerToken(PhpCssScannerToken::CLASS_SELECTOR, '.bar', 0)
        ),
        array(PhpCssScannerToken::ANY),
      ),
      'two tokens, any token type as skalar' => array(
        PhpCssScannerToken::IDENTIFIER,
        array(
          new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 0),
          new PhpCssScannerToken(PhpCssScannerToken::CLASS_SELECTOR, '.bar', 0)
        ),
        PhpCssScannerToken::ANY,
      )
    );
  }

  public static function provideDirectMismatchingTokens() {
    return array(
      'one token, one token type' => array(
        array(new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 0)), // token list
        array(PhpCssScannerToken::CLASS_SELECTOR), // allowed token types
      ),
      'one token, two token types' => array(
        array(new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 0)),
        array(PhpCssScannerToken::CLASS_SELECTOR, PhpCssScannerToken::ID_SELECTOR),
      ),
      'two tokens, one token type' => array(
        array(
          new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 0),
          new PhpCssScannerToken(PhpCssScannerToken::CLASS_SELECTOR, '.bar', 0)
        ),
        array(PhpCssScannerToken::CLASS_SELECTOR),
      ),
      'two tokens, two token types' => array(
        array(
          new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 0),
          new PhpCssScannerToken(PhpCssScannerToken::CLASS_SELECTOR, '.bar', 0)
        ),
        array(PhpCssScannerToken::CLASS_SELECTOR, PhpCssScannerToken::ID_SELECTOR),
      ),
      'empty tokens, one token type' => array(
        array(),
        array(PhpCssScannerToken::IDENTIFIER),
      ),
      'empty tokens, special any token type' => array(
        array(),
        array(PhpCssScannerToken::ANY),
      )
    );
  }

  public static function provideLookaheadMatchingTokens() {
    return array(
      array(
        PhpCssScannerToken::CLASS_SELECTOR,
        array(
          new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 0),
          new PhpCssScannerToken(PhpCssScannerToken::CLASS_SELECTOR, '.bar', 0)
        ),
        array(PhpCssScannerToken::CLASS_SELECTOR)
      ),
      array(
        PhpCssScannerToken::CLASS_SELECTOR,
        array(
          new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 0),
          new PhpCssScannerToken(PhpCssScannerToken::CLASS_SELECTOR, '.bar', 0)
        ),
        array(PhpCssScannerToken::CLASS_SELECTOR, PhpCssScannerToken::IDENTIFIER)
      ),
      array(
        PhpCssScannerToken::CLASS_SELECTOR,
        array(
          new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 0),
          new PhpCssScannerToken(PhpCssScannerToken::CLASS_SELECTOR, '.bar', 0)
        ),
        array(PhpCssScannerToken::ANY)
      ),
      array(
        PhpCssScannerToken::CLASS_SELECTOR,
        array(
          new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 0),
          new PhpCssScannerToken(PhpCssScannerToken::CLASS_SELECTOR, '.bar', 0)
        ),
        PhpCssScannerToken::ANY
      )
    );
  }

  public static function provideLookaheadMismatchingTokens() {
    return array(
      array(
        array(
          new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 0),
        ),
        array(PhpCssScannerToken::IDENTIFIER)
      ),
      array(
        array(
          new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 0),
        ),
        array(PhpCssScannerToken::IDENTIFIER, PhpCssScannerToken::CLASS_SELECTOR)
      ),
      array(
        array(
          new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 0),
          new PhpCssScannerToken(PhpCssScannerToken::CLASS_SELECTOR, 'foo', 0),
        ),
        array(PhpCssScannerToken::IDENTIFIER)
      ),
      array(
        array(
          new PhpCssScannerToken(PhpCssScannerToken::IDENTIFIER, 'foo', 0),
          new PhpCssScannerToken(PhpCssScannerToken::CLASS_SELECTOR, 'foo', 0),
        ),
        array(PhpCssScannerToken::IDENTIFIER, PhpCssScannerToken::ID_SELECTOR)
      )
    );
  }
}