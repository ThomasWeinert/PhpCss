<?php
/**
* Collection of test for the PhpCssScannerStatusStringDouble class
*
* @version $Id: DoubleTest.php 430 2010-03-29 15:53:43Z subjective $
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
require_once(dirname(__FILE__).'/../../../../../src/PhpCss/Scanner/Status/String/Double.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Collection of test for the PhpCssScannerStatusStringDouble class
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssScannerStatusStringDoubleTest extends PHPUnit_Framework_TestCase {

  /**
  * @covers PhpCssScannerStatusStringDouble::getToken
  * @dataProvider getTokenDataProvider
  */
  public function testGetToken($string, $expectedToken) {
    $status = new PhpCssScannerStatusStringDouble();
    $this->assertEquals(
      $status->getToken($string, 0),
      $expectedToken
    );
  }

  /**
  * @covers PhpCssScannerStatusStringDouble::isEndToken
  */
  public function testIsEndToken() {
    $status = new PhpCssScannerStatusStringDouble();
    $this->assertTrue(
      $status->isEndToken(
        new PhpCssScannerToken(
          PhpCssScannerToken::DOUBLEQUOTE_STRING_END, '"', 0
        )
      )
    );
  }
  /**
  * @covers PhpCssScannerStatusStringDouble::getNewStatus
  */
  public function testGetNewStatus() {
    $status = new PhpCssScannerStatusStringDouble();
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
      'double quote string end' => array(
        '"',
        new PhpCssScannerToken(
          PhpCssScannerToken::DOUBLEQUOTE_STRING_END, '"', 0
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