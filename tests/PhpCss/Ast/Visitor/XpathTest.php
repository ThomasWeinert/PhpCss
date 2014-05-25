<?php
namespace PhpCss\Ast\Visitor {

  use PhpCss\Ast;
  use PhpCss\Exception;

  require_once(__DIR__.'/../../../bootstrap.php');

  class XpathTest extends \PHPUnit_Framework_TestCase {

    /**
     * @param $option
     * @param $options
     * @dataProvider provideIncludedOptionsExamples
     */
    public function testHasOptionExpectingTrue($option, $options) {
      $xpath = new Xpath($options);
      $this->assertTrue($xpath->hasOption($option));
    }

    public static function provideIncludedOptionsExamples() {
      return [
        [Xpath::OPTION_EXPLICT_NAMESPACES, Xpath::OPTION_EXPLICT_NAMESPACES],
        [Xpath::OPTION_USE_DOCUMENT_CONTEXT, Xpath::OPTION_USE_DOCUMENT_CONTEXT],
        [
          Xpath::OPTION_EXPLICT_NAMESPACES,
          Xpath::OPTION_EXPLICT_NAMESPACES | Xpath::OPTION_USE_DOCUMENT_CONTEXT
        ],
        [
          Xpath::OPTION_USE_DOCUMENT_CONTEXT,
          Xpath::OPTION_EXPLICT_NAMESPACES | Xpath::OPTION_USE_DOCUMENT_CONTEXT
        ]
      ];
    }

    /**
     * @param $option
     * @param $options
     * @dataProvider provideExcludedOptionsExamples
     */
    public function testHasOptionExpectingFalse($option, $options) {
      $xpath = new Xpath($options);
      $this->assertFalse($xpath->hasOption($option));
    }

    public static function provideExcludedOptionsExamples() {
      return [
        [Xpath::OPTION_EXPLICT_NAMESPACES, Xpath::OPTION_USE_DOCUMENT_CONTEXT],
        [Xpath::OPTION_USE_DOCUMENT_CONTEXT, Xpath::OPTION_EXPLICT_NAMESPACES],
        [
          Xpath::OPTION_EXPLICT_NAMESPACES,
          0
        ],
        [
          Xpath::OPTION_USE_DOCUMENT_CONTEXT,
          0
        ]
      ];
    }

    /**
     * @covers PhpCss\Ast\Visitor\Xpath
     * @dataProvider provideNotConvertableExamples
     */
    public function testNotConvertableElements(Ast $ast) {
      $visitor = new Xpath();
      $this->setExpectedException(Exception\NotConvertable::CLASS);
      $ast->accept($visitor);
    }

    public static function provideNotConvertableExamples() {
      return array(
        ':link' => array(new Ast\Selector\Simple\PseudoClass('link')),
        ':visited' => array(new Ast\Selector\Simple\PseudoClass('visited')),
        ':hover' => array(new Ast\Selector\Simple\PseudoClass('hover')),
        ':first-line' => array(new Ast\Selector\Simple\PseudoElement('first-line'))
      );
    }

    /**
    * @covers PhpCss\Ast\Visitor\Xpath
    * @dataProvider provideExamples
    */
    public function testIntegration($expected, Ast $ast, $options = 0) {
      $visitor = new Xpath((int)$options);
      $ast->accept($visitor);
      $this->assertEquals(
        $expected, (string)$visitor
      );
    }

