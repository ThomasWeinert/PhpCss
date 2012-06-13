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
require_once(dirname(__FILE__).'/../TestCase.php');

/**
* Test class for PhpCssAstSelectorGroup.
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssAstSelectorSequenceTest extends PhpCssTestCase {

  /**
  * @covers PhpCssAstSelectorGroup::__construct
  */
  public function testConstructor() {
    $this->assertInstanceOf(
      'PhpCssAstSelectorGroup',
      new PhpCssAstSelectorGroup()
    );
  }

  /**
  * @covers PhpCssAstSelectorGroup::__construct
  * @covers PhpCssAstSelectorGroup::getIterator
  */
  public function testConstructorWithSequences() {
    $sequences = array(
      new PhpCssAstSelectorSequence()
    );
    $list = new PhpCssAstSelectorGroup(
      $sequences
    );
    $this->assertSame(
      $sequences, iterator_to_array($list)
    );
  }

  /**
  * @covers PhpCssAstSelectorGroup::__offsetExists
  */
  public function testOffsetExistsExpectingTrue() {
    $list = new PhpCssAstSelectorGroup(
      array(
        new PhpCssAstSelectorSequence()
      )
    );
    $this->assertTrue(isset($list[0]));
  }

  /**
  * @covers PhpCssAstSelectorGroup::__offsetExists
  */
  public function testOffsetExistsExpectingFalse() {
    $list = new PhpCssAstSelectorGroup();
    $this->assertFalse(isset($list[0]));
  }

  /**
  * @covers PhpCssAstSelectorGroup::__offsetGet
  */
  public function testOffsetGet() {
    $list = new PhpCssAstSelectorGroup(
      array(
        $sequence = new PhpCssAstSelectorSequence()
      )
    );
    $this->assertSame($sequence, $list[0]);
  }

  /**
  * @covers PhpCssAstSelectorGroup::__offsetSet
  */
  public function testOffsetSetAppendsElement() {
    $list = new PhpCssAstSelectorGroup();
    $list[] = $sequence = new PhpCssAstSelectorSequence();
    $this->assertSame($sequence, $list[0]);
  }

  /**
  * @covers PhpCssAstSelectorGroup::__offsetSet
  */
  public function testOffsetSetReplacesElement() {
    $list = new PhpCssAstSelectorGroup(
      array(
        $sequence = new PhpCssAstSelectorSequence()
      )
    );
    $list[0] = $sequence = new PhpCssAstSelectorSequence();
    $this->assertSame($sequence, $list[0]);
  }

  /**
  * @covers PhpCssAstSelectorGroup::__offsetSet
  */
  public function testOffsetSetValidatesElementExpectingException() {
    $list = new PhpCssAstSelectorGroup();
    $this->setExpectedException('InvalidArgumentException');
    $list[] = 'INVALID TYPE';
  }

  /**
  * @covers PhpCssAstSelectorGroup::__offsetGet
  */
  public function testOffsetUnset() {
    $list = new PhpCssAstSelectorGroup(
      array(
        new PhpCssAstSelectorSequence()
      )
    );
    unset($list[0]);
    $this->assertFalse(isset($list[0]));
  }
}