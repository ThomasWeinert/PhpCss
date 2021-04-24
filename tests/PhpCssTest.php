<?php
/**
 * Load necessary files
 */

use PHPUnit\Framework\TestCase;

require_once(__DIR__.'/bootstrap.php');

/**
 * Testing PhpCss usage
 *
 * @package PhpCss
 * @subpackage Tests
 */
class PhpCssTest extends TestCase {

  /**
   * @covers       \stdClass
   * @dataProvider provideReformattedCss
   */
  public function testReformatCss($expected, $selector): void {
    $this->assertEquals(
      $expected, PhpCss::reformat($selector)
    );
  }

  /**
   * @covers       \stdClass
   * @dataProvider provideToXpath
   */
  public function testToXpath($expected, $selector, $options = 0): void {
    $this->assertEquals(
      $expected, PhpCss::toXpath($selector, $options)
    );
  }

  public static function provideReformattedCss(): array {
    return [
      // CSS 3 specification
      ['*', '*'],
      ['E', 'E'],
      // CSS 3 specification - Attributes
      ['E[foo]', 'E[foo]'],
      ['E[foo="bar"]', 'E[foo="bar"]'],
      ['E[foo~="bar"]', 'E[foo~="bar"]'],
      ['E[foo^="bar"]', 'E[foo^="bar"]'],
      ['E[foo$="bar"]', 'E[foo$="bar"]'],
      ['E[foo*="bar"]', 'E[foo*="bar"]'],
      ['E[foo|="bar"]', 'E[foo|="bar"]'],
      // CSS 3 specification - structural pseudo classes
      ['E:root', 'E:root'],
      ['E:nth-child(42)', 'E:nth-child(42)'],
      ['E:nth-last-child(42)', 'E:nth-last-child(42)'],
      ['E:nth-of-type(42)', 'E:nth-of-type(42)'],
      ['E:nth-last-of-type(42)', 'E:nth-last-of-type(42)'],
      ['E:first-child', 'E:first-child'],
      ['E:last-child', 'E:last-child'],
      ['E:first-of-type', 'E:first-of-type'],
      ['E:last-of-type', 'E:last-of-type'],
      ['E:only-child', 'E:only-child'],
      ['E:only-of-type', 'E:only-of-type'],
      ['E:empty', 'E:empty'],
      // CSS 3 specification - link pseudo classes
      ['E:link', 'E:link'],
      ['E:visited', 'E:visited'],
      // CSS 3 specification - user action pseudo classes
      ['E:active', 'E:active'],
      ['E:hover', 'E:hover'],
      ['E:focus', 'E:focus'],
      // CSS 3 specification - target pseudo class
      ['E:target', 'E:target'],
      // CSS 3 specification - language pseudo class
      ['E:lang(fr)', 'E:lang(fr)'],
      // CSS 3 specification - ui element states pseudo classes
      ['E:enabled', 'E:enabled'],
      ['E:disabled', 'E:disabled'],
      ['E:checked', 'E:checked'],
      // CSS 3 specification - pseudo elements
      ['E::first-line', 'E::first-line'],
      ['E::first-letter', 'E::first-letter'],
      ['E::before', 'E::before'],
      ['E::after', 'E::after'],
      // CSS 3 specification - class selector
      ['E.warning', 'E.warning'],
      // CSS 3 specification - id selector
      ['E#myid', 'E#myid'],
      // CSS 3 specification - negation pseudo class
      ['E:not(s)', 'E:not(s)'],
      // CSS 3 specification - combinators
      ['E F', 'E F'],
      ['E > F', 'E > F'],
      ['E + F', 'E + F'],
      ['E ~ F', 'E ~ F'],

      // individual examples
      ['element', 'element'],
      ['ns|*', 'ns|*'],
      ['.class', '.class'],
      ['element, .class', 'element, .class'],
      ['.classOne.classTwo', '.classOne.classTwo'],
      ['#id.classOne.classTwo', '#id.classOne.classTwo'],
      ['tag#id.classOne.classTwo', 'tag#id.classOne.classTwo'],
      ['element > child', 'element > child'],
      ['element child', 'element child'],
      ['html|*:not(:link):not(:visited)', 'html|*:not(:link):not(:visited)'],
      ['li + li', 'li+li'],
      ['li.class > li', 'li.class>li'],
      ['li#id ~ li', 'li#id~li'],
      ['li:hover + li', 'li:hover+li'],

      // pseudo class positions
      ['tr:nth-child(odd)', 'tr:nth-child(2n+1)'],
      ['tr:nth-child(odd)', 'tr:nth-child(odd)'],
      ['tr:nth-child(even)', 'tr:nth-child(2n+0)'],
      ['tr:nth-child(even)', 'tr:nth-child(even)'],
      ['p:nth-child(4n+1)', 'p:nth-child(4n+1)'],
      [':nth-child(10n)', ':nth-child(10n)'],
      [':nth-child(10n-1)', ':nth-child(10n-1)'],
      [':nth-child(10n+9)', ':nth-child(10n+9)'],
      ['foo:nth-child(5)', 'foo:nth-child(0n+5)'],
      ['bar:nth-child(n)', 'bar:nth-child(1n+0)'],
      [':nth-child(3n-2)', ':nth-child( +3n - 2 )'],
      [':nth-child(6)', ':nth-child( -n+ 6)'],

      // optimized
      ['*', '*|*'],
      ['element', '*|element'],
      ['element', ' element'],

      // jQuery
      ['div:contains("text")', 'div:contains("text")'],
      ['div:has(p)', 'div:has(p)'],
      ['*:gt(1)', '*:gt(1)'],
      [':lt(1)', ':lt(1)'],
      ['*:gt(-1)', '*:gt(-1)'],
      ['*:lt(-1)', '*:lt(-1)'],
      ['bar:gt(1)', 'bar:gt(1)'],
      ['tr:odd', 'tr:odd'],
      ['tr:even', 'tr:even'],
    ];
  }

