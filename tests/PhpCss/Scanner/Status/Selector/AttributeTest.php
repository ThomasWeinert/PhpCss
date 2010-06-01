<?php
/**
* Collection of test for the PhpCssScannerStatusSelectorAttribute class
*
* @version $Id: AttributesTest.php 429 2010-03-29 08:05:32Z subjective $
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2010 Bastian Feder, Thomas Weinert
*
* @package PhpCss
* @subpackage Tests
*/

/**
* Load necessary files
*/
require_once(dirname(__FILE__).'/../../../TestCase.php');
PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Collection of test for the PhpCssScannerStatusSelectorAttribute class
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssScannerStatusSelectorAttributeTest extends PhpCssTestCase {

  /**
  * @covers PhpCssScannerStatusSelectorAttribute::getToken
  * @dataProvider getTokenDataProvider
  */
  public function testGetToken($string, $expectedToken) {
    $status = new PhpCssScannerStatusSelectorAttribute();
    $this->assertEquals(
      $status->getToken($string, 0),
      $expectedToken
    );
  }

  /**
  * @covers PhpCssScannerStatusSelectorAttribute::isEndToken
  */
  public function testIsEndToken() {
    $status = new PhpCssScannerStatusSelectorAttribute();
    $this->assertTrue(
      $status->isEndToken(
        new PhpCssScannerToken(
          PhpCssScannerToken::ATTRIBUTE_SELECTOR_END, "]", 0
        )
      )
    );
  }
  /**
  * @covers PhpCssScannerStatusSelectorAttribute::getNewStatus
  * @dataProvider getNewStatusDataProvider
  */
  public function testGetNewStatus($token, $expectedStatus) {
    $status = new PhpCssScannerStatusSelectorAttribute();
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
      'attributes end' => array(
        "]",
        new PhpCssScannerToken(
          PhpCssScannerToken::ATTRIBUTE_SELECTOR_END, "]", 0
        )
      ),
      'simple attribute name' => array(
        "class=",
        new PhpCssScannerToken(
          PhpCssScannerToken::ATTRIBUTE_NAME, "class", 0
        )
      )
    );
  }

  public static function getNewStatusDataProvider() {
    return array(
      'whitespaces - no new status' => array(
        new PhpCssScannerToken(
          PhpCssScannerToken::WHITESPACE, " ", 0
        ),
        NULL
      ),
      'single quote string start' => array(
        new PhpCssScannerToken(
          PhpCssScannerToken::SINGLEQUOTE_STRING_START, "'", 0
        ),
        new PhpCssScannerStatusStringSingle()
      ),
      'double quote string start' => array(
        new PhpCssScannerToken(
          PhpCssScannerToken::DOUBLEQUOTE_STRING_START, "'", 0
        ),
        new PhpCssScannerStatusStringDouble()
      )
    );
  }
}
?>