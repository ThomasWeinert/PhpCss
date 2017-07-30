<?php
namespace PhpCss\Scanner\Status\Selector {

  use PhpCss\Scanner;

  require_once(__DIR__.'/../../../../bootstrap.php');

  class AttributeTest extends \PHPUnit\Framework\TestCase {

    /**
    * @covers \PhpCss\Scanner\Status\Selector\Attribute::getToken
    * @dataProvider getTokenDataProvider
    */
    public function testGetToken($string, $expectedToken) {
      $status = new Attribute();
      $this->assertEquals(
        $status->getToken($string, 0),
        $expectedToken
      );
    }

    /**
    * @covers \PhpCss\Scanner\Status\Selector\Attribute::isEndToken
    */
    public function testIsEndToken() {
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
    * @covers \PhpCss\Scanner\Status\Selector\Attribute::getNewStatus
    * @dataProvider getNewStatusDataProvider
    */
    public function testGetNewStatus($token, $expectedStatus) {
      $status = new Attribute();
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
        'attributes end' => array(
          "]",
          new Scanner\Token(
            Scanner\Token::ATTRIBUTE_SELECTOR_END, "]", 0
          )
        ),
        'attribute-name' => array(
          "attribute-name",
          new Scanner\Token(
            Scanner\Token::IDENTIFIER, "attribute-name", 0
          )
        ),
        'attribute operator' => array(
          "=",
          new Scanner\Token(
            Scanner\Token::ATTRIBUTE_OPERATOR, "=", 0
          )
        ),
        'single quote string start' => array(
          "'",
          new Scanner\Token(
            Scanner\Token::SINGLEQUOTE_STRING_START, "'", 0
          )
        ),
        'double quote string start' => array(
          '"',
          new Scanner\Token(
            Scanner\Token::DOUBLEQUOTE_STRING_START, '"', 0
          )
        )
      );
    }

    public static function getNewStatusDataProvider() {
      return array(
        'whitespaces - no new status' => array(
          new Scanner\Token(
            Scanner\Token::WHITESPACE, " ", 0
          ),
          NULL
        ),
        'single quote string start' => array(
          new Scanner\Token(
            Scanner\Token::SINGLEQUOTE_STRING_START, "'", 0
          ),
          new Scanner\Status\Text\Single()
        ),
        'double quote string start' => array(
          new Scanner\Token(
            Scanner\Token::DOUBLEQUOTE_STRING_START, "'", 0
          ),
          new Scanner\Status\Text\Double()
        )
      );
    }
  }
}