    public static function provideExamples() {
      return array(
        '*' => array(
          './/*',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(new Ast\Selector\Simple\Universal('*'))
              )
            )
          )
        ),
        'element, default xmlns' => array(
          './/*[(self::element or self::html:element)]',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(new Ast\Selector\Simple\Type('element'))
              )
            )
          ),
          Ast\Visitor\Xpath::OPTION_DEFAULT_NAMESPACE
        ),
        'element' => array(
          './/*[local-name() = "element"]',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(new Ast\Selector\Simple\Type('element'))
              )
            )
          )
        ),
        'element, #id' => array(
          './/*[local-name() = "element"]|.//*[@id = "id"]',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(new Ast\Selector\Simple\Type('element'))
              ),
              new Ast\Selector\Sequence(
                array(new Ast\Selector\Simple\Id('id'))
              )
            )
          )
        ),
        'element.class' => array(
          './/*[local-name() = "element" and contains(concat(" ", normalize-space(@class), " "), " class ")]',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(
                  new Ast\Selector\Simple\Type('element'),
                  new Ast\Selector\Simple\ClassName('class')
                )
              )
            )
          )
        ),
        '.class' => array(
          './/*[contains(concat(" ", normalize-space(@class), " "), " class ")]',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(
                  new Ast\Selector\Simple\ClassName('class')
                )
              )
            )
          )
        ),
        '#someId' => array(
          './/*[@id = "someId"]',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(
                  new Ast\Selector\Simple\Id('someId')
                )
              )
            )
          )
        ),
        '[attr]' => array(
          './/*[@attr]',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(
                  new Ast\Selector\Simple\Attribute(
                    'attr', Ast\Selector\Simple\Attribute::MATCH_EXISTS
                  )
                )
              )
            )
          )
        ),
        '[attr = "value"]' => array(
          './/*[@attr = "value"]',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(
                  new Ast\Selector\Simple\Attribute(
                    'attr', Ast\Selector\Simple\Attribute::MATCH_EQUALS, 'value'
                  )
                )
              )
            )
          )
        ),
        '[attr = "some value"]' => array(
          './/*[@attr = "some value"]',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(
                  new Ast\Selector\Simple\Attribute(
                    'attr', Ast\Selector\Simple\Attribute::MATCH_EQUALS, 'some value'
                  )
                )
              )
            )
          )
        ),
        '[attr^="value"]' => array(
          './/*[starts-with(@attr, "value")]',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(
                  new Ast\Selector\Simple\Attribute(
                    'attr', Ast\Selector\Simple\Attribute::MATCH_PREFIX, 'value'
                  )
                )
              )
            )
          )
        ),
        '[attr~="value"]' => array(
          './/*[contains(concat(" ", normalize-space(@attr), " "), " value ")]',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(
                  new Ast\Selector\Simple\Attribute(
                    'attr', Ast\Selector\Simple\Attribute::MATCH_INCLUDES, 'value'
                  )
                )
              )
            )
          )
        ),
        '[attr$="value"]' => array(
          './/*[substring(@attr, string-length(@attr) - 5) = "value"]',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(
                  new Ast\Selector\Simple\Attribute(
                    'attr', Ast\Selector\Simple\Attribute::MATCH_SUFFIX, 'value'
                  )
                )
              )
            )
          )
        ),
        '[attr*="value"]' => array(
          './/*[contains(@attr, "value")]',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(
                  new Ast\Selector\Simple\Attribute(
                    'attr', Ast\Selector\Simple\Attribute::MATCH_SUBSTRING, 'value'
                  )
                )
              )
            )
          )
        ),
        '[attr|="value"]' => array(
          './/*[(@attr = "value" or substring-before(@attr, "-") = "value")]',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(
                  new Ast\Selector\Simple\Attribute(
                    'attr', Ast\Selector\Simple\Attribute::MATCH_DASHMATCH, 'value'
                  )
                )
              )
            )
          )
        ),
        'E F' => array(
          './/*[local-name() = "E"]//*[local-name() = "F"]',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(
                  new Ast\Selector\Simple\Type('E')
                ),
                new Ast\Selector\Combinator\Descendant(
                  new Ast\Selector\Sequence(
                    array(
                      new Ast\Selector\Simple\Type('F')
                    )
                  )
                )
              )
            )
          )
        ),
        'E > F' => array(
          './/*[local-name() = "E"]/*[local-name() = "F"]',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(
                  new Ast\Selector\Simple\Type('E')
                ),
                new Ast\Selector\Combinator\Child(
                  new Ast\Selector\Sequence(
                    array(
                      new Ast\Selector\Simple\Type('F')
                    )
                  )
                )
              )
            )
          )
        ),
        'E ~ F' => array(
          './/*[local-name() = "E"]/following-sibling::*[local-name() = "F"]',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(
                  new Ast\Selector\Simple\Type('E')
                ),
                new Ast\Selector\Combinator\Follower(
                  new Ast\Selector\Sequence(
                    array(
                      new Ast\Selector\Simple\Type('F')
                    )
                  )
                )
              )
            )
          )
        ),
        'E + F' => array(
          './/*[local-name() = "E"]/following-sibling::*[1]/self::*[local-name() = "F"]',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(
                  new Ast\Selector\Simple\Type('E')
                ),
                new Ast\Selector\Combinator\Next(
                  new Ast\Selector\Sequence(
                    array(
                      new Ast\Selector\Simple\Type('F')
                    )
                  )
                )
              )
            )
          )
        ),
        'E:not(s)' => array(
          './/*[local-name() = "E" and not([local-name() = "s"])]',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(
                  new Ast\Selector\Simple\Type('E'),
                  new Ast\Selector\Simple\PseudoClass(
                    'not',
                    new Ast\Selector\Simple\Type('s')
                  )
                )
              )
            )
          )
        ),
        ':nth-child(3n+1)'  => array(
          './/*[((position() mod 3) = 1)]',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(
                  new Ast\Selector\Simple\PseudoClass(
                    'nth-child',
                    new Ast\Selector\Simple\PseudoClass\Position(3, 1)
                  )
                )
              )
            )
          )
        ),
        ':nth-child(3n-1)'  => array(
          './/*[((position() mod 3) = 2)]',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(
                  new Ast\Selector\Simple\PseudoClass(
                    'nth-child',
                    new Ast\Selector\Simple\PseudoClass\Position(3, -1)
                  )
                )
              )
            )
          )
        ),
        ':nth-child(42)'  => array(
          './/*[(position() = 42)]',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(
                  new Ast\Selector\Simple\PseudoClass(
                    'nth-child',
                    new Ast\Selector\Simple\PseudoClass\Position(0, 42)
                  )
                )
              )
            )
          )
        )
      );
    }
  }
}