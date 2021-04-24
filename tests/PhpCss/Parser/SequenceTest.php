<?php

namespace PhpCss\Parser {

  use PhpCss;
  use PhpCss\Ast;
  use PhpCss\Scanner;
  use PHPUnit\Framework\TestCase;

  require_once(__DIR__.'/../../bootstrap.php');

  class SequenceTest extends TestCase {

    /**
     * @covers       \PhpCss\Parser\Sequence
     * @dataProvider provideParseData
     */
    public function testParse($expected, $tokens): void {
      $parser = new Sequence($tokens);
      $this->assertEquals(
        $expected, $parser->parse()
      );
    }

    /**
     * @covers       \PhpCss\Parser\Sequence
     * @dataProvider provideInvalidParseData
     */
    public function testParseExpectingException($tokens): void {
      $parser = new Sequence($tokens);
      $this->expectException(PhpCss\Exception\TokenMismatchException::CLASS);
      $parser->parse();
    }

    public static function provideParseData(): array {
      return [
        'universal with namespace' => [
          new Ast\Selector\Sequence(
            [new Ast\Selector\Simple\Universal('ns')]
          ),
          [
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'ns|*',
              0
            ),
          ],
        ],
        'element' => [
          new Ast\Selector\Sequence(
            [new Ast\Selector\Simple\Type('element')]
          ),
          [
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'element',
              0
            ),
          ],
        ],
        'element with prefix' => [
          new Ast\Selector\Sequence(
            [new Ast\Selector\Simple\Type('element', 'prefix')]
          ),
          [
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'prefix|element',
              0
            ),
          ],
        ],
        'class' => [
          new Ast\Selector\Sequence(
            [new Ast\Selector\Simple\ClassName('classname')]
          ),
          [
            new Scanner\Token(
              Scanner\Token::CLASS_SELECTOR,
              '.classname',
              0
            ),
          ],
        ],
        'id' => [
          new Ast\Selector\Sequence(
            [new Ast\Selector\Simple\Id('id')]
          ),
          [
            new Scanner\Token(
              Scanner\Token::ID_SELECTOR,
              '#id',
              0
            ),
          ],
        ],
        'element.class' => [
          new Ast\Selector\Sequence(
            [
              new Ast\Selector\Simple\Type('element'),
              new Ast\Selector\Simple\ClassName('classname'),
            ]
          ),
          [
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'element',
              0
            ),
            new Scanner\Token(
              Scanner\Token::CLASS_SELECTOR,
              '.classname',
              7
            ),
          ],
        ],
        'element > child' => [
          new Ast\Selector\Sequence(
            [
              new Ast\Selector\Simple\Type('element'),
            ],
            new Ast\Selector\Combinator\Child(
              new Ast\Selector\Sequence(
                [
                  new Ast\Selector\Simple\Type('child'),
                ]
              )
            )
          ),
          [
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
            ),
          ],
        ],
        'element child' => [
          new Ast\Selector\Sequence(
            [
              new Ast\Selector\Simple\Type('element'),
            ],
            new Ast\Selector\Combinator\Descendant(
              new Ast\Selector\Sequence(
                [
                  new Ast\Selector\Simple\Type('child'),
                ]
              )
            )
          ),
          [
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
            ),
          ],
        ],
      ];
    }

    public static function provideInvalidParseData(): array {
      return [
        'two elements' => [
          [
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'element',
              0
            ),
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'element',
              7
            ),
          ],
        ],
        'element after class' => [
          [
            new Scanner\Token(
              Scanner\Token::CLASS_SELECTOR,
              '.classname',
              0
            ),
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'element',
              10
            ),
          ],
        ],
      ];
    }
  }
}
