<?php
namespace PhpCss\Exception {

  use PhpCss\Scanner;

  require_once(__DIR__.'/../../bootstrap.php');

  class TokenMismatchTest extends \PHPUnit_Framework_TestCase {

    /**
    * @covers PhpCss\Exception\TokenMismatch::__construct
    */
    public function testConstructor() {
      $expectedToken = new Scanner\Token(
        Scanner\Token::IDENTIFIER, 'sample', 42
      );
      $e = new TokenMismatch(
        $expectedToken, array(Scanner\Token::STRING_CHARACTERS)
      );
      $this->assertAttributeEquals(
        $expectedToken, 'encounteredToken', $e
      );
      $this->assertAttributeEquals(
        array(Scanner\Token::STRING_CHARACTERS), 'expectedTokens', $e
      );
      $this->assertEquals(
        'Parse error: Found TOKEN::IDENTIFIER @42 \'sample\' while one of STRING_CHARACTERS was expected.',
        $e->getMessage()
      );
    }
  }
}