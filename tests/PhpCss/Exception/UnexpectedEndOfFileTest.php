<?php
namespace PhpCss\Exception {

  use PhpCss\Scanner;

  require_once(__DIR__.'/../../bootstrap.php');

  class UnexpectedEndOfFileTest extends \PHPUnit_Framework_TestCase {

    /**
    * @covers PhpCss\Exception\UnexpectedEndOfFile::__construct
    */
    public function testConstructor() {
      $e = new UnexpectedEndOfFile(
        array(Scanner\Token::STRING_CHARACTERS)
      );
      $this->assertAttributeEquals(
        array(Scanner\Token::STRING_CHARACTERS), 'expectedTokens', $e
      );
      $this->assertEquals(
        'Parse error: Unexpected end of file was found while one of STRING_CHARACTERS was expected.',
        $e->getMessage()
      );
    }
  }
}