<?php
namespace PhpCss {

  require_once(__DIR__.'/../bootstrap.php');

  class AstTest extends \PHPUnit_Framework_TestCase {

    /**
    * @covers PhpCss\Ast::accept
    */
    public function testAccept() {
      $ast = $this->getMockForAbstractClass(Ast::CLASS);
      $visitor = $this->getMock(Ast\Visitor::CLASS);
      $visitor
        ->expects($this->once())
        ->method('visit')
        ->with($this->equalTo($ast));
      /**
       * @var Ast $ast
       * @var Ast\Visitor $visitor
       */
      $ast->accept($visitor);
    }
  }
}
