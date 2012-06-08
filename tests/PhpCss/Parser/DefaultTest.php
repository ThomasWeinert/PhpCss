<?php
/**
* Collection of tests for the ParserDefault class
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
require_once(dirname(dirname(__FILE__)).'/TestCase.php');

/**
* Test class for PhpCssParserDefault.
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssParserDefaultTest extends PhpCssTestCase {

  /**
  * @covers PhpCssParserDefault::parse
  * @dataProvider provideParseData
  */
  public function testParse($expected, $tokens) {
    $parser = new PhpCssParserDefault($tokens);
    $this->assertEquals(
      $expected, $parser->parse()
    );
  }

  public static function provideParseData() {
    return array(
      'empty list' => array(
        new PhpCssAstSelectorSequenceList(),
        array()
      ),
      'element' => array(
        new PhpCssAstSelectorSequenceList(
          array(
            new PhpCssAstSelectorSequence(
              array(new PhpCssAstSelectorSimpleType('element'))
            )
          )
        ),
        array(
          new PhpCssScannerToken(
            PhpCssScannerToken::TYPE_SELECTOR,
            'element',
            0
          )
        )
      ),
      'two whitespaces and an element' => array(
        new PhpCssAstSelectorSequenceList(
          array(
            new PhpCssAstSelectorSequence(
              array(new PhpCssAstSelectorSimpleType('element'))
            )
          )
        ),
        array(
          new PhpCssScannerToken(
            PhpCssScannerToken::WHITESPACE,
            '   ',
            0
          ),
          new PhpCssScannerToken(
            PhpCssScannerToken::WHITESPACE,
            '  ',
            3
          ),
          new PhpCssScannerToken(
            PhpCssScannerToken::TYPE_SELECTOR,
            'element',
            5
          )
        )
      )
    );
  }
}