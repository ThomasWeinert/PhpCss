<?php
/**
* Collection of tests for the abstract ast superclass
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2010 Bastian Feder, Thomas Weinert
*
* @package PhpCss
* @subpackage Tests
*/

/**
* Load necessary files
*/
require_once(dirname(__FILE__).'/TestCase.php');

/**
* Test class for PhpCssAst.
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssAstTest extends PhpCssTestCase {

  /**
  * @covers PhpCssAst::accept
  */
  public function testAccept() {
    $ast = new PhpCssAst_TestProxy();
    $visitor = $this->getMock('PhpCssAstVisitor');
    $visitor
      ->expects($this->once())
      ->method('visit')
      ->with($this->equalTo($ast));
    $ast->accept($visitor);
  }
}

class PhpCssAst_TestProxy extends PhpCssAst {
}

