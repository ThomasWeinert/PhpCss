<?php

namespace PhpCss\Scanner\Status\Selector {

  use PhpCss\Scanner;
  use PHPUnit\Framework\TestCase;

  require_once(__DIR__.'/../../../../bootstrap.php');

  class AttributeTest extends TestCase {

    /**
     * @covers       \PhpCss\Scanner\Status\Selector\Attribute::getToken
     * @dataProvider getTokenDataProvider
     */
    public function testGetToken($string, $expectedToken): void {
      $status = new Attribute();
      $this->assertEquals(
        $status->getToken($string, 0),
        $expectedToken
      );
    }

    /**
     * @covers \PhpCss\Scanner\Status\Selector\Attribute::isEndToken
     */
    public function testIsEndToken(): void {
      $status = new Attribute();
      $this->assertTrue(
        $status->isEndToken(
          new Scanner\Token(
            Scanner\Token::ATTRIBUTE_SELECTOR_END, "]", 0
          )
        )
      );
    }

    /**
     * @covers       \PhpCss\Scanner\Status\Selector\Attribute::getNewStatus
     * @dataProvider getNewStatusDataProvider
     */
    public function testGetNewStatus($token, $expectedStatus): void {
      $status = new Attribute();
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
        'attributes end' => [
          "]",
          new Scanner\Token(
            Scanner\Token::ATTRIBUTE_SELECTOR_END, "]", 0
          ),
        ],
        'attribute-name' => [
          "attribute-name",
          new Scanner\Token(
            Scanner\Token::IDENTIFIER, "attribute-name", 0
          ),
        ],
        'attribute operator' => [
          "=",
          new Scanner\Token(
            Scanner\Token::ATTRIBUTE_OPERATOR, "=", 0
          ),
        ],
        'single quote string start' => [
          "'",
          new Scanner\Token(
            Scanner\Token::SINGLEQUOTE_STRING_START, "'", 0
          ),
        ],
        'double quote string start' => [
          '"',
          new Scanner\Token(
            Scanner\Token::DOUBLEQUOTE_STRING_START, '"', 0
          ),
        ],
      ];
    }

    public static function getNewStatusDataProvider(): array {
      return [
        'whitespaces - no new status' => [
          new Scanner\Token(
            Scanner\Token::WHITESPACE, " ", 0
          ),
          NULL,
        ],
        'single quote string start' => [
          new Scanner\Token(
            Scanner\Token::SINGLEQUOTE_STRING_START, "'", 0
          ),
          new Scanner\Status\Text\Single(),
        ],
        'double quote string start' => [
          new Scanner\Token(
            Scanner\Token::DOUBLEQUOTE_STRING_START, "'", 0
          ),
          new Scanner\Status\Text\Double(),
        ],
      ];
    }
  }
}
