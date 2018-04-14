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
    * @param Node $ast
    */
    function visit(Node $ast);

    /**
    * Visit an ast object
    *
    * @param Node $ast
    */
    function visitEnter(Node $ast);

    /**
    * Visit an ast object
    *
    * @param Node $ast
    */
    function visitLeave(Node $ast);
  }
}
