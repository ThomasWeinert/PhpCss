<?php
/**
* Collection of test for the PhpCssScannerPatterns class
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
require_once(dirname(__FILE__).'/../TestCase.php');

/**
* Collection of test for the PhpCssScannerPatterns class
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssScannerPatternsTest extends PhpCssTestCase {

  /**
  * @dataProvider provideValidIdentifiers
  */
  public function testValidIdentifier($identifier) {
    $this->assertRegExpMatchesString(PhpCssScannerPatterns::IDENTIFIER, $identifier);
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
    $this->assertNotRegExpMatchesString(PhpCssScannerPatterns::IDENTIFIER, $identifier);
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