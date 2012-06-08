<?php
/**
* Collection of tests for the Selector Sequence List class
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
require_once(dirname(__FILE__).'/../../TestCase.php');

/**
* Test class for PhpCssAstSelectorSequenceList.
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssAstSelectorSequenceTest extends PhpCssTestCase {

  /**
  * @covers PhpCssAstSelectorSequenceList::__construct
  */
  public function testConstructor() {
    $this->assertInstanceOf(
      'PhpCssAstSelectorSequenceList',
      new PhpCssAstSelectorSequenceList()
    );
  }

  /**
  * @covers PhpCssAstSelectorSequenceList::__construct
  * @covers PhpCssAstSelectorSequenceList::getIterator
  */
  public function testConstructorWithSequences() {
    $sequences = array(
      new PhpCssAstSelectorSequence()
    );
    $list = new PhpCssAstSelectorSequenceList(
      $sequences
    );
    $this->assertSame(
      $sequences, iterator_to_array($list)
    );
  }

  /**
  * @covers PhpCssAstSelectorSequenceList::__offsetExists
  */
  public function testOffsetExistsExpectingTrue() {
    $list = new PhpCssAstSelectorSequenceList(
      array(
        new PhpCssAstSelectorSequence()
      )
    );
    $this->assertTrue(isset($list[0]));
  }

  /**
  * @covers PhpCssAstSelectorSequenceList::__offsetExists
  */
  public function testOffsetExistsExpectingFalse() {
    $list = new PhpCssAstSelectorSequenceList();
    $this->assertFalse(isset($list[0]));
  }

  /**
  * @covers PhpCssAstSelectorSequenceList::__offsetGet
  */
  public function testOffsetGet() {
    $list = new PhpCssAstSelectorSequenceList(
      array(
        $sequence = new PhpCssAstSelectorSequence()
      )
    );
    $this->assertSame($sequence, $list[0]);
  }

  /**
  * @covers PhpCssAstSelectorSequenceList::__offsetSet
  */
  public function testOffsetSetAppendsElement() {
    $list = new PhpCssAstSelectorSequenceList();
    $list[] = $sequence = new PhpCssAstSelectorSequence();
    $this->assertSame($sequence, $list[0]);
  }

  /**
  * @covers PhpCssAstSelectorSequenceList::__offsetSet
  */
  public function testOffsetSetReplacesElement() {
    $list = new PhpCssAstSelectorSequenceList(
      array(
        $sequence = new PhpCssAstSelectorSequence()
      )
    );
    $list[0] = $sequence = new PhpCssAstSelectorSequence();
    $this->assertSame($sequence, $list[0]);
  }

  /**
  * @covers PhpCssAstSelectorSequenceList::__offsetSet
  */
  public function testOffsetSetValidatesElementExpectingException() {
    $list = new PhpCssAstSelectorSequenceList();
    $this->setExpectedException('InvalidArgumentException');
    $list[] = 'INVALID TYPE';
  }

  /**
  * @covers PhpCssAstSelectorSequenceList::__offsetGet
  */
  public function testOffsetUnset() {
    $list = new PhpCssAstSelectorSequenceList(
      array(
        new PhpCssAstSelectorSequence()
      )
    );
    unset($list[0]);
    $this->assertFalse(isset($list[0]));
  }
}