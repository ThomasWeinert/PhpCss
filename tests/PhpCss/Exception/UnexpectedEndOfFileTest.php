<?php

namespace PhpCss\Exception {

  use PhpCss\Scanner;
  use PHPUnit\Framework\TestCase;

  require_once(__DIR__.'/../../bootstrap.php');

  class UnexpectedEndOfFileTest extends TestCase {

    /**
     * @covers \PhpCss\Exception\UnexpectedEndOfFileException::__construct
     */
    public function testConstructor(): void {
      $e = new UnexpectedEndOfFileException(
        [Scanner\Token::STRING_CHARACTERS]
      );
      $this->assertEquals(
        [Scanner\Token::STRING_CHARACTERS], $e->getExpected()
      );
      $this->assertEquals(
        'Parse error: Unexpected end of file was found while one of STRING_CHARACTERS was expected.',
        $e->getMessage()
      );
    }
  }
}
