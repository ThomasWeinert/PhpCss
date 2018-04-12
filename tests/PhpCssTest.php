<?php
/**
* Load necessary files
*/
require_once(dirname(__FILE__).'/bootstrap.php');

/**
* Testing PhpCss usage
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssTest extends \PHPUnit\Framework\TestCase {

  /**
  * @covers \stdClass
  * @dataProvider provideReformattedCss
  */
  public function testReformatCss($expected, $selector) {
    $this->assertEquals(
      $expected, PhpCss::reformat($selector)
    );
  }

  /**
  * @covers \stdClass
  * @dataProvider provideToXpath
  */
  public function testToXpath($expected, $selector, $options = 0) {
    $this->assertEquals(
      $expected, PhpCss::toXpath($selector, $options)
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
      array('E:root', 'E:root'),
      array('E:nth-child(42)', 'E:nth-child(42)'),
      array('E:nth-last-child(42)', 'E:nth-last-child(42)'),
      array('E:nth-of-type(42)', 'E:nth-of-type(42)'),
      array('E:nth-last-of-type(42)', 'E:nth-last-of-type(42)'),
      array('E:first-child', 'E:first-child'),
      array('E:last-child', 'E:last-child'),
      array('E:first-of-type', 'E:first-of-type'),
      array('E:last-of-type', 'E:last-of-type'),
      array('E:only-child', 'E:only-child'),
      array('E:only-of-type', 'E:only-of-type'),
      array('E:empty', 'E:empty'),
      // CSS 3 specification - link pseudo classes
      array('E:link', 'E:link'),
      array('E:visited', 'E:visited'),
      // CSS 3 specification - user action pseudo classes
      array('E:active', 'E:active'),
      array('E:hover', 'E:hover'),
      array('E:focus', 'E:focus'),
      // CSS 3 specification - target pseudo class
      array('E:target', 'E:target'),
      // CSS 3 specification - language pseudo class
      array('E:lang(fr)', 'E:lang(fr)'),
      // CSS 3 specification - ui element states pseudo classes
      array('E:enabled', 'E:enabled'),
      array('E:disabled', 'E:disabled'),
      array('E:checked', 'E:checked'),
      // CSS 3 specification - pseudo elements
      array('E::first-line', 'E::first-line'),
      array('E::first-letter', 'E::first-letter'),
      array('E::before', 'E::before'),
      array('E::after', 'E::after'),
      // CSS 3 specification - class selector
      array('E.warning', 'E.warning'),
      // CSS 3 specification - id selector
      array('E#myid', 'E#myid'),
      // CSS 3 specification - negation pseudo class
      array('E:not(s)', 'E:not(s)'),
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
      array('html|*:not(:link):not(:visited)', 'html|*:not(:link):not(:visited)'),

      // pseudo class positions
      array('tr:nth-child(odd)', 'tr:nth-child(2n+1)'),
      array('tr:nth-child(odd)', 'tr:nth-child(odd)'),
      array('tr:nth-child(even)', 'tr:nth-child(2n+0)'),
      array('tr:nth-child(even)', 'tr:nth-child(even)'),
      array('p:nth-child(4n+1)', 'p:nth-child(4n+1)'),
      array(':nth-child(10n-1)', ':nth-child(10n-1)'),
      array(':nth-child(10n+9)', ':nth-child(10n+9)'),
      array('foo:nth-child(5)', 'foo:nth-child(0n+5)'),
      array('bar:nth-child(n)', 'bar:nth-child(1n+0)'),
      array(':nth-child(3n-2)', ':nth-child( +3n - 2 )'),
      array(':nth-child(6)', ':nth-child( -n+ 6)'),

      // optimized
      array('*', '*|*'),
      array('element', '*|element'),
      array('element', ' element'),

      // jQuery
      array('div:contains("text")', 'div:contains("text")')
    );
  }

  public function provideToXpath() {
    return array(
      // CSS 3 specification
      array('.//*', '*'),
      array('.//*[local-name() = "E"]', 'E'),
      // CSS 3 specification - Attributes
      array('.//*[@foo]', '[foo]'),
      array('.//*[local-name() = "E" and @foo]', 'E[foo]'),
      array('.//*[local-name() = "E" and @foo = "bar"]', 'E[foo="bar"]'),
      array('.//*[local-name() = "E" and contains(concat(" ", normalize-space(@foo), " "), " bar ")]', 'E[foo~="bar"]'),
      array('.//*[local-name() = "E" and starts-with(@foo, "bar")]', 'E[foo^="bar"]'),
      array('.//*[local-name() = "E" and substring(@foo, string-length(@foo) - 3) = "bar"]', 'E[foo$="bar"]'),
      array('.//*[local-name() = "E" and contains(@foo, "bar")]', 'E[foo*="bar"]'),
      array('.//*[local-name() = "E" and (@foo = "bar" or substring-before(@foo, "-") = "bar")]', 'E[foo|="bar"]'),
      // CSS 3 specification - structural pseudo classes
      array('.//*[local-name() = "E" and (. = //*)]', 'E:root'),
      array('.//*[local-name() = "E" and (position() = 42)]', 'E:nth-child(42)'),
      array('.//*[local-name() = "E" and ((last() - position() + 1) = 42)]', 'E:nth-last-child(42)'),
      array('.//*[local-name() = "E" and ((count(preceding-sibling::*[local-name() = "E"]) + 1) = 42)]', 'E:nth-of-type(42)'),
      array('.//*[local-name() = "E" and ((count(following-sibling::*[local-name() = "E"]) + 1) = 42)]', 'E:nth-last-of-type(42)'),
      array('.//*[local-name() = "E" and position() = 1]', 'E:first-child'),
      array('.//*[local-name() = "E" and position() = last()]', 'E:last-child'),
      array('.//*[local-name() = "E" and (count(preceding-sibling::*[local-name() = "E"]) = 0)]', 'E:first-of-type'),
      array('.//*[local-name() = "E" and (count(following-sibling::*[local-name() = "E"]) = 0)]', 'E:last-of-type'),
      array('.//*[local-name() = "E" and (count(parent::*/*|parent::*/text()) = 1)]', 'E:only-child'),
      array('.//*[local-name() = "E" and (count(parent::*/*[local-name() = "E"]) = 1)]', 'E:only-of-type'),
      array('.//*[local-name() = "E" and (count(*|text()) = 0)]', 'E:empty'),
      // CSS 3 specification - language pseudo class
      array('.//*[local-name() = "E" and (ancestor-or-self::*[@lang][1]/@lang = "fr" or substring-before(ancestor-or-self::*[@lang][1]/@lang, "-") = "fr")]', 'E:lang(fr)'),
      // CSS 3 specification - ui element states pseudo classes
      array('.//*[local-name() = "E" and not(@disabled)]', 'E:enabled'),
      array('.//*[local-name() = "E" and @disabled]', 'E:disabled'),
      array('.//*[local-name() = "E" and @checked]', 'E:checked'),
      // CSS 3 specification - class selector
      array('.//*[local-name() = "E" and contains(concat(" ", normalize-space(@class), " "), " warning ")]', 'E.warning'),
      // CSS 3 specification - id selector
      array('.//*[local-name() = "E" and @id = "myid"]', 'E#myid'),
      // CSS 3 specification - negation pseudo class
      array('.//*[local-name() = "E" and not(local-name() = "s")]', 'E:not(s)'),
      // CSS 3 specification - combinators
      array('.//*[local-name() = "E"]//*[local-name() = "F"]', 'E F'),
      array('.//*[local-name() = "E"]/*[local-name() = "F"]', 'E > F'),
      array('.//*[local-name() = "E"]/following-sibling::*[1]/self::*[local-name() = "F"]', 'E + F'),
      array('.//*[local-name() = "E"]/following-sibling::*[local-name() = "F"]', 'E ~ F'),

      // two selectors
      array('.//*[local-name() = "foo"]|.//*[local-name() = "bar"]', 'foo, bar'),

      // foo but not in namespace bar
      array('.//*[local-name() = "foo" and not(bar:*)]', 'foo:not(bar|*)'),

      // every even but not the first 2
      array('.//*[((position() mod 2) = 1 position() >= 3)]', ':nth-child(2n+3)'),

      // individual
      array('.//*[local-name() = "bar"]', 'bar'),
      array('.//foo:bar', 'foo|bar'),
      array('.//foo:bar[@id = "ok"]', 'foo|bar#ok'),
      array('.//*[local-name() = "div" and @data-plugin = "feed"]', 'div[data-plugin=feed]'),

      // utf-8
      array('.//*[local-name() = "äöü"]', 'äöü'),
      array('.//*[local-name() = "äöü"]', 'ÄÖÜ', PhpCss\Ast\Visitor\Xpath::OPTION_LOWERCASE_ELEMENTS),

      // jQuery
      array('.//*[contains(., "text")]', '*:contains("text")'),
    );
  }
}
