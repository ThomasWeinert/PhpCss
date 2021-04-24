<?php

namespace PhpCss\Scanner\Status\Text {

  use PhpCss\Scanner;
  use PHPUnit\Framework\TestCase;

  require_once(__DIR__.'/../../../../bootstrap.php');

  class DoubleTest extends TestCase {

    /**
     * @covers       \PhpCss\Scanner\Status\Text\Double::getToken
     * @dataProvider getTokenDataProvider
     */
    public function testGetToken($string, $expectedToken): void {
      $status = new Double();
      $this->assertEquals(
        $status->getToken($string, 0),
        $expectedToken
      );
    }

    /**
     * @covers \PhpCss\Scanner\Status\Text\Double::isEndToken
     */
    public function testIsEndToken(): void {
      $status = new Double();
      $this->assertTrue(
        $status->isEndToken(
          new Scanner\Token(
            Scanner\Token::DOUBLEQUOTE_STRING_END, '"', 0
          )
        )
      );
    }

    /**
     * @covers \PhpCss\Scanner\Status\Text\Double::getNewStatus
     */
    public function testGetNewStatus(): void {
      $status = new Double();
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
        'double quote string end' => [
          '"',
          new Scanner\Token(
            Scanner\Token::DOUBLEQUOTE_STRING_END, '"', 0
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
