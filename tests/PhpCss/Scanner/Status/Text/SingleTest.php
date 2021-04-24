<?php

namespace PhpCss\Scanner\Status\Text {

  use PhpCss\Scanner;
  use PHPUnit\Framework\TestCase;

  require_once(__DIR__.'/../../../../bootstrap.php');

  class SingleTest extends TestCase {

    /**
     * @covers       \PhpCss\Scanner\Status\Text\Single::getToken
     * @dataProvider getTokenDataProvider
     */
    public function testGetToken($string, $expectedToken): void {
      $status = new Single();
      $this->assertEquals(
        $status->getToken($string, 0),
        $expectedToken
      );
    }

    /**
     * @covers \PhpCss\Scanner\Status\Text\Single::isEndToken
     */
    public function testIsEndToken(): void {
      $status = new Single();
      $this->assertTrue(
        $status->isEndToken(
          new Scanner\Token(
            Scanner\Token::SINGLEQUOTE_STRING_END, "'", 0
          )
        )
      );
    }

    /**
     * @covers \PhpCss\Scanner\Status\Text\Single::getNewStatus
     */
    public function testGetNewStatus(): void {
      $status = new Single();
      $token = $this->createMock(Scanner\Token::CLASS);
      /**
       * @var Scanner\Token $token
       */
      $this->assertNULL($status->getNewStatus($token));
    }


    /*****************************
     * Data provider
     *****************************/

    public static function getTokenDataProvider(): array {
      return [
        'empty' => [
          '',
          NULL,
        ],
        'single quote string end' => [
          "'",
          new Scanner\Token(
            Scanner\Token::SINGLEQUOTE_STRING_END, "'", 0
          ),
        ],
        'escaped backslash' => [
          '\\\\',
          new Scanner\Token(
            Scanner\Token::STRING_ESCAPED_CHARACTER, '\\\\', 0
          ),
        ],
        'string chars' => [
          'abcd',
          new Scanner\Token(
            Scanner\Token::STRING_CHARACTERS, 'abcd', 0
          ),
        ],
      ];
    }
  }
}
