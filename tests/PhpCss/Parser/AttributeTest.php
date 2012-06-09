<?php
/**
* Collection of tests for the ParserAttribute class
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
* Test class for PhpCssParserAttribute.
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssParserAttributeTest extends PhpCssTestCase {

  /**
  * @covers PhpCssParserAttribute
  * @dataProvider provideParseData
  */
  public function testParse($expected, $tokens) {
    $parser = new PhpCssParserAttribute($tokens);
    $this->assertEquals(
      $expected, $parser->parse()
    );
  }

  /**
  * @covers PhpCssParserAttribute
  * @dataProvider provideInvalidParseData
  */
  public function testParseExpectingException($tokens) {
    $parser = new PhpCssParserAttribute($tokens);
    $this->setExpectedException('PhpCssExceptionTokenMismatch');
    $parser->parse();
  }

  public static function provideParseData() {
    return array(
      'simple identifier' => array(
        new PhpCssAstSelectorSimpleAttribute('attr'),
        array(
          new PhpCssScannerToken(
            PhpCssScannerToken::IDENTIFIER,
            'attr',
            0
          ),
          new PhpCssScannerToken(
            PhpCssScannerToken::ATTRIBUTE_SELECTOR_END,
            ']',
            4
          )
        )
      ),
      'class attribute' => array(
        new PhpCssAstSelectorSimpleAttribute(
          'class',
          PhpCssAstSelectorSimpleAttribute::MATCH_INCLUDES,
          'warning'
        ),
        array(
          new PhpCssScannerToken(
            PhpCssScannerToken::IDENTIFIER,
            'class',
            0
          ),
          new PhpCssScannerToken(
            PhpCssScannerToken::ATTRIBUTE_OPERATOR,
            '~=',
            5
          ),
          new PhpCssScannerToken(
            PhpCssScannerToken::IDENTIFIER,
            'warning',
            7
          ),
          new PhpCssScannerToken(
            PhpCssScannerToken::ATTRIBUTE_SELECTOR_END,
            ']',
            14
          )
        )
      )
    );
  }
  public static function provideInvalidParseData() {
    return array(
      'identifer followed by string start' => array(
        array(
          new PhpCssScannerToken(
            PhpCssScannerToken::IDENTIFIER,
            'attr',
            0
          ),
          new PhpCssScannerToken(
            PhpCssScannerToken::SINGLEQUOTE_STRING_START,
            "'",
            4
          )
        )
      )
    );
  }
}