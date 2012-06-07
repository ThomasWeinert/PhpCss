<?php
/**
* Collection of tests for the ParserSequence class
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2010 Bastian Feder, Thomas Weinert
*
* @package PhpCss
* @subpackage Tests
*/

/**
* Load necessary files
*/
require_once(dirname(dirname(__FILE__)).'/TestCase.php');

/**
* Test class for PhpCssParserSequence.
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssParserSequenceTest extends PhpCssTestCase {

  /**
  * @covers PhpCssParserSequence
  * @dataProvider provideParseData
  */
  public function testParse($expected, $tokens) {
    $parser = new PhpCssParserSequence($tokens);
    $this->assertEquals(
      $expected, $parser->parse()
    );
  }

  /**
  * @covers PhpCssParserSequence
  * @dataProvider provideInvalidParseData
  */
  public function testParseExpectingException($tokens) {
    $parser = new PhpCssParserSequence($tokens);
    $this->setExpectedException('PhpCssExceptionTokenMismatch');
    $parser->parse();
  }

  public static function provideParseData() {
    return array(
      'element' => array(
        new PhpCssAstSelectorSequence(
          array(new PhpCssAstSelectorSimpleType('element'))
        ),
        array(
          new PhpCssScannerToken(
            PhpCssScannerToken::TYPE_SELECTOR,
            'element',
            0
          )
        )
      ),
      'element with prefix' => array(
        new PhpCssAstSelectorSequence(
          array(new PhpCssAstSelectorSimpleType('element', 'prefix'))
        ),
        array(
          new PhpCssScannerToken(
            PhpCssScannerToken::TYPE_SELECTOR,
            'prefix|element',
            0
          )
        )
      ),
      'class' => array(
        new PhpCssAstSelectorSequence(
          array(new PhpCssAstSelectorSimpleClass('classname'))
        ),
        array(
          new PhpCssScannerToken(
            PhpCssScannerToken::CLASS_SELECTOR,
            '.classname',
            0
          )
        )
      ),
      'id' => array(
        new PhpCssAstSelectorSequence(
          array(new PhpCssAstSelectorSimpleId('id'))
        ),
        array(
          new PhpCssScannerToken(
            PhpCssScannerToken::ID_SELECTOR,
            '#id',
            0
          )
        )
      ),
      'element.class' => array(
        new PhpCssAstSelectorSequence(
          array(
            new PhpCssAstSelectorSimpleType('element'),
            new PhpCssAstSelectorSimpleClass('classname')
          )
        ),
        array(
          new PhpCssScannerToken(
            PhpCssScannerToken::TYPE_SELECTOR,
            'element',
            0
          ),
          new PhpCssScannerToken(
            PhpCssScannerToken::CLASS_SELECTOR,
            '.classname',
            7
          )
        )
      ),
      'element > child' => array(
        new PhpCssAstSelectorSequence(
          array(
            new PhpCssAstSelectorSimpleType('element')
          ),
          new PhpCssAstSelectorCombinatorChild(
            new PhpCssAstSelectorSequence(
              array(
                new PhpCssAstSelectorSimpleType('child')
              )
            )
          )
        ),
        array(
          new PhpCssScannerToken(
            PhpCssScannerToken::TYPE_SELECTOR,
            'element',
            0
          ),
          new PhpCssScannerToken(
            PhpCssScannerToken::COMBINATOR,
            '>',
            7
          ),
          new PhpCssScannerToken(
            PhpCssScannerToken::TYPE_SELECTOR,
            'child',
            8
          )
        )
      ),
      'element child' => array(
        new PhpCssAstSelectorSequence(
          array(
            new PhpCssAstSelectorSimpleType('element')
          ),
          new PhpCssAstSelectorCombinatorDescendant(
            new PhpCssAstSelectorSequence(
              array(
                new PhpCssAstSelectorSimpleType('child')
              )
            )
          )
        ),
        array(
          new PhpCssScannerToken(
            PhpCssScannerToken::TYPE_SELECTOR,
            'element',
            0
          ),
          new PhpCssScannerToken(
            PhpCssScannerToken::WHITESPACE,
            ' ',
            7
          ),
          new PhpCssScannerToken(
            PhpCssScannerToken::TYPE_SELECTOR,
            'child',
            8
          )
        )
      )
    );
  }
  public static function provideInvalidParseData() {
    return array(
      'two elements' => array(
        array(
          new PhpCssScannerToken(
            PhpCssScannerToken::TYPE_SELECTOR,
            'element',
            0
          ),
          new PhpCssScannerToken(
            PhpCssScannerToken::TYPE_SELECTOR,
            'element',
            7
          )
        )
      ),
      'element after class' => array(
        array(
          new PhpCssScannerToken(
            PhpCssScannerToken::CLASS_SELECTOR,
            '.classname',
            0
          ),
          new PhpCssScannerToken(
            PhpCssScannerToken::TYPE_SELECTOR,
            'element',
            10
          )
        )
      )
    );
  }
}