<?php
/**
* Testing PhpCss usage
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2012 PhpCss Team
*
* @package PhpCss
* @subpackage Tests
*/

/**
* Load necessary files
*/
require_once(dirname(__FILE__).'/TestCase.php');

/**
* Testing PhpCss usage
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssIntegrationTest extends PhpCssTestCase {

  /**
  * @covers \stdClass
  * @dataProvider provideReformattedCss
  */
  public function testReformatCss($expected, $selector) {
    $scanner = new PhpCssScanner(new PhpCssScannerStatusSelector());
    $tokens = array();
    $scanner->scan($tokens, $selector);
    $parser = new PhpCssParserDefault($tokens);
    $ast = $parser->parse();
    $visitor = new PhpCssAstVisitorCss();
    $ast->accept($visitor);
    $this->assertEquals(
      $expected, (string)$visitor
    );
  }

  public static function provideReformattedCss() {
    return array(
      array('*', '*'),
      array('element', 'element'),
      array('ns|*', 'ns|*'),
      array('.class', '.class'),
      array('element, .class', 'element, .class'),
      array('.classOne.classTwo', '.classOne.classTwo'),
      array('#id.classOne.classTwo', '#id.classOne.classTwo'),
      array('tag#id.classOne.classTwo', 'tag#id.classOne.classTwo'),
      // optimized
      array('*', '*|*'),
      array('element', '*|element'),
      array('element', ' element')
    );
  }
}