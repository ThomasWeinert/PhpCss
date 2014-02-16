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
    const OPTION_LOWERCASE_ELEMENTS = 4;

    const STATUS_DEFAULT = 0;
    const STATUS_ELEMENT = 1;
    const STATUS_CONDITION = 2;
    const STATUS_COMBINATOR = 3;
    const STATUS_PSEUDOCLASS = 4;

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
     * store expressions for use in visitor methods, the actual expression can depend on
     * the visitor methods called before.
     *
     * @var array
     */
    private $_expressions = [];

    /**
     * store an expression for the current element (type selector)
     * @var string
     */
    private $_element = '*';

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
     * Add a string to the buffer
     *
     * @param string $string
     */
    private function add($string) {
      $this->_buffer .= (string)$string;
    }

    /**
     * Get/Set the current visiting status
     *
     * @param null|int $status
     * @return int
     */
    private function status($status = NULL) {
      if (isset($status)) {
        $this->_status = $status;
      }
      return $this->_status;
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

    private function setElement($element) {
      switch ($this->status()) {
      case self::STATUS_DEFAULT :
      case self::STATUS_COMBINATOR :
        $this->_element = $element;
        break;
      }
    }

    /**
     * prepare buffer to add a condition to the xpath expression
     */
    private function addCondition($condition) {
      if (!empty($condition)) {
        switch ($this->status()) {
        case self::STATUS_DEFAULT :
        case self::STATUS_COMBINATOR :
          $this->setElement('*');
          $this->add('*[');
          break;
        case self::STATUS_PSEUDOCLASS :
        case self::STATUS_ELEMENT :
          $this->add('[');
          break;
        case self::STATUS_CONDITION :
          $this->add(' and ');
          break;
        }
        $this->status(self::STATUS_CONDITION);
        $this->add($condition);
      }
    }

    /**
     * end condition if in condition status
     */
    private function endConditions() {
      if ($this->status() == self::STATUS_CONDITION) {
        $this->add(']');
      }
      $this->status(self::STATUS_DEFAULT);
    }

    /**
     * Quote literal if needed
     *
     * @param string $literal
     * @return string
     */
    private function quoteLiteral($literal) {
      $hasDoubleQuote = FALSE !== strpos($literal, '"');
      if ($hasDoubleQuote) {
        $hasSingleQuote = FALSE !== strpos($literal, "'");
        if ($hasSingleQuote) {
          $result = '';
          $parts = explode('"', $literal);
          foreach ($parts as $part) {
            $result .= ", '\"'";
            if ("" !== $part) {
              $result .= ', "'.$part.'"';
            }
          };
          return 'concat('.substr($result, 7).')';
        } else {
          return "'".$literal."'";
        }
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
      switch ($this->status()) {
      case self::STATUS_DEFAULT :
        if (!empty($this->_buffer)) {
          $this->add('|');
        }
        $this->add($this->hasOption(self::OPTION_USE_DOCUMENT_CONTEXT) ? '//' : './/');
        break;
      case self::STATUS_CONDITION :
        $this->endConditions();
        $this->add('//');
        break;
      }
      return TRUE;
    }

    /**
    * If the visitor is in the condition status, close it.
    *
    * @return boolean
    */
    public function visitLeaveSelectorSequence() {
      $this->endConditions();
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
        $element = $universal->namespacePrefix.':*';
      } else {
        $element = '*';
      }
      $this->setElement($element);
      $this->add($element);
    }

    /**
    * Output the type (element name) selector to the buffer
    *
    * @param Ast\Selector\Simple\Type $type
    * @return boolean
    */
    public function visitSelectorSimpleType(Ast\Selector\Simple\Type $type) {
      if ($this->hasOption(self::OPTION_LOWERCASE_ELEMENTS)) {
        $elementName = strtolower($type->elementName);
      } else {
        $elementName = $type->elementName;
      }
      if ($this->hasOption(self::OPTION_EXPLICT_NAMESPACES)) {
        $this->add($elementName);
        $this->setElement($elementName);
        $this->status(self::STATUS_ELEMENT);
      } else {
        if (!empty($type->namespacePrefix) && $type->namespacePrefix != '*') {
          $this->add($type->namespacePrefix.':'.$elementName);
          $this->setElement($type->namespacePrefix.':'.$elementName);
          $this->status(self::STATUS_ELEMENT);
        } else {
          $condition = 'local-name() = '.$this->quoteLiteral($elementName);
          if ($this->status() != self::STATUS_PSEUDOCLASS) {
            $this->setElement('*['.$condition.']');
            $this->add('*');
          }
          $this->status(self::STATUS_ELEMENT);
          $this->addCondition($condition);
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
      $this->addCondition('@id = "'.$id->id.'"');
      return TRUE;
    }


    /**
    * Output the class selector to the buffer
    *
    * @param Ast\Selector\Simple\ClassName $class
    * @return boolean
    */
    public function visitSelectorSimpleClassName(Ast\Selector\Simple\ClassName $class) {
      $this->addCondition(
        sprintf(
          'contains(concat(" ", normalize-space(@class), " "), " %s ")',
          $class->className
        )
      );
      return TRUE;
    }

    public function visitSelectorSimpleAttribute(
      Ast\Selector\Simple\Attribute $attribute
    ) {
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
        $condition = sprintf(
          '(@%1$s = %2$s or substring-before(@%1$s, "-") = %2$s)',
          $attribute->name,
          $this->quoteLiteral($attribute->literal)
        );
        break;
      case Ast\Selector\Simple\Attribute::MATCH_EXISTS :
      default :
        $condition = '@'.$attribute->name;
        break;
      }
      if (!empty($condition)) {
        $this->addCondition($condition);
      }
      return TRUE;
    }

    public function visitSelectorCombinatorChild() {
      $this->endConditions();
      $this->add('/');
      $this->status(self::STATUS_COMBINATOR);
    }

    public function visitSelectorCombinatorDescendant() {
      $this->endConditions();
      $this->add('//');
      $this->status(self::STATUS_COMBINATOR);
    }

    public function visitSelectorCombinatorFollower() {
      $this->endConditions();
      $this->add('/following-sibling::');
      $this->status(self::STATUS_COMBINATOR);
    }

    public function visitSelectorCombinatorNext() {
      $this->endConditions();
      $this->add('/following-sibling::*[1]/self::');
      $this->status(self::STATUS_COMBINATOR);
    }

    public function visitSelectorSimplePseudoClass(Ast\Selector\Simple\PseudoClass $pseudoClass) {
      $condition = '';
      switch ($pseudoClass->name) {
      case 'root' :
        $condition = '(. = //*)';
        break;
      case 'empty' :
        $condition = '(count(*|text()) = 0)';
        break;
      case 'enabled' :
        $condition = 'not(@disabled)';
        break;
      case 'disabled' :
      case 'checked' :
        $condition = '@'.$pseudoClass->name;
        break;
      case 'first-child' :
        $condition = 'position() = 1';
        break;
      case 'last-child' :
        $condition = 'position() = last()';
        break;
      case 'first-of-type' :
        $condition = '(count(preceding-sibling::'.$this->_element.') = 0)';
        break;
      case 'last-of-type' :
        $condition = '(count(following-sibling::'.$this->_element.') = 0)';
        break;
      case 'only-child' :
        $condition = '(count(parent::*/*|parent::*/text()) = 1)';
        break;
      case 'only-of-type' :
        $condition = '(count(parent::*/'.$this->_element.') = 1)';
        break;
      }
      $this->addCondition($condition);
    }

    public function visitEnterSelectorSimplePseudoClass(Ast\Selector\Simple\PseudoClass $pseudoClass) {
      switch ($pseudoClass->name) {
      case 'not' :
        $this->addCondition('not(');
        $this->status(self::STATUS_PSEUDOCLASS);
        return TRUE;
      case 'nth-child' :
        $this->addCondition('(');
        $this->status(self::STATUS_PSEUDOCLASS);
        $this->_expressions['position'] = 'position()';
        $this->_expressions['count'] = 'last()';
        return TRUE;
      case 'nth-last-child' :
        $this->addCondition('(');
        $this->status(self::STATUS_PSEUDOCLASS);
        $this->_expressions['position'] = '(last() - position() + 1)';
        $this->_expressions['count'] = 'count()';
        return TRUE;
      case 'nth-of-type' :
        $this->addCondition('(');
        $this->status(self::STATUS_PSEUDOCLASS);
        $this->_expressions['position'] = '(count(preceding-sibling::'.$this->_element.') + 1)';
        $this->_expressions['count'] = 'count(parent::*/'.$this->_element.')';
        return TRUE;
      case 'nth-last-of-type' :
        $this->addCondition('(');
        $this->status(self::STATUS_PSEUDOCLASS);
        $this->_expressions['position'] = '(count(following-sibling::'.$this->_element.') + 1)';
        $this->_expressions['count'] = 'count(parent::*/'.$this->_element.')';
        return TRUE;
      }
      return FALSE;
    }

    public function visitLeaveSelectorSimplePseudoClass() {
      $this->endConditions();
      $this->add(')');
      $this->status(self::STATUS_CONDITION);
    }

    public function visitSelectorSimplePseudoClassPosition(
      Ast\Selector\Simple\PseudoClass\Position $position
    ) {
      $repeat = $position->repeat;
      $add = $position->add;
      $expressionPosition = empty($this->_expressions['position'])
        ? 'position()' : $this->_expressions['position'];
      $expressionCount = empty($this->_expressions['count'])
        ? 'last()' : $this->_expressions['count'];
      if ($repeat == 0) {
        $condition = $expressionPosition.' = '.(int)$add;
      } else {
        if ($add > $repeat) {
          $balance = $add - (floor($add / $repeat) * $repeat);
          $start = $add;
        } elseif ($add < 0 and abs($add) > $repeat) {
          $balance = $add - (floor($add / $repeat) * $repeat);
          $start = $add;
        } elseif ($add < 0) {
          $balance = $repeat + $add;
          $start = 1;
        } else {
          $balance = $add;
          $start = 1;
        }
        $condition = sprintf('(%s mod %d) = %d', $expressionPosition, $repeat, $balance);
        if ($start > 1) {
          $condition .= sprintf(' %s >= %d', $expressionPosition, $start);
        } elseif ($start < 0) {
          $condition .= sprintf(' %s <= %s - %d', $expressionPosition, $expressionCount, abs($start));
        }
      }
      $this->add($condition);
    }
  }
}