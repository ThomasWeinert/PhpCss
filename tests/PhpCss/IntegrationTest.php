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
      // CSS 3 specification
      array('*', '*'),
      array('E', 'E'),
      // CSS 3 specification - Attributes
      array('E[foo]', 'E[foo]'),
      array('E[foo="bar"]', 'E[foo="bar"]'),
      array('E[foo~="bar"]', 'E[foo~="bar"]'),
      array('E[foo^="bar"]', 'E[foo^="bar"]'),
      array('E[foo$="bar"]', 'E[foo$="bar"]'),
      array('E[foo*="bar"]', 'E[foo*="bar"]'),
      array('E[foo|="bar"]', 'E[foo|="bar"]'),
      // CSS 3 specification - structural pseudo classes
      //array('E:root', 'E:root'),
      //array('E:nth-child(42)', 'E:nth-child(42)'),
      //array('E:nth-last-child(42)', 'E:nth-last-child(42)'),
      //array('E:nth-of-type(42)', 'E:nth-of-type(42)'),
      //array('E:nth-last-of-type(42)', 'E:nth-last-of-type(42)'),
      //array('E:first-child', 'E:first-child'),
      //array('E:last-child', 'E:last-child'),
      //array('E:first-of-type', 'E:first-of-type'),
      //array('E:last-of-type', 'E:last-of-type'),
      //array('E:only-child', 'E:only-child'),
      //array('E:only-of-type', 'E:only-of-type'),
      //array('E:emtpy', 'E:empty'),
      // CSS 3 specification - link pseudo classes
      //array('E:link', 'E:link'),
      //array('E:visited', 'E:visited'),
      // CSS 3 specification - user action pseudo classes
      //array('E:active', 'E:active'),
      //array('E:hover', 'E:hover'),
      //array('E:focus', 'E:focus'),
      // CSS 3 specification - target pseudo class
      //array('E:target'. 'E:target'),
      // CSS 3 specification - language pseudo class
      //array('E:lang(fr)'. 'E:lang(fr)'),
      // CSS 3 specification - ui element states pseudo classes
      //array('E:enabled', 'E:enabled'),
      //array('E:disabled', 'E:disabled'),
      //array('E:checked', 'E:checked'),
      // CSS 3 specification - pseudo elements
      //array('E::first-line', 'E::first-line'),
      //array('E::first-letter', 'E::first-letter'),
      //array('E::before', 'E::before'),
      //array('E::after', 'E::after'),
      // CSS 3 specification - class selector
      array('E.warning', 'E.warning'),
      // CSS 3 specification - id selector
      array('E#myid', 'E#myid'),
      // CSS 3 specification - negation pseudo class
      //array('E:not(s)', 'E:not(s)'),
      // CSS 3 specification - combinators
      array('E F', 'E F'),
      array('E > F', 'E > F'),
      array('E + F', 'E + F'),
      array('E ~ F', 'E ~ F'),

      // individual examples
      array('element', 'element'),
      array('ns|*', 'ns|*'),
      array('.class', '.class'),
      array('element, .class', 'element, .class'),
      array('.classOne.classTwo', '.classOne.classTwo'),
      array('#id.classOne.classTwo', '#id.classOne.classTwo'),
      array('tag#id.classOne.classTwo', 'tag#id.classOne.classTwo'),
      array('element > child', 'element > child'),
      array('element child', 'element child'),
      // optimized
      array('*', '*|*'),
      array('element', '*|element'),
      array('element', ' element')
    );
  }
}