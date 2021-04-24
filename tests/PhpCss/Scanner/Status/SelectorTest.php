<?php

namespace PhpCss\Scanner\Status {

  use PhpCss\Scanner;
  use PHPUnit\Framework\TestCase;

  require_once(__DIR__.'/../../../bootstrap.php');

  class SelectorTest extends TestCase {

    /**
     * @covers       \PhpCss\Scanner\Status\Selector::getToken
     * @dataProvider getTokenDataProvider
     */
    public function testGetToken($string, $expectedToken): void {
      $status = new Selector();
      $this->assertEquals(
        $status->getToken($string, 0),
        $expectedToken
      );
    }

    /**
     * @covers \PhpCss\Scanner\Status\Selector::isEndToken
     */
    public function testIsEndToken(): void {
      $status = new Selector();
      $this->assertFalse(
        $status->isEndToken(
          new Scanner\Token(Scanner\Token::WHITESPACE, ' ', 42)
        )
      );
    }

    /**
     * @covers       \PhpCss\Scanner\Status\Selector::getNewStatus
     * @dataProvider getNewStatusDataProvider
     */
    public function testGetNewStatus($token, $expectedStatus): void {
      $status = new Selector();
      $this->assertEquals(
        $status->getNewStatus($token),
        $expectedStatus
      );
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
        'type selector' => [
          'tag',
          new Scanner\Token(Scanner\Token::IDENTIFIER, "tag", 0),
        ],
        'id selector' => [
          '#id',
          new Scanner\Token(Scanner\Token::ID_SELECTOR, "#id", 0),
        ],
        'class selector' => [
          '.class',
          new Scanner\Token(Scanner\Token::CLASS_SELECTOR, ".class", 0),
        ],
        'single quote string start' => [
          "'test'",
          new Scanner\Token(Scanner\Token::SINGLEQUOTE_STRING_START, "'", 0),
        ],
        'double quote string start' => [
          '"test"',
          new Scanner\Token(Scanner\Token::DOUBLEQUOTE_STRING_START, '"', 0),
        ],
        'attributes start' => [
          "[attr]",
          new Scanner\Token(Scanner\Token::ATTRIBUTE_SELECTOR_START, "[", 0),
        ],
      ];
    }

    public static function getNewStatusDataProvider(): array {
      return [
        'whitespaces - no new status' => [
          new Scanner\Token(Scanner\Token::WHITESPACE, " ", 0),
          NULL,
        ],
        'single quote string start' => [
          new Scanner\Token(Scanner\Token::SINGLEQUOTE_STRING_START, "'", 0),
          new Scanner\Status\Text\Single(),
        ],
        'double quote string start' => [
          new Scanner\Token(Scanner\Token::DOUBLEQUOTE_STRING_START, "'", 0),
          new Scanner\Status\Text\Double(),
        ],
        'attributes selector start' => [
          new Scanner\Token(Scanner\Token::ATTRIBUTE_SELECTOR_START, "[", 0),
          new Scanner\Status\Selector\Attribute(),
        ],
      ];
    }
  }
}
