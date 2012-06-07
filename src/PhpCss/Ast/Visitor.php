<?php
/**
* Initerface declaration for php css ast vistors
*
* * @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2012 PhpCss Team
*
* @package PhpCss
* @subpackage Ast
*/

/**
* Initerface declaration for php css ast vistors
*
* @package PhpCss
* @subpackage Ast
*/
interface PhpCssAstVisitor {

  function visit(PHPCssAst $ast);
}