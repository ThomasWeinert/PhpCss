<?php
/**
* Collection of test for the PHPCssScannerStatus class
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
require_once(dirname(__FILE__).'/../../../src/PhpCss/Scanner/Status.php');

/**
* Collection of test for the PHPCssScannerStatus class
*
* @package PhpCss
* @subpackage Tests
*/
class PHPCssScannerStatusTest extends PhpCssTestCase {

  /**
  * @covers PHPCssScannerStatus::matchPattern
   */
  public function testMatchPatternExpectingString() {
    $status = new PHPCssScannerStatus_TestProxy();
    $this->assertEquals(
      'y',
      $status->matchPattern('xyz', 1, '(y)')
    );
  }
  /**
  * @covers PHPCssScannerStatus::matchPattern
   */
  public function testMatchPatternExpectingNull() {
    $status = new PHPCssScannerStatus_TestProxy();
    $this->assertNull(
      $status->matchPattern('xyz', 1, '(=)')
    );
  }
}

class PHPCssScannerStatus_TestProxy extends PHPCssScannerStatus {

  public function getToken($buffer, $offset) {
  }

  public function getNewStatus($token) {
  }

  public function isEndToken($token) {
  }
}