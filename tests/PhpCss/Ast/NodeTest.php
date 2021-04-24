<?php

namespace PhpCss {

  require_once __DIR__.'/../../bootstrap.php';

  class NodeTest extends \PHPUnit\Framework\TestCase {

    /**
     * @covers \PhpCss\Ast\Node::accept
     */
    public function testAccept(): void {
      $node = $this->getMockForAbstractClass(Ast\Node::CLASS);
      $visitor = $this->createMock(Ast\Visitor::CLASS);
      $visitor
        ->expects($this->once())
        ->method('visit')
        ->with($this->equalTo($node));
      /**
       * @var Ast\Node $node
       * @var Ast\Visitor $visitor
       */
      $node->accept($visitor);
    }
  }
}
