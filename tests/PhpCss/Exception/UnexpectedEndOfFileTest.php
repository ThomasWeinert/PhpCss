<?php
namespace PhpCss\Exception {

  use PhpCss\Scanner;

  require_once(__DIR__.'/../../bootstrap.php');

  class UnexpectedEndOfFileTest extends \PHPUnit\Framework\TestCase {

    /**
    * @covers \PhpCss\Exception\UnexpectedEndOfFileException::__construct
    */
    public function testConstructor() {
      $e = new UnexpectedEndOfFileException(
        array(Scanner\Token::STRING_CHARACTERS)
      );
      $this->assertEquals(
        array(Scanner\Token::STRING_CHARACTERS), $e->getExpected()
      );
      $this->assertEquals(
        'Parse error: Unexpected end of file was found while one of STRING_CHARACTERS was expected.',
        $e->getMessage()
      );
    }
  }
}
