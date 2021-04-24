<?php

namespace PhpCss\Exception {

  use PhpCss\Scanner;
  use PHPUnit\Framework\TestCase;

  require_once(__DIR__.'/../../bootstrap.php');

  class TokenMismatchTest extends TestCase {

    /**
     * @covers \PhpCss\Exception\TokenMismatchException::__construct
     */
    public function testConstructor(): void {
      $expectedToken = new Scanner\Token(
        Scanner\Token::IDENTIFIER, 'sample', 42
      );
      $e = new TokenMismatchException(
        $expectedToken, [Scanner\Token::STRING_CHARACTERS]
      );
      $this->assertEquals(
        $expectedToken, $e->getToken()
      );
      $this->assertEquals(
        [Scanner\Token::STRING_CHARACTERS], $e->getExpected()
      );
      $this->assertEquals(
        'Parse error: Found TOKEN::IDENTIFIER @42 \'sample\' while one of STRING_CHARACTERS was expected.',
        $e->getMessage()
      );
    }
  }
}
