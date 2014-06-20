<?php
/**
* Abstract superclass of all elements in the abstract syntax tree.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2014 PhpCss Team
*/

namespace PhpCss {
  /**
  * Abstract superclass of all elements in the abstract syntax tree.
  */
  abstract class Ast {

    /**
    * The visitors are used to extract information from an ast.
    *
    * @param Ast\Visitor $visitor
    */
    public function accept(Ast\Visitor $visitor) {
      $visitor->visit($this);
    }
  }
}
