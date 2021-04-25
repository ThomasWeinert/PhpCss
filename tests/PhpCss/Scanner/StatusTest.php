<?php

namespace PhpCss\Scanner {

  use PHPUnit\Framework\TestCase;

  require_once(__DIR__.'/../../bootstrap.php');

  class StatusTest extends TestCase {

    /**
     * @covers \PhpCss\Scanner\Status::matchPattern
     */
    public function testMatchPatternExpectingString(): void {
      /** @var Status_TestProxy $status */
      $status = $this->getMockForAbstractClass(Status_TestProxy::CLASS);
      $this->assertEquals(
        'y',
        $status->matchPattern('xyz', 1, '(y)')
      );
    }

    /**
     * @covers \PhpCss\Scanner\Status::matchPattern
     */
    public function testMatchPatternExpectingNull(): void {
      /** @var Status_TestProxy $status */
      $status = $this->getMockForAbstractClass(Status_TestProxy::CLASS);
      $this->assertNull(
        $status->matchPattern('xyz', 1, '(=)')
      );
    }
  }

  abstract class Status_TestProxy extends Status {

    public function matchPattern(string $buffer, int $offset, string $pattern): ?string {
      return parent::matchPattern($buffer, $offset, $pattern);
    }
  }
}
