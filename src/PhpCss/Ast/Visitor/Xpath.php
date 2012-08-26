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
class PhpCssAstVisitorXpath extends PhpCssAstVisitorOverload {

  const MODE_NONE = 0;
  const MODE_IGNORE_NAMESPACES = 1;
  const MODE_CASE_INSENSITIVE = 2;

  const STATUS_DEFAULT = 0;
  const STATUS_ELEMENT = 1;
  const STATUS_CONDITION = 2;

  private $_buffer = '';

  /**
   * Current visitor status (position in expression)
   * @var integer
   */
  private $_status = self::STATUS_DEFAULT;

  /**
   * Visitor mode
   * @var integer
   */
  private $_mode = 0;

  /**
   * Create visitor and store mode options
   *
   * @param integer $mode
   */
  public function __construct($mode = self::MODE_NONE) {
    $this->_mode = (int)$mode;
  }

  /**
  * Clear the visitor object to visit another selector group
  */
  public function clear() {
    $this->_buffer = '';
    $this->_status = self::STATUS_DEFAULT;
  }

  /**
  * Return the collected selector string
  */
  public function __toString() {
    return $this->_buffer;
  }

  /**
   * prepare buffer to add a condition to the xpath expression
   */
  private function prepareCondition() {
    switch ($this->_status) {
    case self::STATUS_DEFAULT :
      $this->_buffer .= '*[';
      break;
    case self::STATUS_ELEMENT :
      $this->_buffer .= '[';
      break;
    default :
      $this->_buffer .= ' and ';
      break;
    }
    $this->_status = self::STATUS_CONDITION;
  }

  /**
   * Quote literal if needed
   * @param string $literal
   */
  private function quoteLiteral($literal) {
    if (preg_match('(["])', $literal)) {
      return "'".$literal."'";
    } else {
      return '"'.$literal.'"';
    }
  }

  /**
  * Validate the buffer before vistiting a PhpCssAstSelectorGroup.
  * If the buffer already contains data, throw an exception.
  *
  * @throws LogicException
  * @param PhpCssAstSelectorGroup $group
  * @return boolean
  */
  public function visitEnterSelectorSequenceGroup(PhpCssAstSelectorGroup $group) {
    if (!empty($this->_buffer)) {
      throw new LogicException(
        sprintf(
          'Visitor buffer already contains data, can not visit "%s"',
          get_class($group)
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
    return TRUE;
  }

  /**
  * If the visitor is in the condition status, close it.
  *
  * @param PhpCssAstSelectorSequence $sequence
  * @return boolean
  */
  public function visitLeaveSelectorSequence(PhpCssAstSelectorSequence $sequence) {
    if ($this->_status == self::STATUS_CONDITION) {
      $this->_buffer .= ']';
    }
    $this->_status = self::STATUS_DEFAULT;
    return TRUE;
  }

  /**
  * Output the type selector to the buffer
  *
  * @param PhpCssAstSelectorSimpleType $type
  * @return boolean
  */
  public function visitSelectorSimpleType(PhpCssAstSelectorSimpleType $type) {
    if ($this->_mode & self::MODE_IGNORE_NAMESPACES == self::MODE_IGNORE_NAMESPACES) {
      $this->_buffer .= $type->elementName;
      $this->_status = self::STATUS_ELEMENT;
    } else {
      if (!empty($type->namespacePrefix) && $type->namespacePrefix != '*') {
        $this->_buffer .= $type->namespacePrefix.':'.$type->elementName;
        $this->_status = self::STATUS_ELEMENT;
      } else {
        $this->prepareCondition();
        $this->_buffer .= 'local-name() = "'.$type->elementName.'"';
      }
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
    $this->prepareCondition();
    $this->_buffer .= '@id = "'.$id->id.'"';
    return TRUE;
  }


  /**
  * Output the class selector to the buffer
  *
  * @param PhpCssAstSelectorSimpleClass $class
  * @return boolean
  */
  public function visitSelectorSimpleClass(PhpCssAstSelectorSimpleClass $class) {
    $this->prepareCondition();
    $this->_buffer .= sprintf(
      'contains(concat(" ", normalize-space(@class), " "), " %s ")',
      $class->className
    );
    return TRUE;
  }

  public function visitSelectorSimpleAttribute(
    PhpCssAstSelectorSimpleAttribute $attribute
  ) {
    $this->prepareCondition();
    switch ($attribute->match) {
    case PhpCssAstSelectorSimpleAttribute::MATCH_PREFIX :
      $this->_buffer .=
        'starts-with(@'.$attribute->name.', '.$this->quoteLiteral($attribute->literal).')';
      break;
    case PhpCssAstSelectorSimpleAttribute::MATCH_SUFFIX :
      break;
    case PhpCssAstSelectorSimpleAttribute::MATCH_SUBSTRING :
      break;
    case PhpCssAstSelectorSimpleAttribute::MATCH_EQUALS :
      $this->_buffer .= '@'.$attribute->name.' = '.$this->quoteLiteral($attribute->literal);
      break;
    case PhpCssAstSelectorSimpleAttribute::MATCH_INCLUDES :
      break;
    case PhpCssAstSelectorSimpleAttribute::MATCH_DASHMATCH :
      break;
    }
    return TRUE;
  }
}