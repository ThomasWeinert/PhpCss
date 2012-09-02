<?php
/**
* Collection of tests for the xpath visitor
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2012 PhpCss Team
*
* @package PhpCss
* @subpackage Tests
*/

/**
* Load necessary files
*/
require_once(dirname(__FILE__).'/../../TestCase.php');

/**
* Test class for PhpCssAstVisitorXpath.
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssAstVisitorXpathTest extends PhpCssTestCase {

  /**
  * @covers PhpCssAstVisitorCss
  * @dataProvider provideExamples
  */
  public function testIntegration($expected, $ast) {
    $visitor = new PhpCssAstVisitorXpath();
    $ast->accept($visitor);
    $this->assertEquals(
      $expected, (string)$visitor
    );
  }

  public static function provideExamples() {
    return array(
      'element' => array(
        '*[local-name() = "element"]',
        new PhpCssAstSelectorGroup(
          array(
            new PhpCssAstSelectorSequence(
              array(new PhpCssAstSelectorSimpleType('element'))
            )
          )
        )
      ),
      'element, #id' => array(
        '*[local-name() = "element"]|*[@id = "id"]',
        new PhpCssAstSelectorGroup(
          array(
            new PhpCssAstSelectorSequence(
              array(new PhpCssAstSelectorSimpleType('element'))
            ),
            new PhpCssAstSelectorSequence(
              array(new PhpCssAstSelectorSimpleId('id'))
            )
          )
        )
      ),
      'element.class' => array(
        '*[local-name() = "element" and contains(concat(" ", normalize-space(@class), " "), " class ")]',
        new PhpCssAstSelectorGroup(
          array(
            new PhpCssAstSelectorSequence(
              array(
                new PhpCssAstSelectorSimpleType('element'),
                new PhpCssAstSelectorSimpleClass('class')
              )
            )
          )
        )
      ),
      '.class' => array(
        '*[contains(concat(" ", normalize-space(@class), " "), " class ")]',
        new PhpCssAstSelectorGroup(
          array(
            new PhpCssAstSelectorSequence(
              array(
                new PhpCssAstSelectorSimpleClass('class')
              )
            )
          )
        )
      ),
      '#someId' => array(
        '*[@id = "someId"]',
        new PhpCssAstSelectorGroup(
          array(
            new PhpCssAstSelectorSequence(
              array(
                new PhpCssAstSelectorSimpleId('someId')
              )
            )
          )
        )
      ),
      '[attr=value]' => array(
        '*[@attr = "value"]',
        new PhpCssAstSelectorGroup(
          array(
            new PhpCssAstSelectorSequence(
              array(
                new PhpCssAstSelectorSimpleAttribute(
                  'attr', PhpCssAstSelectorSimpleAttribute::MATCH_EQUALS, 'value'
                )
              )
            )
          )
        )
      ),
      '[attr="some value"]' => array(
        '*[@attr = "some value"]',
        new PhpCssAstSelectorGroup(
          array(
            new PhpCssAstSelectorSequence(
              array(
                new PhpCssAstSelectorSimpleAttribute(
                  'attr', PhpCssAstSelectorSimpleAttribute::MATCH_EQUALS, 'some value'
                )
              )
            )
          )
        )
      ),
      '[attr^="value")]' => array(
        '*[starts-with(@attr, "value")]',
        new PhpCssAstSelectorGroup(
          array(
            new PhpCssAstSelectorSequence(
              array(
                new PhpCssAstSelectorSimpleAttribute(
                  'attr', PhpCssAstSelectorSimpleAttribute::MATCH_PREFIX, 'value'
                )
              )
            )
          )
        )
      ),
      '[attr*="value")]' => array(
        '*[contains(@attr, "value")]',
        new PhpCssAstSelectorGroup(
          array(
            new PhpCssAstSelectorSequence(
              array(
                new PhpCssAstSelectorSimpleAttribute(
                  'attr', PhpCssAstSelectorSimpleAttribute::MATCH_SUBSTRING, 'value'
                )
              )
            )
          )
        )
      ),
      '[attr~="value")]' => array(
        '*[contains(concat(" ", normalize-space(@attr), " "), " value ")]',
        new PhpCssAstSelectorGroup(
          array(
            new PhpCssAstSelectorSequence(
              array(
                new PhpCssAstSelectorSimpleAttribute(
                  'attr', PhpCssAstSelectorSimpleAttribute::MATCH_INCLUDES, 'value'
                )
              )
            )
          )
        )
      ),
      '[lang|="de")]' => array(
        '*[(@lang = "de" or starts-with(@lang, "de-")]',
        new PhpCssAstSelectorGroup(
          array(
            new PhpCssAstSelectorSequence(
              array(
                new PhpCssAstSelectorSimpleAttribute(
                  'lang', PhpCssAstSelectorSimpleAttribute::MATCH_DASHMATCH, 'de'
                )
              )
            )
          )
        )
      ),
      '[@type$="/xml")]' => array(
        '*[substring(@type, string-length(@type) - string-length("/xml") + 1) = "/xml"]',
        new PhpCssAstSelectorGroup(
          array(
            new PhpCssAstSelectorSequence(
              array(
                new PhpCssAstSelectorSimpleAttribute(
                  'type', PhpCssAstSelectorSimpleAttribute::MATCH_SUFFIX, '/xml'
                )
              )
            )
          )
        )
      )
    );
  }

}