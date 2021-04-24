<?php
/**
 * Interface declaration for php css ast visitors
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright 2010-2014 PhpCss Team
 */

namespace PhpCss\Ast {

  use PhpCss;

  /**
   * Interface declaration for php css ast visitors
   */
  interface Visitor {

    /**
     * Visit an ast object
     *
     * @param Node $astNode
     */
    public function visit(Node $astNode): void;

    /**
     * Visit an ast object
     *
     * @param Node $astNode
     * @return bool
     */
    public function visitEnter(Node $astNode): bool;

    /**
     * Visit an ast object
     *
     * @param Node $astNode
     */
    public function visitLeave(Node $astNode): void;
  }
}
