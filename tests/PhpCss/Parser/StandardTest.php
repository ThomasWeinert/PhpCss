<?php
namespace PhpCss\Parser {

  use PhpCss;
  use PhpCss\Ast;
  use PhpCss\Scanner;

  require_once(__DIR__.'/../../bootstrap.php');

  class StandardTest extends \PHPUnit_Framework_TestCase {

    /**
    * @covers PhpCss\Parser\Standard::parse
    * @dataProvider provideParseData
    */
    public function testParse($expected, $tokens) {
      $parser = new Standard($tokens);
      $this->assertEquals(
        $expected, $parser->parse()
      );
    }

    public static function provideParseData() {
      return array(
        'empty group' => array(
          new Ast\Selector\Group(),
          array()
        ),
        'element' => array(
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(new Ast\Selector\Simple\Type('element'))
              )
            )
          ),
          array(
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'element',
              0
            )
          )
        ),
        'two whitespaces and an element' => array(
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(new Ast\Selector\Simple\Type('element'))
              )
            )
          ),
          array(
            new Scanner\Token(
              Scanner\Token::WHITESPACE,
              '   ',
              0
            ),
            new Scanner\Token(
              Scanner\Token::WHITESPACE,
              '  ',
              3
            ),
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'element',
              5
            )
          )
        )
      );
    }
  }
}
