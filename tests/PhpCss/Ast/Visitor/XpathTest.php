<?php

namespace PhpCss\Ast\Visitor {

  use PhpCss\Ast;
  use PhpCss\Exception;
  use PHPUnit\Framework\TestCase;

  require_once(__DIR__.'/../../../bootstrap.php');

  class XpathTest extends TestCase {

    /**
     * @param $option
     * @param $options
     * @dataProvider provideIncludedOptionsExamples
     */
    public function testHasOptionExpectingTrue($option, $options): void {
      $xpath = new Xpath($options);
      $this->assertTrue($xpath->hasOption($option));
    }

    public static function provideIncludedOptionsExamples(): array {
      return [
        [Xpath::OPTION_EXPLICIT_NAMESPACES, Xpath::OPTION_EXPLICIT_NAMESPACES],
        [Xpath::OPTION_USE_DOCUMENT_CONTEXT, Xpath::OPTION_USE_DOCUMENT_CONTEXT],
        [
          Xpath::OPTION_EXPLICIT_NAMESPACES,
          Xpath::OPTION_EXPLICIT_NAMESPACES | Xpath::OPTION_USE_DOCUMENT_CONTEXT,
        ],
        [
          Xpath::OPTION_USE_DOCUMENT_CONTEXT,
          Xpath::OPTION_EXPLICIT_NAMESPACES | Xpath::OPTION_USE_DOCUMENT_CONTEXT,
        ],
      ];
    }

    /**
     * @param $option
     * @param $options
     * @dataProvider provideExcludedOptionsExamples
     */
    public function testHasOptionExpectingFalse($option, $options): void {
      $xpath = new Xpath($options);
      $this->assertFalse($xpath->hasOption($option));
    }

    public static function provideExcludedOptionsExamples(): array {
      return [
        [Xpath::OPTION_EXPLICIT_NAMESPACES, Xpath::OPTION_USE_DOCUMENT_CONTEXT],
        [Xpath::OPTION_USE_DOCUMENT_CONTEXT, Xpath::OPTION_EXPLICIT_NAMESPACES],
        [
          Xpath::OPTION_EXPLICIT_NAMESPACES,
          0,
        ],
        [
          Xpath::OPTION_USE_DOCUMENT_CONTEXT,
          0,
        ],
      ];
    }

    /**
     * @covers \PhpCss\Ast\Visitor\Xpath
     * @dataProvider provideNotConvertibleExamples
     * @param Ast\Node $node
     */
    public function testNotConvertibleElements(Ast\Node $node): void {
      $visitor = new Xpath();
      $this->expectException(Exception\NotConvertibleException::CLASS);
      $node->accept($visitor);
    }

    public static function provideNotConvertibleExamples(): array {
      return [
        ':link' => [new Ast\Selector\Simple\PseudoClass('link')],
        ':visited' => [new Ast\Selector\Simple\PseudoClass('visited')],
        ':hover' => [new Ast\Selector\Simple\PseudoClass('hover')],
        ':first-line' => [new Ast\Selector\Simple\PseudoElement('first-line')],
      ];
    }

    /**
     * @covers \PhpCss\Ast\Visitor\Xpath
     * @dataProvider provideExamples
     */
    public function testIntegration($expected, Ast\Node $ast, $options = 0): void {
      $visitor = new Xpath((int)$options);
      $ast->accept($visitor);
      $this->assertEquals(
        $expected, (string)$visitor
      );
    }

