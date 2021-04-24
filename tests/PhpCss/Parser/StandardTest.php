<?php

namespace PhpCss\Parser {

  use PhpCss;
  use PhpCss\Ast;
  use PhpCss\Scanner;
  use PHPUnit\Framework\TestCase;

  require_once(__DIR__.'/../../bootstrap.php');

  class StandardTest extends TestCase {

    /**
     * @covers       \PhpCss\Parser\Standard::parse
     * @dataProvider provideParseData
     */
    public function testParse($expected, $tokens): void {
      $parser = new Standard($tokens, Standard::ALLOW_RELATIVE_SELECTORS);
      $this->assertEquals(
        $expected, $parser->parse()
      );
    }

    public static function provideParseData(): array {
      return [
        'empty group' => [
          new Ast\Selector\Group(),
          [],
        ],
        'element' => [
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [new Ast\Selector\Simple\Type('element')]
              ),
            ]
          ),
          [
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'element',
              0
            ),
          ],
        ],
        'two whitespaces and an element' => [
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [new Ast\Selector\Simple\Type('element')]
              ),
            ]
          ),
          [
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
            ),
          ],
        ],
        "combinator and element" => [
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [
                ],
                new PhpCss\Ast\Selector\Combinator\Next(
                  new Ast\Selector\Sequence(
                    [
                      new Ast\Selector\Simple\Type('p'),
                    ]
                  )
                )
              ),
            ]
          ),
          [
            new Scanner\Token(
              Scanner\Token::COMBINATOR,
              ' + ',
              0
            ),
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'p',
              3
            ),
          ],
        ],
      ];
    }
  }
}
