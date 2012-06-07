<?php
/**
* PhpCss Scanner Status String Test Suite
*
* @version $Id: AllTests.php 429 2010-03-29 08:05:32Z subjective $
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2010 Bastian Feder, Thomas Weinert
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