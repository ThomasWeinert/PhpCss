<?php
namespace PhpCss\Ast\Visitor {

  use PhpCss\Ast;

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
    * @dataProvider provideExamples
    */
    public function testIntegration($expected, Ast $ast) {
      $visitor = new Xpath();
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
        '*[attr = "value"]' => array(
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
        '*[attr = "some value"]' => array(
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
        '*[attr~="value"]' => array(
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
        )
      );
    }
  }
}