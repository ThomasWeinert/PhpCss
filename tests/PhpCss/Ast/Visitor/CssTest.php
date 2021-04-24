<?php

namespace PhpCss\Ast\Visitor {

  use PhpCss\Ast;
  use PHPUnit\Framework\TestCase;

  require_once(__DIR__.'/../../../bootstrap.php');

  class CssTest extends TestCase {

    /**
     * @covers       \PhpCss\Ast\Visitor\Css
     * @dataProvider provideExamples
     */
    public function testIntegration($expected, Ast\Node $node): void {
      $visitor = new Css();
      $node->accept($visitor);
      $this->assertEquals(
        $expected, (string)$visitor
      );
    }

    public static function provideExamples(): array {
      return [
        [
          'ns|*',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [new Ast\Selector\Simple\Universal('ns')]
              ),
            ]
          ),
        ],
        [
          'element, #id, .class',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [new Ast\Selector\Simple\Type('element')]
              ),
              new Ast\Selector\Sequence(
                [new Ast\Selector\Simple\Id('id')]
              ),
              new Ast\Selector\Sequence(
                [new Ast\Selector\Simple\ClassName('class')]
              ),
            ]
          ),
        ],
        [
          'element > child',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [
                  new Ast\Selector\Simple\Type('element'),
                  new Ast\Selector\Combinator\Child(
                    new Ast\Selector\Sequence(
                      [new Ast\Selector\Simple\Type('child')]
                    )
                  ),
                ]
              ),
            ]
          ),
        ],
        [
          '+ p',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [
                ],
                new Ast\Selector\Combinator\Next(
                  new Ast\Selector\Sequence(
                    [
                      new Ast\Selector\Simple\Type('p'),
                    ]
                  )
                )
              ),
            ]
          ),
        ],
      ];
    }
  }
}