    public static function provideExamples(): array {
      return [
        '*' => [
          './/*',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [new Ast\Selector\Simple\Universal('*')]
              ),
            ]
          ),
        ],
        'element, default xmlns' => [
          './/*[(self::element or self::html:element)]',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [new Ast\Selector\Simple\Type('element')]
              ),
            ]
          ),
          Ast\Visitor\Xpath::OPTION_DEFAULT_NAMESPACE,
        ],
        'element' => [
          './/*[local-name() = "element"]',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [new Ast\Selector\Simple\Type('element')]
              ),
            ]
          ),
        ],
        'element, self context' => [
          'descendant-or-self::*[local-name() = "element"]',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [new Ast\Selector\Simple\Type('element')]
              ),
            ]
          ),
          Ast\Visitor\Xpath::OPTION_USE_CONTEXT_SELF,
        ],
        'element, self context limit' => [
          'self::*[local-name() = "element"]',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [new Ast\Selector\Simple\Type('element')]
              ),
            ]
          ),
          Ast\Visitor\Xpath::OPTION_USE_CONTEXT_SELF_LIMIT,
        ],
        'element, #id' => [
          './/*[local-name() = "element"]|.//*[@id = "id"]',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [new Ast\Selector\Simple\Type('element')]
              ),
              new Ast\Selector\Sequence(
                [new Ast\Selector\Simple\Id('id')]
              ),
            ]
          ),
        ],
        'element.class' => [
          './/*[local-name() = "element" and contains(concat(" ", normalize-space(@class), " "), " class ")]',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [
                  new Ast\Selector\Simple\Type('element'),
                  new Ast\Selector\Simple\ClassName('class'),
                ]
              ),
            ]
          ),
        ],
        '.class' => [
          './/*[contains(concat(" ", normalize-space(@class), " "), " class ")]',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [
                  new Ast\Selector\Simple\ClassName('class'),
                ]
              ),
            ]
          ),
        ],
        '#someId' => [
          './/*[@id = "someId"]',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [
                  new Ast\Selector\Simple\Id('someId'),
                ]
              ),
            ]
          ),
        ],
        '[attr]' => [
          './/*[@attr]',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [
                  new Ast\Selector\Simple\Attribute(
                    'attr', Ast\Selector\Simple\Attribute::MATCH_EXISTS
                  ),
                ]
              ),
            ]
          ),
        ],
        '[attr = "value"]' => [
          './/*[@attr = "value"]',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [
                  new Ast\Selector\Simple\Attribute(
                    'attr', Ast\Selector\Simple\Attribute::MATCH_EQUALS, 'value'
                  ),
                ]
              ),
            ]
          ),
        ],
        '[attr = "some value"]' => [
          './/*[@attr = "some value"]',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [
                  new Ast\Selector\Simple\Attribute(
                    'attr', Ast\Selector\Simple\Attribute::MATCH_EQUALS, 'some value'
                  ),
                ]
              ),
            ]
          ),
        ],
        '[attr^="value"]' => [
          './/*[starts-with(@attr, "value")]',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [
                  new Ast\Selector\Simple\Attribute(
                    'attr', Ast\Selector\Simple\Attribute::MATCH_PREFIX, 'value'
                  ),
                ]
              ),
            ]
          ),
        ],
        '[attr~="value"]' => [
          './/*[contains(concat(" ", normalize-space(@attr), " "), " value ")]',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [
                  new Ast\Selector\Simple\Attribute(
                    'attr', Ast\Selector\Simple\Attribute::MATCH_INCLUDES, 'value'
                  ),
                ]
              ),
            ]
          ),
        ],
        '[attr$="value"]' => [
          './/*[substring(@attr, string-length(@attr) - 5) = "value"]',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [
                  new Ast\Selector\Simple\Attribute(
                    'attr', Ast\Selector\Simple\Attribute::MATCH_SUFFIX, 'value'
                  ),
                ]
              ),
            ]
          ),
        ],
        '[attr*="value"]' => [
          './/*[contains(@attr, "value")]',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [
                  new Ast\Selector\Simple\Attribute(
                    'attr', Ast\Selector\Simple\Attribute::MATCH_SUBSTRING, 'value'
                  ),
                ]
              ),
            ]
          ),
        ],
        '[attr|="value"]' => [
          './/*[(@attr = "value" or substring-before(@attr, "-") = "value")]',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [
                  new Ast\Selector\Simple\Attribute(
                    'attr', Ast\Selector\Simple\Attribute::MATCH_DASHMATCH, 'value'
                  ),
                ]
              ),
            ]
          ),
        ],
        'E F' => [
          './/*[local-name() = "E"]//*[local-name() = "F"]',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [
                  new Ast\Selector\Simple\Type('E'),
                ],
                new Ast\Selector\Combinator\Descendant(
                  new Ast\Selector\Sequence(
                    [
                      new Ast\Selector\Simple\Type('F'),
                    ]
                  )
                )
              ),
            ]
          ),
        ],
        'E > F' => [
          './/*[local-name() = "E"]/*[local-name() = "F"]',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [
                  new Ast\Selector\Simple\Type('E'),
                ],
                new Ast\Selector\Combinator\Child(
                  new Ast\Selector\Sequence(
                    [
                      new Ast\Selector\Simple\Type('F'),
                    ]
                  )
                )
              ),
            ]
          ),
        ],
        'E ~ F' => [
          './/*[local-name() = "E"]/following-sibling::*[local-name() = "F"]',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [
                  new Ast\Selector\Simple\Type('E'),
                ],
                new Ast\Selector\Combinator\Follower(
                  new Ast\Selector\Sequence(
                    [
                      new Ast\Selector\Simple\Type('F'),
                    ]
                  )
                )
              ),
            ]
          ),
        ],
        'E + F' => [
          './/*[local-name() = "E"]/following-sibling::*[1]/self::*[local-name() = "F"]',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [
                  new Ast\Selector\Simple\Type('E'),
                ],
                new Ast\Selector\Combinator\Next(
                  new Ast\Selector\Sequence(
                    [
                      new Ast\Selector\Simple\Type('F'),
                    ]
                  )
                )
              ),
            ]
          ),
        ],
        'E:not(s)' => [
          './/*[local-name() = "E" and not(local-name() = "s")]',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [
                  new Ast\Selector\Simple\Type('E'),
                  new Ast\Selector\Simple\PseudoClass(
                    'not',
                    new Ast\Selector\Simple\Type('s')
                  ),
                ]
              ),
            ]
          ),
        ],
        ':nth-child(3n+1)' => [
          './/*[((position() mod 3) = 1)]',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [
                  new Ast\Selector\Simple\PseudoClass(
                    'nth-child',
                    new Ast\Value\Position(3, 1)
                  ),
                ]
              ),
            ]
          ),
        ],
        ':nth-child(3n-1)' => [
          './/*[((position() mod 3) = 2)]',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [
                  new Ast\Selector\Simple\PseudoClass(
                    'nth-child',
                    new Ast\Value\Position(3, -1)
                  ),
                ]
              ),
            ]
          ),
        ],
        ':nth-child(42)' => [
          './/*[(position() = 42)]',
          new Ast\Selector\Group(
            [
              new Ast\Selector\Sequence(
                [
                  new Ast\Selector\Simple\PseudoClass(
                    'nth-child',
                    new Ast\Value\Position(0, 42)
                  ),
                ]
              ),
            ]
          ),
        ],
        ' + p' => [
          'following-sibling::*[1]/self::*[local-name() = "p"]',
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
