<?php
/**
* Collection of test for the PhpCssScannerStatusSelector class
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
require_once(dirname(__FILE__).'/../../../../src/PhpCss/Scanner/Status/Selector.php');

/**
* Collection of test for the PhpCssScannerStatusSelector class
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssScannerStatusSelectorTest extends PhpCssTestCase {

  /**
  * @covers PhpCssScannerStatusSelector::getToken
  * @dataProvider getTokenDataProvider
  */
  public function testGetToken($string, $expectedToken) {
    $status = new PhpCssScannerStatusSelector();
    $this->assertEquals(
      $status->getToken($string, 0),
      $expectedToken
    );
  }

  /**
  * @covers PhpCssScannerStatusSelector::isEndToken
  */
  public function testIsEndToken() {
    $status = new PhpCssScannerStatusSelector();
    $this->assertFalse(
      $status->isEndToken(
        new PhpCssScannerToken(PhpCssScannerToken::WHITESPACE, ' ', 42)
      )
    );
  }

  /**
  * @covers PhpCssScannerStatusSelector::getNewStatus
  * @dataProvider getNewStatusDataProvider
  */
  public function testGetNewStatus($token, $expectedStatus) {
    $status = new PhpCssScannerStatusSelector();
    $this->assertEquals(
      $status->getNewStatus($token),
      $expectedStatus
    );
  }


  /*****************************
  * Data provider
  *****************************/

  public static function getTokenDataProvider() {
    return array(
      'empty' => array(
        '',
        NULL
      ),
      'type selector' => array(
        'tag',
        new PhpCssScannerToken(PhpCssScannerToken::TYPE_SELECTOR, "tag", 0)
      ),
      'id selector' => array(
        '#id',
        new PhpCssScannerToken(PhpCssScannerToken::ID_SELECTOR, "#id", 0)
      ),
      'class selector' => array(
        '.class',
        new PhpCssScannerToken(PhpCssScannerToken::CLASS_SELECTOR, ".class", 0)
      ),
      'single quote string start' => array(
        "'test'",
        new PhpCssScannerToken(PhpCssScannerToken::SINGLEQUOTE_STRING_START, "'", 0)
      ),
      'double quote string start' => array(
        '"test"',
        new PhpCssScannerToken(PhpCssScannerToken::DOUBLEQUOTE_STRING_START, '"', 0)
      ),
      'attributes start' => array(
        "[attr]",
        new PhpCssScannerToken(PhpCssScannerToken::ATTRIBUTE_SELECTOR_START, "[", 0)
      )
    );
  }

  public static function getNewStatusDataProvider() {
    return array(
      'whitespaces - no new status' => array(
        new PhpCssScannerToken(PhpCssScannerToken::WHITESPACE, " ", 0),
        NULL
      ),
      'single quote string start' => array(
        new PhpCssScannerToken(PhpCssScannerToken::SINGLEQUOTE_STRING_START, "'", 0),
        new PhpCssScannerStatusStringSingle()
      ),
      'double quote string start' => array(
        new PhpCssScannerToken(PhpCssScannerToken::DOUBLEQUOTE_STRING_START, "'", 0),
        new PhpCssScannerStatusStringDouble()
      ),
      'attributes selector start' => array(
        new PhpCssScannerToken(PhpCssScannerToken::ATTRIBUTE_SELECTOR_START, "[", 0),
        new PhpCssScannerStatusSelectorAttribute()
      )
    );
  }
}
?>