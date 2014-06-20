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
    * @param PhpCss\Ast $ast
    */
    function visit(PhpCss\Ast $ast);

    /**
    * Visit an ast object
    *
    * @param PhpCss\Ast $ast
    */
    function visitEnter(PhpCss\Ast $ast);

    /**
    * Visit an ast object
    *
    * @param PhpCss\Ast $ast
    */
    function visitLeave(PhpCss\Ast $ast);
  }
}
