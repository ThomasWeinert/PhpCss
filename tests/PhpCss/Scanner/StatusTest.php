<?php
namespace PhpCss\Scanner {

  require_once(__DIR__.'/../../bootstrap.php');

  class StatusTest extends \PHPUnit\Framework\TestCase {

    /**
    * @covers \PhpCss\Scanner\Status::matchPattern
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
    * @covers \PhpCss\Scanner\Status::matchPattern
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
