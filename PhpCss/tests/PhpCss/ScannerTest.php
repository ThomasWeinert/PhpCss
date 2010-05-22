<?php
/**
* Collection of tests for the Scanner class
*
* @version $Id: ScannerTest.php 430 2010-03-29 15:53:43Z subjective $
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
require_once(dirname(__FILE__).'/../../src/PhpCss/Scanner.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for PhpCssScanner.
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssScannerTest extends PHPUnit_Framework_TestCase {

  /**
  * @covers PhpCssScanner::__construct
  */
  public function testConstructor() {
    $status = $this->getMock('PhpCssScannerStatus');
    $scanner = new PhpCssScanner($status);
    $this->assertAttributeSame(
      $status, '_status', $scanner
    );
  }

  /**
  * @covers PhpCssScanner::scan
  * @covers PhpCssScanner::_next
  */
  public function testScanWithSingleValidToken() {
    $token = $this->getTokenMockObjectFixture(6);
    $status = $this->getStatusMockObjectFixture(
      // getToken() returns this elements
      array($token, NULL),
      // isEndToken() returns FALSE
      FALSE
    );
    $status
      ->expects($this->once())
      ->method('getNewStatus')
      ->with($this->equalTo($token))
      ->will($this->returnValue(FALSE));

    $scanner = new PhpCssScanner($status);
    $tokens = array();
    $scanner->scan($tokens, 'SAMPLE');
    $this->assertEquals(
      array($token),
      $tokens
    );
  }

  /**
  * @covers PhpCssScanner::scan
  * @covers PhpCssScanner::_next
  */
  public function testScanWithEndToken() {
    $token = $this->getTokenMockObjectFixture(6);
    $status = $this->getStatusMockObjectFixture(
      // getToken() returns this elements
      array($token),
      // isEndToken() returns TRUE
      TRUE
    );

    $scanner = new PhpCssScanner($status);
    $tokens = array();
    $scanner->scan($tokens, 'SAMPLE');
    $this->assertEquals(
      array($token),
      $tokens
    );
  }

  /**
  * @covers PhpCssScanner::scan
  * @covers PhpCssScanner::_next
  */
  public function testScanWithInvalidToken() {
    $status = $this->getStatusMockObjectFixture(
      array(NULL) // getToken() returns this elements
    );
    $scanner = new PhpCssScanner($status);
    $tokens = array();
    try {
      $scanner->scan($tokens, 'SAMPLE');
      $this->fail('An expected exception has not been occured.');
    } catch (UnexpectedValueException $e) {
    }
  }

  /**
  * @covers PhpCssScanner::scan
  * @covers PhpCssScanner::_next
  * @covers PhpCssScanner::_delegate
  */
  public function testScanWithSubStatus() {
    $tokenOne = $this->getTokenMockObjectFixture(6);
    $tokenTwo = $this->getTokenMockObjectFixture(4);
    $subStatus = $this->getStatusMockObjectFixture(
      // getToken() returns this elements
      array($tokenTwo),
      // isEndToken() returns TRUE
      TRUE
    );
    $status = $this->getStatusMockObjectFixture(
      // getToken() returns this elements
      array($tokenOne, NULL),
      // isEndToken() returns FALSE
      FALSE
    );
    $status
      ->expects($this->once())
      ->method('getNewStatus')
      ->with($this->equalTo($tokenOne))
      ->will($this->returnValue($subStatus));

    $scanner = new PhpCssScanner($status);
    $tokens = array();
    $scanner->scan($tokens, 'SAMPLETEST');
    $this->assertEquals(
      array($tokenOne, $tokenTwo),
      $tokens
    );
  }

  /******************************
  * Fixtures
  ******************************/

  private function getTokenMockObjectFixture($length) {
    $token = $this->getMock('PhpCssScannerToken');
    $token
      ->expects($this->any())
      ->method('__get')
      ->will($this->returnValue($length));
    return $token;
  }

  private function getStatusMockObjectFixture($tokens, $isEndToken = NULL) {
    $status = $this->getMock('PhpCssScannerStatus');
    if (count($tokens) > 0) {
      $status
        ->expects($this->exactly(count($tokens)))
        ->method('getToken')
        ->with(
          $this->isType('string'),
          $this->isType('integer')
         )
        ->will(
          call_user_func_array(
            array($this, 'onConsecutiveCalls'),
            $tokens
          )
        );
    }
    if (!is_null($isEndToken)) {
      $status
        ->expects($this->any())
        ->method('isEndToken')
        ->with($this->isInstanceOf('PhpCssScannerToken'))
        ->will($this->returnValue($isEndToken));
    }
    return $status;
  }
}