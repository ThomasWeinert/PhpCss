<?php
/**
* Collection of test for the PhpCssScannerStatusStringSingle class
*
* @version $Id: SingleTest.php 430 2010-03-29 15:53:43Z subjective $
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2010 Bastian Feder, Thomas Weinert
*
* @package PhpCss
* @subpackage Tests
*/

/**
* load necessary files
*/
require_once('PHPUnit/Framework.php');
require_once(dirname(__FILE__).'/../../../../../src/PhpCss/Scanner/Status.php');
require_once(dirname(__FILE__).'/../../../../../src/PhpCss/Scanner/Token.php');
require_once(dirname(__FILE__).'/../../../../../src/PhpCss/Scanner/Status/String/Single.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Collection of test for the PhpCssScannerStatusStringSingle class
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssScannerStatusStringSingleTest extends PHPUnit_Framework_TestCase {

  /**
  * @covers PhpCssScannerStatusStringSingle::getToken
  * @dataProvider getTokenDataProvider
  */
  public function testGetToken($string, $expectedToken) {
    $status = new PhpCssScannerStatusStringSingle();
    $this->assertEquals(
      $status->getToken($string, 0),
      $expectedToken
    );
  }

  /**
  * @covers PhpCssScannerStatusStringSingle::isEndToken
  */
  public function testIsEndToken() {
    $status = new PhpCssScannerStatusStringSingle();
    $this->assertTrue(
      $status->isEndToken(
        new PhpCssScannerToken(
          PhpCssScannerToken::SINGLEQUOTE_STRING_END, "'", 0
        )
      )
    );
  }
  /**
  * @covers PhpCssScannerStatusStringSingle::getNewStatus
  */
  public function testGetNewStatus() {
    $status = new PhpCssScannerStatusStringSingle();
    $this->assertNULL(
       $status->getNewStatus(NULL)
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
      'single quote string end' => array(
        "'",
        new PhpCssScannerToken(
          PhpCssScannerToken::SINGLEQUOTE_STRING_END, "'", 0
        )
      ),
      'escaped backslash' => array(
        '\\\\',
        new PhpCssScannerToken(
          PhpCssScannerToken::STRING_ESCAPED_CHARACTER, '\\\\', 0
        )
      ),
      'string chars' => array(
        'abcd',
        new PhpCssScannerToken(
          PhpCssScannerToken::STRING_CHARACTERS, 'abcd', 0
        )
      )
    );
  }
}