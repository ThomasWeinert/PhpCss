<?php
/**
* PhpCss Test Suite
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
require_once(dirname(__FILE__).'/Exception/AllTests.php');
require_once(dirname(__FILE__).'/ParserTest.php');
require_once(dirname(__FILE__).'/ScannerTest.php');
require_once(dirname(__FILE__).'/Scanner/AllTests.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* PhpCss Test Suite
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCss_AllTests {

  public static function suite() {
    $suite = new PHPUnit_Framework_TestSuite('PhpCss');
    $suite->addTestSuite('PhpCssException_AllTests');
    $suite->addTestSuite('PhpCssParserTest');
    $suite->addTestSuite('PhpCssScannerTest');
    $suite->addTestSuite('PhpCssScanner_AllTests');
    return $suite;
  }
}