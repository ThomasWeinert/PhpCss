<?php
namespace PhpCss\Parser {

  use PhpCss;
  use PhpCss\Ast;
  use PhpCss\Scanner;

  require_once(__DIR__.'/../../bootstrap.php');

  class AttributeTest extends \PHPUnit_Framework_TestCase {

    /**
    * @covers PhpCss\Parser\Attribute
    * @dataProvider provideParseData
    */
    public function testParse($expected, $tokens) {
      $parser = new Attribute($tokens);
      $this->assertEquals(
        $expected, $parser->parse()
      );
    }

    /**
    * @covers PhpCss\Parser\Attribute
    * @dataProvider provideInvalidParseData
    */
    public function testParseExpectingException($tokens) {
      $parser = new Attribute($tokens);
      $this->setExpectedException(PhpCss\Exception\TokenMismatch::CLASS);
      $parser->parse();
    }

    public static function provideParseData() {
      return array(
        'simple identifier' => array(
          new Ast\Selector\Simple\Attribute('attr'),
          array(
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'attr',
              0
            ),
            new Scanner\Token(
              Scanner\Token::ATTRIBUTE_SELECTOR_END,
              ']',
              4
            )
          )
        ),
        'class attribute' => array(
          new Ast\Selector\Simple\Attribute(
            'class',
            Ast\Selector\Simple\Attribute::MATCH_INCLUDES,
            'warning'
          ),
          array(
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'class',
              0
            ),
            new Scanner\Token(
              Scanner\Token::ATTRIBUTE_OPERATOR,
              '~=',
              5
            ),
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'warning',
              7
            ),
            new Scanner\Token(
              Scanner\Token::ATTRIBUTE_SELECTOR_END,
              ']',
              14
            )
          )
        )
      );
    }
    public static function provideInvalidParseData() {
      return array(
        'identifer followed by string start' => array(
          array(
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'attr',
              0
            ),
            new Scanner\Token(
              Scanner\Token::SINGLEQUOTE_STRING_START,
              "'",
              4
            )
          )
        )
      );
    }
  }
}
