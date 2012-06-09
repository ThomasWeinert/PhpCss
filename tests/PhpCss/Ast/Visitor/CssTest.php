<?php
/**
* Collection of tests for the Selector Sequence List class
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
* Test class for PhpCssAstSelectorSequenceList.
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssAstVisitorCssTest extends PhpCssTestCase {

  /**
  * @covers PhpCssAstVisitorCss
  * @dataProvider provideExamples
  */
  public function testIntegration($expected, $ast) {
    $visitor = new PhpCssAstVisitorCss();
    $ast->accept($visitor);
    $this->assertEquals(
      $expected, (string)$visitor
    );
  }

  public static function provideExamples() {
    return array(
      array(
        'ns|*',
        new PhpCssAstSelectorSequenceList(
          array(
            new PhpCssAstSelectorSequence(
              array(new PhpCssAstSelectorSimpleUniversal('ns'))
            )
          )
        )
      ),
      array(
        'element, #id, .class',
        new PhpCssAstSelectorSequenceList(
          array(
            new PhpCssAstSelectorSequence(
              array(new PhpCssAstSelectorSimpleType('element'))
            ),
            new PhpCssAstSelectorSequence(
              array(new PhpCssAstSelectorSimpleId('id'))
            ),
            new PhpCssAstSelectorSequence(
              array(new PhpCssAstSelectorSimpleClass('class'))
            )
          )
        )
      ),
      array(
        'element > child',
        new PhpCssAstSelectorSequenceList(
          array(
            new PhpCssAstSelectorSequence(
              array(
                new PhpCssAstSelectorSimpleType('element'),
                new PhpCssAstSelectorCombinatorChild(
                  new PhpCssAstSelectorSequence(
                    array(new PhpCssAstSelectorSimpleType('child'))
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