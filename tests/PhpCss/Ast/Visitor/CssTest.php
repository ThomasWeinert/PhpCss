<?php
namespace PhpCss\Ast\Visitor {

  use PhpCss\Ast;

  require_once(__DIR__.'/../../../bootstrap.php');

  class CssTest extends \PHPUnit_Framework_TestCase {

    /**
    * @covers PhpCss\Ast\Visitor\Css
    * @dataProvider provideExamples
    */
    public function testIntegration($expected, $ast) {
      $visitor = new Css();
      $ast->accept($visitor);
      $this->assertEquals(
        $expected, (string)$visitor
      );
    }

    public static function provideExamples() {
      return array(
        array(
          'ns|*',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(new Ast\Selector\Simple\Universal('ns'))
              )
            )
          )
        ),
        array(
          'element, #id, .class',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(new Ast\Selector\Simple\Type('element'))
              ),
              new Ast\Selector\Sequence(
                array(new Ast\Selector\Simple\Id('id'))
              ),
              new Ast\Selector\Sequence(
                array(new Ast\Selector\Simple\ClassName('class'))
              )
            )
          )
        ),
        array(
          'element > child',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(
                  new Ast\Selector\Simple\Type('element'),
                  new Ast\Selector\Combinator\Child(
                    new Ast\Selector\Sequence(
                      array(new Ast\Selector\Simple\Type('child'))
                    )
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