<?php
namespace PhpCss\Scanner\Status {

  use PhpCss\Scanner;

  require_once(__DIR__.'/../../../bootstrap.php');

  class SelectorTest extends \PHPUnit_Framework_TestCase {

    /**
    * @covers PhpCss\Scanner\Status\Selector::getToken
    * @dataProvider getTokenDataProvider
    */
    public function testGetToken($string, $expectedToken) {
      $status = new Selector();
      $this->assertEquals(
        $status->getToken($string, 0),
        $expectedToken
      );
    }

    /**
    * @covers PhpCss\Scanner\Status\Selector::isEndToken
    */
    public function testIsEndToken() {
      $status = new Selector();
      $this->assertFalse(
        $status->isEndToken(
          new Scanner\Token(Scanner\Token::WHITESPACE, ' ', 42)
        )
      );
    }

    /**
    * @covers PhpCss\Scanner\Status\Selector::getNewStatus
    * @dataProvider getNewStatusDataProvider
    */
    public function testGetNewStatus($token, $expectedStatus) {
      $status = new Selector();
      $this->assertEquals(
        $status->getNewStatus($token),
        $expectedStatus
      );
    }


    /*****************************
    * Data provider
    *****************************/

    public static function getTokenDataProvider() {
      return array(
        'empty' => array(
          '',
          NULL
        ),
        'type selector' => array(
          'tag',
          new Scanner\Token(Scanner\Token::IDENTIFIER, "tag", 0)
        ),
        'id selector' => array(
          '#id',
          new Scanner\Token(Scanner\Token::ID_SELECTOR, "#id", 0)
        ),
        'class selector' => array(
          '.class',
          new Scanner\Token(Scanner\Token::CLASS_SELECTOR, ".class", 0)
        ),
        'single quote string start' => array(
          "'test'",
          new Scanner\Token(Scanner\Token::SINGLEQUOTE_STRING_START, "'", 0)
        ),
        'double quote string start' => array(
          '"test"',
          new Scanner\Token(Scanner\Token::DOUBLEQUOTE_STRING_START, '"', 0)
        ),
        'attributes start' => array(
          "[attr]",
          new Scanner\Token(Scanner\Token::ATTRIBUTE_SELECTOR_START, "[", 0)
        )
      );
    }

    public static function getNewStatusDataProvider() {
      return array(
        'whitespaces - no new status' => array(
          new Scanner\Token(Scanner\Token::WHITESPACE, " ", 0),
          NULL
        ),
        'single quote string start' => array(
          new Scanner\Token(Scanner\Token::SINGLEQUOTE_STRING_START, "'", 0),
          new Scanner\Status\Text\Single()
        ),
        'double quote string start' => array(
          new Scanner\Token(Scanner\Token::DOUBLEQUOTE_STRING_START, "'", 0),
          new Scanner\Status\Text\Double()
        ),
        'attributes selector start' => array(
          new Scanner\Token(Scanner\Token::ATTRIBUTE_SELECTOR_START, "[", 0),
          new Scanner\Status\Selector\Attribute()
        )
      );
    }
  }
}
