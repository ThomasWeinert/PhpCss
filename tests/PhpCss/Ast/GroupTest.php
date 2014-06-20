<?php
namespace PhpCss\Ast {

  use PhpCss;

  require_once(__DIR__.'/../../bootstrap.php');

  class GroupTest extends \PHPUnit_Framework_TestCase {

    /**
    * @covers PhpCss\Ast\Selector\Group::__construct
    */
    public function testConstructor() {
      $this->assertInstanceOf(
        Selector\Group::CLASS,
        new Selector\Group()
      );
    }

    /**
    * @covers PhpCss\Ast\Selector\Group::__construct
    * @covers PhpCss\Ast\Selector\Group::getIterator
    */
    public function testConstructorWithSequences() {
      $sequences = array(
        new Selector\Sequence()
      );
      $list = new Selector\Group(
        $sequences
      );
      $this->assertSame(
        $sequences, iterator_to_array($list)
      );
    }

    /**
    * @covers PhpCss\Ast\Selector\Group::offsetExists
    */
    public function testOffsetExistsExpectingTrue() {
      $list = new Selector\Group(
        array(
          new Selector\Sequence()
        )
      );
      $this->assertTrue(isset($list[0]));
    }

    /**
    * @covers PhpCss\Ast\Selector\Group::offsetExists
    */
    public function testOffsetExistsExpectingFalse() {
      $list = new Selector\Group();
      $this->assertFalse(isset($list[0]));
    }

    /**
    * @covers PhpCss\Ast\Selector\Group::offsetGet
    */
    public function testOffsetGet() {
      $list = new Selector\Group(
        array(
          $sequence = new Selector\Sequence()
        )
      );
      $this->assertSame($sequence, $list[0]);
    }

    /**
    * @covers PhpCss\Ast\Selector\Group::offsetSet
    */
    public function testOffsetSetAppendsElement() {
      $list = new Selector\Group();
      $list[] = $sequence = new Selector\Sequence();
      $this->assertSame($sequence, $list[0]);
    }

    /**
    * @covers PhpCss\Ast\Selector\Group::offsetSet
    */
    public function testOffsetSetReplacesElement() {
      $list = new Selector\Group(
        array(
          $sequence = new Selector\Sequence()
        )
      );
      $list[0] = $sequence = new Selector\Sequence();
      $this->assertSame($sequence, $list[0]);
    }

    /**
    * @covers PhpCss\Ast\Selector\Group::offsetSet
    */
    public function testOffsetSetValidatesElementExpectingException() {
      $list = new Selector\Group();
      $this->setExpectedException(\InvalidArgumentException::CLASS);
      $list[] = 'INVALID TYPE';
    }

    /**
    * @covers PhpCss\Ast\Selector\Group::offsetGet
    */
    public function testOffsetUnset() {
      $list = new Selector\Group(
        array(
          new Selector\Sequence()
        )
      );
      unset($list[0]);
      $this->assertFalse(isset($list[0]));
    }
  }
}
