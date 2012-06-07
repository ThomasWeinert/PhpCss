<?php
/**
* An visitor that compiles the AST into a xpath expression
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2012 PhpCss Team
*
* @package PhpCss
* @subpackage Ast
*/

/**
* An visitor that compiles the AST into a xpath expression
*
* @package PhpCss
* @subpackage Ast
*/
class PhpCssAstVisitorXpath implements PhpCssAstVisitor {

  private $_buffer = '';

  /**
  * Clear the visitor object to visit another sequence list
  */
  public function clear() {
    $this->_buffer = '';
  }

  /**
  * Return the collected selector string
  */
  public function __toString() {
    return $this->_buffer;
  }

  /**
  * Map the class name of the PhpCssAst instance to a method name, validate if it exists and return
  * it as callback.
  *
  * @param PhpCssAst $object
  * @param string $prefix
  * @return callback|NULL
  */
  private function getMethodByClass(PhpCssAst $object, $prefix = 'visit') {
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

  /**
  * Validate the buffer before vistiting a PhpCssAstSelectorSequenceList.
  * If the buffer already contains data, throw an exception.
  *
  * @throws LogicException
  * @param PhpCssAstSelectorSequenceList $list
  * @return boolean
  */
  public function visitEnterSelectorSequenceList(PhpCssAstSelectorSequenceList $list) {
    if (!empty($this->_buffer)) {
      throw new LogicException(
        sprintf(
          'Visitor buffer already contains data, can not visit "%s"',
          get_class($list)
        )
      );
    }
    return TRUE;
  }

  /**
  * If here is already data in the buffer, add a separator before starting the next.
  *
  * @param PhpCssAstSelectorSequence $sequence
  * @return boolean
  */
  public function visitEnterSelectorSequence(PhpCssAstSelectorSequence $sequence) {
    if (!empty($this->_buffer)) {
      $this->_buffer .= '|';
    }
    $this->_buffer .= '*';
    return TRUE;
  }

  /**
  * Output the type selector to the buffer
  *
  * @param PhpCssAstSelectorSimpleType $type
  * @return boolean
  */
  public function visitSelectorSimpleType(PhpCssAstSelectorSimpleType $type) {
    if (!empty($type->namespacePrefix) && $type->namespacePrefix != '*') {
      $this->_buffer .= '[name() = "'.$type->namespacePrefix.':'.$type->elementName.'"]';
    } else {
      $this->_buffer .= '[local-name() = "'.$type->elementName.'"]';
    }
    return TRUE;
  }

  /**
  * Output the class selector to the buffer
  *
  * @param PhpCssAstSelectorSimpleId $class
  * @return boolean
  */
  public function visitSelectorSimpleId(PhpCssAstSelectorSimpleId $id) {
    $this->_buffer .= '[@id = "#'.$id->id.']';
    return TRUE;
  }


  /**
  * Output the class selector to the buffer
  *
  * @param PhpCssAstSelectorSimpleClass $class
  * @return boolean
  */
  public function visitSelectorSimpleClass(PhpCssAstSelectorSimpleClass $class) {
    $this->_buffer .= sprintf(
      '[contains(concat(" ", normalize-space(@class), " "), " %s ")]."',
      $class->className
    );
    return TRUE;
  }
}