<?php

namespace PhpCss\Ast {

  use InvalidArgumentException;
  use PhpCss;
  use PHPUnit\Framework\TestCase;

  require_once(__DIR__.'/../../bootstrap.php');

  class GroupTest extends TestCase {

    /**
     * @covers \PhpCss\Ast\Selector\Group::__construct
     */
    public function testConstructor(): void {
      $this->assertInstanceOf(
        Selector\Group::CLASS,
        new Selector\Group()
      );
    }

    /**
     * @covers \PhpCss\Ast\Selector\Group::__construct
     * @covers \PhpCss\Ast\Selector\Group::getIterator
     */
    public function testConstructorWithSequences(): void {
      $sequences = [
        new Selector\Sequence(),
      ];
      $list = new Selector\Group(
        $sequences
      );
      $this->assertSame(
        $sequences, iterator_to_array($list)
      );
    }

    /**
     * @covers \PhpCss\Ast\Selector\Group::offsetExists
     */
    public function testOffsetExistsExpectingTrue(): void {
      $list = new Selector\Group(
        [
          new Selector\Sequence(),
        ]
      );
      $this->assertTrue(isset($list[0]));
    }

    /**
     * @covers \PhpCss\Ast\Selector\Group::offsetExists
     */
    public function testOffsetExistsExpectingFalse(): void {
      $list = new Selector\Group();
      $this->assertFalse(isset($list[0]));
    }

    /**
     * @covers \PhpCss\Ast\Selector\Group::offsetGet
     */
    public function testOffsetGet(): void {
      $list = new Selector\Group(
        [
          $sequence = new Selector\Sequence(),
        ]
      );
      $this->assertSame($sequence, $list[0]);
    }

    /**
     * @covers \PhpCss\Ast\Selector\Group::offsetSet
     */
    public function testOffsetSetAppendsElement(): void {
      $list = new Selector\Group();
      $list[] = $sequence = new Selector\Sequence();
      $this->assertSame($sequence, $list[0]);
    }

    /**
     * @covers \PhpCss\Ast\Selector\Group::offsetSet
     */
    public function testOffsetSetReplacesElement(): void {
      $list = new Selector\Group(
        [
          new Selector\Sequence(),
        ]
      );
      $list[0] = $sequence = new Selector\Sequence();
      $this->assertSame($sequence, $list[0]);
    }

    /**
     * @covers \PhpCss\Ast\Selector\Group::offsetSet
     */
    public function testOffsetSetValidatesElementExpectingException(): void {
      $list = new Selector\Group();
      $this->expectException(InvalidArgumentException::CLASS);
      $list[] = 'INVALID TYPE';
    }

    /**
     * @covers \PhpCss\Ast\Selector\Group::offsetGet
     */
    public function testOffsetUnset(): void {
      $list = new Selector\Group(
        [
          new Selector\Sequence(),
        ]
      );
      unset($list[0]);
      $this->assertFalse(isset($list[0]));
    }
  }
}
