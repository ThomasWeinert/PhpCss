<?php
namespace PhpCss\Ast\Visitor {

  use PhpCss\Ast;

  require_once(__DIR__.'/../../../bootstrap.php');

  class XpathTest extends \PHPUnit_Framework_TestCase {

    /**
    * @covers PhpCss\Ast\Visitor\Xpath
    * @dataProvider provideExamples
    */
    public function testIntegration($expected, $ast) {
      $visitor = new Xpath();
      $ast->accept($visitor);
      $this->assertEquals(
        $expected, (string)$visitor
      );
    }

    public static function provideExamples() {
      return array(
        'element' => array(
          '*[local-name() = "element"]',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(new Ast\Selector\Simple\Type('element'))
              )
            )
          )
        ),
        'element, #id' => array(
          '*[local-name() = "element"]|*[@id = "id"]',
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
          '*[local-name() = "element" and contains(concat(" ", normalize-space(@class), " "), " class ")]',
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
          '*[contains(concat(" ", normalize-space(@class), " "), " class ")]',
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
          '*[@id = "someId"]',
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
        '*[attr = "value"]' => array(
          '*[@attr = "value"]',
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
          '*[@attr = "some value"]',
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
        '*[starts-with(@attr, "value")]' => array(
          '*[starts-with(@attr, "value")]',
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