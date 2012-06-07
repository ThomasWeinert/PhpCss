<?php
/**
* Abstract superclass of all elements in the abstract syntax tree.
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010 PhpCss Team
*
* @package PhpCss
* @subpackage Ast
*/

/**
* Abstract superclass of all elements in the abstract syntax tree.
*
* @package PhpCss
* @subpackage Ast
*/
abstract class PhpCssAst {

  /**
  * The visitors are used to extract information from an ast.
  *
  * @param PhpCssAstVisitor $visitor
  */
  public function accept(PhpCssAstVisitor $visitor) {
    $visitor->visit($this);
  }

}