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
      'element, #id' => array(
        '*[local-name() = "element"]|*[@id = "#id]',
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
        '*[local-name() = "element"][contains(concat(" ", normalize-space(@class), " "), " class ")]."',
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
      )
    );
  }

}