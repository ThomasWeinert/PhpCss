<?php
/**
* PhpCss Scanner Status Test Suite
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
require_once(dirname(__FILE__).'/SelectorTest.php');
require_once(dirname(__FILE__).'/Selector/AllTests.php');
require_once(dirname(__FILE__).'/String/AllTests.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* PhpCss Test Suite
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssScannerStatus_AllTests {

  public static function suite() {
    $suite = new PHPUnit_Framework_TestSuite('PhpCss Scanner Status');
    $suite->addTestSuite('PhpCssScannerStatusSelectorTest');
    $suite->addTestSuite('PhpCssScannerStatusSelector_AllTests');
    $suite->addTestSuite('PhpCssScannerStatusString_AllTests');
    return $suite;
  }
}