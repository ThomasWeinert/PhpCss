<?php
/**
* An ast visitor that compiles a css selector string
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2012 PhpCss Team
*
* @package PhpCss
* @subpackage Ast
*/

/**
* An ast visitor that compiles a css selector string
*
* @package PhpCss
* @subpackage Ast
*/
class PhpCssAstVisitorCss extends PhpCssAstVisitorOverload {

  private $_buffer = '';
  private $_inSelectorSequence = FALSE;

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
    if ($this->_inSelectorSequence) {
      $this->_buffer .= ', ';
    }
    $this->_inSelectorSequence = TRUE;
    return TRUE;
  }

  /**
  * Output the universal selector to the buffer
  *
  * @param PhpCssAstSelectorSimpleUniversal $type
  * @return boolean
  */
  public function visitSelectorSimpleUniversal(PhpCssAstSelectorSimpleUniversal $universal) {
    if (!empty($universal->namespacePrefix) && $universal->namespacePrefix != '*') {
      $this->_buffer .= $universal->namespacePrefix.'|*';
    } else {
      $this->_buffer .= '*';
    }
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
      $this->_buffer .= $type->namespacePrefix.'|'.$type->elementName;
    } else {
      $this->_buffer .= $type->elementName;
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
    $this->_buffer .= '#'.$id->id;
    return TRUE;
  }

  /**
  * Output the class selector to the buffer
  *
  * @param PhpCssAstSelectorSimpleClass $class
  * @return boolean
  */
  public function visitSelectorSimpleClass(PhpCssAstSelectorSimpleClass $class) {
    $this->_buffer .= '.'.$class->className;
    return TRUE;
  }

  public function visitEnterSelectorCombinatorDescendant(
    PhpCssAstSelectorCombinatorDescendant $combinator
  ) {
    $this->_buffer .= ' ';
    $this->_inSelectorSequence = FALSE;
    return TRUE;
  }

  public function visitEnterSelectorCombinatorChild(
    PhpCssAstSelectorCombinatorChild $combinator
  ) {
    $this->_buffer .= ' > ';
    $this->_inSelectorSequence = FALSE;
    return TRUE;
  }

  public function visitEnterSelectorCombinatorFollower(
    PhpCssAstSelectorCombinatorFollower $combinator
  ) {
    $this->_buffer .= ' ~ ';
    $this->_inSelectorSequence = FALSE;
    return TRUE;
  }

  public function visitEnterSelectorCombinatorNext(
    PhpCssAstSelectorCombinatorNext $combinator
  ) {
    $this->_buffer .= ' + ';
    $this->_inSelectorSequence = FALSE;
    return TRUE;
  }

  public function visitSelectorSimpleAttribute(
    PhpCssAstSelectorSimpleAttribute $attribute
  ) {
    $this->_buffer .= '[';
    $this->_buffer .= $attribute->name;
    switch ($attribute->match) {
    case PhpCssAstSelectorSimpleAttribute::MATCH_EXISTS :
      $this->_buffer .= ']';
      break;
    default :
      $operatorStrings = array(
        PhpCssAstSelectorSimpleAttribute::MATCH_PREFIX => '^=',
        PhpCssAstSelectorSimpleAttribute::MATCH_SUFFIX => '$=',
        PhpCssAstSelectorSimpleAttribute::MATCH_SUBSTRING => '*=',
        PhpCssAstSelectorSimpleAttribute::MATCH_EQUALS => '=',
        PhpCssAstSelectorSimpleAttribute::MATCH_INCLUDES => '~=',
        PhpCssAstSelectorSimpleAttribute::MATCH_DASHMATCH => '|='
      );
      $this->_buffer .= $operatorStrings[$attribute->match];
      $this->_buffer .= $this->quoteString($attribute->literal);
      $this->_buffer .= ']';
    }
    return TRUE;
  }

  private function quoteString($string) {
    return '"'.str_replace(array('\\', '"'), array('\\\\', '\\"'), $string).'"';
  }
}