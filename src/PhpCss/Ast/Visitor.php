<?php
/**
* Interface declaration for php css ast vistors
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2012 PhpCss Team
*
* @package PhpCss
* @subpackage Ast
*/

/**
* Interface declaration for php css ast vistors
*
* @package PhpCss
* @subpackage Ast
*/
interface PhpCssAstVisitor {

  /**
  * Visit an ast object
  *
  * @param PHPCssAst $ast
  */
  function visit(PHPCssAst $ast);

  /**
  * Visit an ast object
  *
  * @param PHPCssAst $ast
  */
  function visitEnter(PHPCssAst $ast);

  /**
  * Visit an ast object
  *
  * @param PHPCssAst $ast
  */
  function visitLeave(PHPCssAst $ast);
}