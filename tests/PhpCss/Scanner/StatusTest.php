<?php

namespace PhpCss\Scanner {

  use PHPUnit\Framework\TestCase;

  require_once(__DIR__.'/../../bootstrap.php');

  class StatusTest extends TestCase {

    /**
     * @covers \PhpCss\Scanner\Status::matchPattern
     */
    public function testMatchPatternExpectingString(): void {
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
    public function testMatchPatternExpectingNull(): void {
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
