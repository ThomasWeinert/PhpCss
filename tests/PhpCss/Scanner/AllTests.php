<?php
/**
* PhpCss Scanner Test Suite
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
require_once(dirname(__FILE__).'/StatusTest.php');
require_once(dirname(__FILE__).'/Status/AllTests.php');
require_once(dirname(__FILE__).'/TokenTest.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* PhpCss Test Suite
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssScanner_AllTests {

  public static function suite() {
    $suite = new PHPUnit_Framework_TestSuite('PhpCss Scanner');
    $suite->addTestSuite('PhpCssScannerStatusTest');
    $suite->addTestSuite('PhpCssScannerStatus_AllTests');
    $suite->addTestSuite('PhpCssScannerTokenTest');
    return $suite;
  }
}