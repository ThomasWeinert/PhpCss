<?php
namespace PhpCss\Scanner\Status\String {

  use PhpCss\Scanner;

  require_once(__DIR__.'/../../../../bootstrap.php');

  class DoubleTest extends \PHPUnit_Framework_TestCase {

    /**
    * @covers PhpCss\Scanner\Status\String\Double::getToken
    * @dataProvider getTokenDataProvider
    */
    public function testGetToken($string, $expectedToken) {
      $status = new Double();
      $this->assertEquals(
        $status->getToken($string, 0),
        $expectedToken
      );
    }

    /**
    * @covers PhpCss\Scanner\Status\String\Double::isEndToken
    */
    public function testIsEndToken() {
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
    * @covers PhpCss\Scanner\Status\String\Double::getNewStatus
    */
    public function testGetNewStatus() {
      $status = new Double();
      $this->assertNULL(
         $status->getNewStatus($this->getMock(Scanner\Token::CLASS))
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
        'double quote string end' => array(
          '"',
          new Scanner\Token(
            Scanner\Token::DOUBLEQUOTE_STRING_END, '"', 0
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