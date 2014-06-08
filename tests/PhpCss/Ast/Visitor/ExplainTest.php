<?php
namespace PhpCss\Ast\Visitor {

  use PhpCss\Ast;

  require_once(__DIR__.'/../../../bootstrap.php');

  class ExplainTest extends \PHPUnit_Framework_TestCase {

    /**
    * @covers PhpCss\Ast\Visitor\Css
    * @dataProvider provideExamples
    */
    public function testIntegration($selector, $xml, Ast $ast) {
      $visitor = new Explain();
      $ast->accept($visitor);
      $actual = (string)$visitor;
      $this->assertXmlStringEqualsXmlString(
        $xml, $actual
      );
      $dom = new \DOMDocument();
      $dom->preserveWhiteSpace = FALSE;
      $dom->loadXML($actual);
      $this->assertEquals(
        $selector,
        $dom->documentElement->nodeValue
      );
    }

    public static function provideExamples() {
      return array(
        array(
          'ns|*',
          '<?xml version="1.0"?>
            <selector-group xmlns="urn:carica-phpcss-explain-2014">
              <selector>
                <universal>
                  <text>ns|*</text>
                </universal>
              </selector>
            </selector-group>',
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
          '<?xml version="1.0"?>
            <selector-group xmlns="urn:carica-phpcss-explain-2014">
              <selector>
                <type>
                  <text>element</text>
                </type>
              </selector>
              <text>, </text>
              <selector>
                <id>
                  <text>#id</text>
                </id>
              </selector>
              <text>, </text>
              <selector>
                <class>
                  <text>.class</text>
                </class>
              </selector>
            </selector-group>',
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
          '<?xml version="1.0"?>
            <selector-group xmlns="urn:carica-phpcss-explain-2014">
              <selector>
                <type>
                  <text>element</text>
                </type>
                <child>
                  <text> &gt; </text>
                  <selector>
                    <type>
                      <text>child</text>
                    </type>
                  </selector>
                </child>
              </selector>
            </selector-group>',
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
        ),
        array(
          '[foo~="42"]',
          '<?xml version="1.0"?>
            <selector-group xmlns="urn:carica-phpcss-explain-2014">
              <selector>
                <attribute operator="includes">
                  <text>[</text>
                  <name>
                    <text>foo</text>
                  </name>
                  <operator>
                    <text>~=</text>
                  </operator>
                  <text>"</text>
                  <value>
                    <text>42</text>
                  </value>
                  <text>"</text>
                  <text>]</text>
                </attribute>
              </selector>
            </selector-group>',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(
                  new Ast\Selector\Simple\Attribute(
                    'foo',
                    Ast\Selector\Simple\Attribute::MATCH_INCLUDES,
                    42
                  )
                )
              )
            )
          )
        ),
        array(
          ':hover',
          '<?xml version="1.0"?>
            <selector-group xmlns="urn:carica-phpcss-explain-2014">
              <selector>
                <pseudoclass>
                  <name>
                    <text>:hover</text>
                  </name>
                </pseudoclass>
              </selector>
            </selector-group>',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(
                  new Ast\Selector\Simple\PseudoClass(
                    'hover'
                  )
                )
              )
            )
          )
        ),
        array(
          ':nth-of-type(odd)',
          '<?xml version="1.0"?>
            <selector-group xmlns="urn:carica-phpcss-explain-2014">
              <selector>
                <pseudoclass>
                  <name>
                    <text>:nth-of-type</text>
                  </name>
                  <text>(</text>
                  <parameter>
                    <text>odd</text>
                  </parameter>
                  <text>)</text>
                </pseudoclass>
              </selector>
            </selector-group>',
          new Ast\Selector\Group(
            array(
              new Ast\Selector\Sequence(
                array(
                  new Ast\Selector\Simple\PseudoClass(
                    'nth-of-type',
                    new Ast\Selector\Simple\PseudoClass\Position(2, 1)
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