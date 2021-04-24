<?php
namespace PhpCss\Parser {

  use PhpCss;
  use PhpCss\Ast;
  use PhpCss\Scanner;

  require_once(__DIR__.'/../../bootstrap.php');

  class SequenceTest extends \PHPUnit\Framework\TestCase {

    /**
    * @covers \PhpCss\Parser\Sequence
    * @dataProvider provideParseData
    */
    public function testParse($expected, $tokens) {
      $parser = new Sequence($tokens);
      $this->assertEquals(
        $expected, $parser->parse()
      );
    }

    /**
    * @covers \PhpCss\Parser\Sequence
    * @dataProvider provideInvalidParseData
    */
    public function testParseExpectingException($tokens) {
      $parser = new Sequence($tokens);
      $this->expectException(PhpCss\Exception\TokenMismatchException::CLASS);
      $parser->parse();
    }

    public static function provideParseData() {
      return array(
        'universal with namespace' => array(
          new Ast\Selector\Sequence(
            array(new Ast\Selector\Simple\Universal('ns'))
          ),
          array(
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'ns|*',
              0
            )
          )
        ),
        'element' => array(
          new Ast\Selector\Sequence(
            array(new Ast\Selector\Simple\Type('element'))
          ),
          array(
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'element',
              0
            )
          )
        ),
        'element with prefix' => array(
          new Ast\Selector\Sequence(
            array(new Ast\Selector\Simple\Type('element', 'prefix'))
          ),
          array(
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'prefix|element',
              0
            )
          )
        ),
        'class' => array(
          new Ast\Selector\Sequence(
            array(new Ast\Selector\Simple\ClassName('classname'))
          ),
          array(
            new Scanner\Token(
              Scanner\Token::CLASS_SELECTOR,
              '.classname',
              0
            )
          )
        ),
        'id' => array(
          new Ast\Selector\Sequence(
            array(new Ast\Selector\Simple\Id('id'))
          ),
          array(
            new Scanner\Token(
              Scanner\Token::ID_SELECTOR,
              '#id',
              0
            )
          )
        ),
        'element.class' => array(
          new Ast\Selector\Sequence(
            array(
              new Ast\Selector\Simple\Type('element'),
              new Ast\Selector\Simple\ClassName('classname')
            )
          ),
          array(
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'element',
              0
            ),
            new Scanner\Token(
              Scanner\Token::CLASS_SELECTOR,
              '.classname',
              7
            )
          )
        ),
        'element > child' => array(
          new Ast\Selector\Sequence(
            array(
              new Ast\Selector\Simple\Type('element')
            ),
            new Ast\Selector\Combinator\Child(
              new Ast\Selector\Sequence(
                array(
                  new Ast\Selector\Simple\Type('child')
                )
              )
            )
          ),
          array(
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'element',
              0
            ),
            new Scanner\Token(
              Scanner\Token::COMBINATOR,
              '>',
              7
            ),
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'child',
              8
            )
          )
        ),
        'element child' => array(
          new Ast\Selector\Sequence(
            array(
              new Ast\Selector\Simple\Type('element')
            ),
            new Ast\Selector\Combinator\Descendant(
              new Ast\Selector\Sequence(
                array(
                  new Ast\Selector\Simple\Type('child')
                )
              )
            )
          ),
          array(
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'element',
              0
            ),
            new Scanner\Token(
              Scanner\Token::WHITESPACE,
              ' ',
              7
            ),
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'child',
              8
            )
          )
        )
      );
    }

    public static function provideInvalidParseData() {
      return array(
        'two elements' => array(
          array(
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'element',
              0
            ),
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'element',
              7
            )
          )
        ),
        'element after class' => array(
          array(
            new Scanner\Token(
              Scanner\Token::CLASS_SELECTOR,
              '.classname',
              0
            ),
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'element',
              10
            )
          )
        )
      );
    }
  }
}
