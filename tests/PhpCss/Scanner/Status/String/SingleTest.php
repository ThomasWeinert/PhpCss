<?php
namespace PhpCss\Scanner\Status\String {

  use PhpCss\Scanner;

  require_once(__DIR__.'/../../../../bootstrap.php');

  class SingleTest extends \PHPUnit_Framework_TestCase {

    /**
    * @covers PhpCss\Scanner\Status\String\Single::getToken
    * @dataProvider getTokenDataProvider
    */
    public function testGetToken($string, $expectedToken) {
      $status = new Single();
      $this->assertEquals(
        $status->getToken($string, 0),
        $expectedToken
      );
    }

    /**
    * @covers PhpCss\Scanner\Status\String\Single::isEndToken
    */
    public function testIsEndToken() {
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
    * @covers PhpCss\Scanner\Status\String\Single::getNewStatus
    */
    public function testGetNewStatus() {
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

    public static function getTokenDataProvider() {
      return array(
        'empty' => array(
          '',
          NULL
        ),
        'single quote string end' => array(
          "'",
          new Scanner\Token(
            Scanner\Token::SINGLEQUOTE_STRING_END, "'", 0
          )
        ),
        'escaped backslash' => array(
          '\\\\',
          new Scanner\Token(
            Scanner\Token::STRING_ESCAPED_CHARACTER, '\\\\', 0
          )
        ),
        'string chars' => array(
          'abcd',
          new Scanner\Token(
            Scanner\Token::STRING_CHARACTERS, 'abcd', 0
          )
        )
      );
    }
  }
}
