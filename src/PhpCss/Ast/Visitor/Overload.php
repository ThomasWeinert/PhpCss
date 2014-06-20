<?php
/**
* An abstract visitor class that includes a mapping between functions and classes,
* simulating overloading.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2014 PhpCss Team
*/
namespace PhpCss\Ast\Visitor  {

  use PhpCss\Ast;
  /**
  * An abstract visitor class that includes a mapping between functions and classes,
  * simulating overloading.
  */
  abstract class Overload implements Ast\Visitor {

    /**
     * Map the class name of the PhpCssAst instance to a method name, validate if it exists and return
     * it as callback.
     *
     * @param Ast $object
     * @param string $prefix
     * @return array|null
     */
    protected function getMethodByClass(Ast $object, $prefix = 'visit') {
      $method = $prefix.substr(str_replace('\\', '', get_class($object)), 9);
      if (method_exists($this, $method)) {
        return array($this, $method);
      } else {
        return NULL;
      }
    }

    /**
    * Entering an element in the ast, called before visiting children
    *
    * @param Ast $ast
    * @return boolean
    */
    public function visitEnter(Ast $ast) {
      if ($method = $this->getMethodByClass($ast, 'visitEnter')) {
        return call_user_func($method, $ast);
      }
      return TRUE;
    }

    /**
    * Visiting the $ast element
    *
    * @param Ast $ast
    * @return boolean
    */
    public function visit(Ast $ast) {
      if ($method = $this->getMethodByClass($ast)) {
        return call_user_func($method, $ast);
      }
      return TRUE;
    }

    /**
    * Entering an element in the ast, called after visiting children
    *
    * @param Ast $ast
    * @return boolean
    */
    public function visitLeave(Ast $ast) {
      if ($method = $this->getMethodByClass($ast, 'visitLeave')) {
        return call_user_func($method, $ast);
      }
      return TRUE;
    }
  }
}