  public function provideToXpath(): array {
    return [
      // CSS 3 specification
      ['.//*', '*'],
      ['.//*[local-name() = "E"]', 'E'],
      // CSS 3 specification - Attributes
      ['.//*[@foo]', '[foo]'],
      ['.//*[local-name() = "E" and @foo]', 'E[foo]'],
      ['.//*[local-name() = "E" and @foo = "bar"]', 'E[foo="bar"]'],
      ['.//*[local-name() = "E" and contains(concat(" ", normalize-space(@foo), " "), " bar ")]', 'E[foo~="bar"]'],
      ['.//*[local-name() = "E" and starts-with(@foo, "bar")]', 'E[foo^="bar"]'],
      ['.//*[local-name() = "E" and substring(@foo, string-length(@foo) - 3) = "bar"]', 'E[foo$="bar"]'],
      ['.//*[local-name() = "E" and contains(@foo, "bar")]', 'E[foo*="bar"]'],
      ['.//*[local-name() = "E" and (@foo = "bar" or substring-before(@foo, "-") = "bar")]', 'E[foo|="bar"]'],
      // CSS 3 specification - structural pseudo classes
      ['.//*[local-name() = "E" and (. = //*)]', 'E:root'],
      ['.//*[local-name() = "E" and (position() = 42)]', 'E:nth-child(42)'],
      ['.//*[local-name() = "E" and ((last() - position() + 1) = 42)]', 'E:nth-last-child(42)'],
      ['.//*[local-name() = "E" and ((count(preceding-sibling::*[local-name() = "E"]) + 1) = 42)]', 'E:nth-of-type(42)'],
      ['.//*[local-name() = "E" and ((count(following-sibling::*[local-name() = "E"]) + 1) = 42)]', 'E:nth-last-of-type(42)'],
      ['.//*[local-name() = "E" and position() = 1]', 'E:first-child'],
      ['.//*[local-name() = "E" and position() = last()]', 'E:last-child'],
      ['.//*[local-name() = "E" and (count(preceding-sibling::*[local-name() = "E"]) = 0)]', 'E:first-of-type'],
      ['.//*[local-name() = "E" and (count(following-sibling::*[local-name() = "E"]) = 0)]', 'E:last-of-type'],
      ['.//*[local-name() = "E" and (count(parent::*/*|parent::*/text()) = 1)]', 'E:only-child'],
      ['.//*[local-name() = "E" and (count(parent::*/*[local-name() = "E"]) = 1)]', 'E:only-of-type'],
      ['.//*[local-name() = "E" and (count(*|text()) = 0)]', 'E:empty'],
      // CSS 3 specification - language pseudo class
      ['.//*[local-name() = "E" and (ancestor-or-self::*[@lang][1]/@lang = "fr" or substring-before(ancestor-or-self::*[@lang][1]/@lang, "-") = "fr")]', 'E:lang(fr)'],
      // CSS 3 specification - ui element states pseudo classes
      ['.//*[local-name() = "E" and not(@disabled)]', 'E:enabled'],
      ['.//*[local-name() = "E" and @disabled]', 'E:disabled'],
      ['.//*[local-name() = "E" and @checked]', 'E:checked'],
      // CSS 3 specification - class selector
      ['.//*[local-name() = "E" and contains(concat(" ", normalize-space(@class), " "), " warning ")]', 'E.warning'],
      // CSS 3 specification - id selector
      ['.//*[local-name() = "E" and @id = "myid"]', 'E#myid'],
      // CSS 3 specification - negation pseudo class
      ['.//*[local-name() = "E" and not(local-name() = "s")]', 'E:not(s)'],
      // CSS 3 specification - combinators
      ['.//*[local-name() = "E"]//*[local-name() = "F"]', 'E F'],
      ['.//*[local-name() = "E"]/*[local-name() = "F"]', 'E > F'],
      ['.//*[local-name() = "E"]/following-sibling::*[1]/self::*[local-name() = "F"]', 'E + F'],
      ['.//*[local-name() = "E"]/following-sibling::*[local-name() = "F"]', 'E ~ F'],

      // two selectors
      ['.//*[local-name() = "foo"]|.//*[local-name() = "bar"]', 'foo, bar'],

      // foo but not in namespace bar
      ['.//*[local-name() = "foo" and not(bar:*)]', 'foo:not(bar|*)'],

      // every even but not the first 2
      ['.//*[((position() mod 2) = 1 position() >= 3)]', ':nth-child(2n+3)'],

      // individual
      ['.//*[local-name() = "bar"]', 'bar'],
      ['.//foo:bar', 'foo|bar'],
      ['.//foo:bar[@id = "ok"]', 'foo|bar#ok'],
      ['.//*[local-name() = "div" and @data-plugin = "feed"]', 'div[data-plugin=feed]'],

      // utf-8
      ['.//*[local-name() = "äöü"]', 'äöü'],
      ['.//*[local-name() = "äöü"]', 'ÄÖÜ', PhpCss\Ast\Visitor\Xpath::OPTION_LOWERCASE_ELEMENTS],

      // jQuery
      ['.//*[contains(., "text")]', '*:contains("text")'],
      ['.//*[local-name() = "div" and (*[local-name() = "p"])]', 'div:has(p)'],
      ['.//h:div[(h:p)]', 'h|div:has(h|p)'],
      ['.//*[position() > 2]', '*:gt(1)'],
      ['.//*[position() < 2]', '*:lt(1)'],
      ['.//*[position() > last() - 2]', '*:gt(-1)'],
      ['.//*[position() < last() - 2]', '*:lt(-1)'],
      ['.//*[local-name() = "bar"][position() > 2]', 'bar:gt(1)'],
      ['.//*[position() mod 2 = 0]', ':odd'],
      ['.//*[position() mod 2 = 1]', ':even'],
      ['.//*[local-name() = "tr"][position() mod 2 = 0]', 'tr:odd'],
    ];
  }
}
