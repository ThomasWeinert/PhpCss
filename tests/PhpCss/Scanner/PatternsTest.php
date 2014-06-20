<?php
namespace PhpCss\Scanner {

  require_once(__DIR__.'/../../bootstrap.php');

  class PatternsTest extends \PHPUnit_Framework_TestCase {

    /**
    * @dataProvider provideValidIdentifiers
    */
    public function testValidIdentifier($identifier) {
      $this->assertRegExpMatchesString(Patterns::IDENTIFIER, $identifier);
    }

    public static function provideValidIdentifiers() {
      return array(
        array('div'),
        array('xhtml|div'),
        array('xhtml|*'),
        array('*|*'),
        array('*|div'),
        array('xsl|for-each'),
      );
    }

    /**
    * @dataProvider provideInvalidIdentifiers
    */
    public function testInvalidIdentifier($identifier) {
      $this->assertNotRegExpMatchesString(Patterns::IDENTIFIER, $identifier);
    }

    public static function provideInvalidIdentifiers() {
      return array(
        array(' '),
        array('1div'),
        array('div bar'),
        array('**'),
        array('**|bar'),
        array('foo|**'),
        array('e.warning'),
        array('e#myid'),
        array(':class'),
        array('attr=value')
      );
    }

    private function assertRegExpMatchesString($pattern, $string, $message = '') {
      $this->assertTrue(
        preg_match($pattern, $string, $matches) && $matches[0] == $string,
        empty($message) ? 'The pattern did not match the full string.' : $message
      );
    }

    private function assertNotRegExpMatchesString($pattern, $string, $message = '') {
      $this->assertFalse(
        preg_match($pattern, $string, $matches) && $matches[0] == $string,
        empty($message) ? 'The pattern did match the full string.' : $message
      );
    }
  }
}