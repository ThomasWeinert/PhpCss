<?php
/**
* An abstract visitor class that includes a mapping between functions and classes,
* simulating overloading.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2012 PhpCss Team
*
* @package PhpCss
* @subpackage Ast
*/

/**
* An abstract visitor class that includes a mapping between functions and classes,
* simulating overloading.
*
* @package PhpCss
* @subpackage Ast
*/
abstract class PhpCssAstVisitorOverload implements PhpCssAstVisitor {

  /**
  * Map the class name of the PhpCssAst instance to a method name, validate if it exists and return
  * it as callback.
  *
  * @param PhpCssAst $object
  * @param string $prefix
  * @return callback|NULL
  */
  protected function getMethodByClass(PhpCssAst $object, $prefix = 'visit') {
    $method = $prefix.substr(get_class($object), 9);
    if (method_exists($this, $method)) {
      return array($this, $method);
    } else {
      return NULL;
    }
  }

  /**
  * Entering an element in the ast, called before visting children
  *
  * @param PhpCssAst $ast
  * @return boolean
  */
  public function visitEnter(PhpCssAst $ast) {
    if ($method = $this->getMethodByClass($ast, 'visitEnter')) {
      return call_user_func($method, $ast);
    }
    return TRUE;
  }

  /**
  * Visting the $ast element
  *
  * @param PhpCssAst $ast
  * @return boolean
  */
  public function visit(PhpCssAst $ast) {
    if ($method = $this->getMethodByClass($ast)) {
      return call_user_func($method, $ast);
    }
    return TRUE;
  }

  /**
  * Entering an element in the ast, called after visting children
  *
  * @param PhpCssAst $ast
  * @return boolean
  */
  public function visitLeave(PhpCssAst $ast) {
    if ($method = $this->getMethodByClass($ast, 'visitLeave')) {
      return call_user_func($method, $ast);
    }
    return TRUE;
  }
}