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
     * Map the class name of the PhpCss\Ast\Node instance to a method name, validate if it exists and return
     * it as callback.
     *
     * @param Ast\Node $object
     * @param string $prefix
     * @return array|null
     */
    protected function getMethodByClass(Ast\Node $object, string $prefix = 'visit') {
      $method = $prefix.substr(str_replace('\\', '', get_class($object)), 9);
      if (method_exists($this, $method)) {
        return array($this, $method);
      }
      return NULL;
    }

    /**
    * Entering an node in the ast, called before visiting children
    *
    * @param Ast\Node $node
    * @return boolean
    */
    public function visitEnter(Ast\Node $node) {
      if ($method = $this->getMethodByClass($node, 'visitEnter')) {
        return $method($node);
      }
      return TRUE;
    }

    /**
    * Visiting the $node element
    *
    * @param Ast\Node $node
    * @return boolean
    */
    public function visit(Ast\Node $node) {
      if ($method = $this->getMethodByClass($node)) {
        return $method($node);
      }
      return TRUE;
    }

    /**
    * Entering an element in the ast, called after visiting children
    *
    * @param Ast\Node $node
    * @return boolean
    */
    public function visitLeave(Ast\Node $node) {
      if ($method = $this->getMethodByClass($node, 'visitLeave')) {
        return $method($node);
      }
      return TRUE;
    }
  }
}
