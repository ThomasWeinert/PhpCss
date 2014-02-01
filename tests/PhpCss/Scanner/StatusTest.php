<?php
namespace PhpCss\Scanner {

  require_once(__DIR__.'/../../bootstrap.php');

  class StatusTest extends \PHPUnit_Framework_TestCase {

    /**
    * @covers PHPCss\Scanner\Status::matchPattern
     */
    public function testMatchPatternExpectingString() {
      $status = $this->getMockForAbstractClass(Status::CLASS);
      /**
       * @var Status $status
       */
      $this->assertEquals(
        'y',
        $status->matchPattern('xyz', 1, '(y)')
      );
    }
    /**
    * @covers PHPCss\Scanner\Status::matchPattern
     */
    public function testMatchPatternExpectingNull() {
      $status = $this->getMockForAbstractClass(Status::CLASS);
      /**
       * @var Status $status
       */
      $this->assertNull(
        $status->matchPattern('xyz', 1, '(=)')
      );
    }
  }
}