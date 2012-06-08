<?php
/**
* PhpCss Scanner Status String Test Suite
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
require_once('PHPUnit/Framework.php');
require_once(dirname(__FILE__).'/DoubleTest.php');
require_once(dirname(__FILE__).'/SingleTest.php');


/**
* PhpCss Scanner Status String Test Suite
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssScannerStatusString_AllTests {

  public static function suite() {
    $suite = new PHPUnit_Framework_TestSuite('PhpCss Scanner Status String');
    $suite->addTestSuite('PhpCssScannerStatusStringDoubleTest');
    $suite->addTestSuite('PhpCssScannerStatusStringSingleTest');
    return $suite;
  }
}