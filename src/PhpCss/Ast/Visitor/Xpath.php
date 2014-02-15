<?php
/**
* An visitor that compiles the AST into a xpath expression
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2014 PhpCss Team
*/
namespace PhpCss\Ast\Visitor  {

  use PhpCss\Ast;

  /**
  * An visitor that compiles the AST into a xpath expression
  */
  class Xpath extends Overload {

    const OPTION_EXPLICT_NAMESPACES = 1;
    const OPTION_USE_DOCUMENT_CONTEXT = 2;

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
    private $_options = 0;

    /**
     * Create visitor and store mode options
     *
     * @param integer $options
     */
    public function __construct($options = 0) {
      $this->_options = (int)$options;
    }

    /**
    * Clear the visitor object to visit another selector group
    */
    public function clear() {
      $this->_buffer = '';
      $this->_status = self::STATUS_DEFAULT;
    }

    /**
     * Read the status of an option
     *
     * @param $option
     * @return int
     */
    public function hasOption($option) {
      return ($this->_options & $option) == $option;
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
     *
     * @param string $literal
     * @return string
     */
    private function quoteLiteral($literal) {
      if (preg_match('(["])', $literal)) {
        return "'".$literal."'";
      } else {
        return '"'.$literal.'"';
      }
    }

    /**
    * Validate the buffer before vistiting a Ast\Selector\Group.
    * If the buffer already contains data, throw an exception.
    *
    * @throws \LogicException
    * @param Ast\Selector\Group $group
    * @return boolean
    */
    public function visitEnterSelectorSequenceGroup(Ast\Selector\Group $group) {
      if (!empty($this->_buffer)) {
        throw new \LogicException(
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
    * @return boolean
    */
    public function visitEnterSelectorSequence() {
      if (!empty($this->_buffer)) {
        $this->_buffer .= '|';
      }
      $this->_buffer .= $this->hasOption(self::OPTION_USE_DOCUMENT_CONTEXT) ? '//' : './/';
      return TRUE;
    }

    /**
    * If the visitor is in the condition status, close it.
    *
    * @return boolean
    */
    public function visitLeaveSelectorSequence() {
      if ($this->_status == self::STATUS_CONDITION) {
        $this->_buffer .= ']';
      }
      $this->_status = self::STATUS_DEFAULT;
      return TRUE;
    }

    /**
     * Output the universal type (* or xmlns|*) selector to the buffer
     *
     * @param Ast\Selector\Simple\Universal $type
     * @return boolean
     */
    public function visitSelectorSimpleUniversal(Ast\Selector\Simple\Universal $universal) {
      if ($universal->namespacePrefix != '*' && trim($universal->namespacePrefix) != '') {
        $this->_buffer .= $universal->namespacePrefix.':*';
      } else {
        $this->_buffer .= '*';
      }
    }

    /**
    * Output the type (element name) selector to the buffer
    *
    * @param Ast\Selector\Simple\Type $type
    * @return boolean
    */
    public function visitSelectorSimpleType(Ast\Selector\Simple\Type $type) {
      if ($this->hasOption(self::OPTION_EXPLICT_NAMESPACES)) {
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
    * @param Ast\Selector\Simple\Id $id
    * @return boolean
    */
    public function visitSelectorSimpleId(Ast\Selector\Simple\Id $id) {
      $this->prepareCondition();
      $this->_buffer .= '@id = "'.$id->id.'"';
      return TRUE;
    }


    /**
    * Output the class selector to the buffer
    *
    * @param Ast\Selector\Simple\ClassName $class
    * @return boolean
    */
    public function visitSelectorSimpleClassName(Ast\Selector\Simple\ClassName $class) {
      $this->prepareCondition();
      $this->_buffer .= sprintf(
        'contains(concat(" ", normalize-space(@class), " "), " %s ")',
        $class->className
      );
      return TRUE;
    }

    public function visitSelectorSimpleAttribute(
      Ast\Selector\Simple\Attribute $attribute
    ) {
      $condition = '';
      switch ($attribute->match) {
      case Ast\Selector\Simple\Attribute::MATCH_PREFIX :
        $condition = sprintf(
          'starts-with(@%s, %s)',
          $attribute->name,
          $this->quoteLiteral($attribute->literal)
        );
        break;
      case Ast\Selector\Simple\Attribute::MATCH_SUFFIX :
        $condition = sprintf(
          'substring(@%1$s, string-length(@%1$s) - %2$s) = %3$s',
          $attribute->name,
          strlen($attribute->literal),
          $this->quoteLiteral($attribute->literal)
        );
        break;
      case Ast\Selector\Simple\Attribute::MATCH_SUBSTRING :
        $condition = sprintf(
          'contains(@%s, %s)',
          $attribute->name,
          $this->quoteLiteral($attribute->literal)
        );
        break;
      case Ast\Selector\Simple\Attribute::MATCH_EQUALS :
        $condition = '@'.$attribute->name.' = '.$this->quoteLiteral($attribute->literal);
        break;
      case Ast\Selector\Simple\Attribute::MATCH_INCLUDES :
        $condition = sprintf(
          'contains(concat(" ", normalize-space(@%s), " "), %s)',
          $attribute->name,
          $this->quoteLiteral(' '.trim($attribute->literal).' ')
        );
        break;
      case Ast\Selector\Simple\Attribute::MATCH_DASHMATCH :
        break;
      case Ast\Selector\Simple\Attribute::MATCH_EXISTS :
      default :
        $condition = '@'.$attribute->name;
        break;
      }
      if (!empty($condition)) {
        $this->prepareCondition();
        $this->_buffer .= $condition;
      }
      return TRUE;
    }
  }
}