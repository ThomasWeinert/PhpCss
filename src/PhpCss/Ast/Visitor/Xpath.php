<?php
/**
* An visitor that compiles the AST into a xpath expression
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2014 PhpCss Team
*/
namespace PhpCss\Ast\Visitor  {

  use PhpCss\Ast;
  use PhpCss\Exception;
  use PhpCss\Parser\Sequence;

  /**
  * An visitor that compiles the AST into a xpath expression
  */
  class Xpath extends Overload {

    /**
     * use explicit namespaces only, no defined namespace means no namespaces. This option and
     * OPTION_DEFAULT_NAMESPACE can not be used at the same time.
     */
    const OPTION_EXPLICIT_NAMESPACES = 1;

    /**
     * use a default namespace, no defined namespace means both no and the default namespace.
     * This option and OPTION_EXPLICT_NAMESPACES can not be used at the same time.
     *
     * If not changed 'html'is used as the additional prefix for elements.
     *
     * Example: foo -> *[(self::foo or self::html:foo)]
     *
     */
    const OPTION_DEFAULT_NAMESPACE = 16;

    /**
     * start expressions in document context
     */
    const OPTION_USE_DOCUMENT_CONTEXT = 2;
    const OPTION_USE_CONTEXT_DOCUMENT = 2;

    /**
     * start expressions in descendant-or-self context
     */
    const OPTION_USE_CONTEXT_SELF = 32;
    /**
     * limit expressions to self context
     */
    const OPTION_USE_CONTEXT_SELF_LIMIT = 64;

    /**
     * lowercase the element names (not the namespace prefixes)
     */
    const OPTION_LOWERCASE_ELEMENTS = 4;
    /**
     * use xml:id and xml:lang not just id or lang
     */
    const OPTION_XML_ATTRIBUTES = 8;

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
     * The default namespace prefix used for elements with no namespace prefix if OPTION_DEFAULT_NAMESPACE is
     * active.
     */
    const DEFAULT_NAMESPACE_PREFIX = 'html';

    /**
     * @var string
     */
    private $_defaultNamespacePrefix = self::DEFAULT_NAMESPACE_PREFIX;

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
     * @param string $defaultPrefix
     */
    public function __construct($options = 0, $defaultPrefix = self::DEFAULT_NAMESPACE_PREFIX) {
      $this->setOptions($options, $defaultPrefix);
    }

    /**
     * Validate and store the options.
     *
     * @param int $options
     * @param string $defaultPrefix
     * @throws \InvalidArgumentException
     */
    public function setOptions($options = 0, $defaultPrefix = self::DEFAULT_NAMESPACE_PREFIX) {
      if (
        $this->hasOption(self::OPTION_EXPLICIT_NAMESPACES) &&
        $this->hasOption(self::OPTION_DEFAULT_NAMESPACE)
      ) {
        throw new \InvalidArgumentException(
          'Options OPTION_EXPLICIT_NAMESPACES and OPTION_DEFAULT_NAMESPACE can not be set at the same time.'
        );
      }
      if (trim($defaultPrefix) == '') {
        throw new \InvalidArgumentException(
          'The default namespace prefix "'.$defaultPrefix.'" is not valid.'
        );
      }
      $this->_options = (int)$options;
      $this->_defaultNamespacePrefix = trim($defaultPrefix);
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
          $this->add($condition);
          return;
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
          }
          return 'concat('.substr($result, 7).')';
        } else {
          return "'".$literal."'";
        }
      } else {
        return '"'.$literal.'"';
      }
    }

    /**
    * Validate the buffer before visiting a Ast\Selector\Group.
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
    public function visitEnterSelectorSequence(Ast\Selector\Sequence $sequence) {
      switch ($this->status()) {
      case self::STATUS_DEFAULT :
        if (!empty($this->_buffer)) {
          $this->add('|');
        }
        if (empty($sequence->simples) && NULL !== $sequence->combinator) {
          return TRUE;
        }
        if ($this->hasOption(self::OPTION_USE_CONTEXT_DOCUMENT)) {
          $this->add('//');
        } elseif ($this->hasOption(self::OPTION_USE_CONTEXT_SELF_LIMIT)) {
          $this->add('self::');
        } elseif ($this->hasOption(self::OPTION_USE_CONTEXT_SELF)) {
          $this->add('descendant-or-self::');
        } else {
          $this->add('.//');
        }
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
     * @param \PhpCss\Ast\Selector\Simple\Universal $universal
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
      $this->status(self::STATUS_ELEMENT);
    }

    /**
    * Output the type (element name) selector to the buffer
    *
    * @param Ast\Selector\Simple\Type $type
    * @return boolean
    */
    public function visitSelectorSimpleType(Ast\Selector\Simple\Type $type) {
      if ($this->hasOption(self::OPTION_LOWERCASE_ELEMENTS)) {
        $elementName = $this->strtolower($type->elementName);
      } else {
        $elementName = $type->elementName;
      }
      if ($this->hasOption(self::OPTION_EXPLICIT_NAMESPACES) && empty($type->namespacePrefix)) {
        $this->add($elementName);
        $this->setElement($elementName);
        $this->status(self::STATUS_ELEMENT);
      } else {
        if (!empty($type->namespacePrefix) && $type->namespacePrefix != '*') {
          $this->add($type->namespacePrefix.':'.$elementName);
          $this->setElement($type->namespacePrefix.':'.$elementName);
          $this->status(self::STATUS_ELEMENT);
        } elseif ($this->hasOption(self::OPTION_DEFAULT_NAMESPACE) && empty($type->namespacePrefix)) {
          $condition = '(self::'.$elementName.' or self::'.$this->_defaultNamespacePrefix.':'.$elementName.')';
          if ($this->status() != self::STATUS_PSEUDOCLASS) {
            $this->setElement('*['.$condition.']');
            $this->add('*');
            $this->status(self::STATUS_ELEMENT);
          }
          $this->addCondition($condition);
        } else {
          $condition = 'local-name() = '.$this->quoteLiteral($elementName);
          if ($this->status() != self::STATUS_PSEUDOCLASS) {
            $this->setElement('*['.$condition.']');
            $this->add('*');
            $this->status(self::STATUS_ELEMENT);
          }
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
      $this->addCondition(
        sprintf(
          '@%1$s = %2$s',
          $this->hasOption(self::OPTION_XML_ATTRIBUTES) ? 'xml:id' : 'id',
          $this->quoteLiteral($id->id)
        )
      );
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
      if ($this->_buffer !== '') {
        $this->add('/');
      }
      $this->status(self::STATUS_COMBINATOR);
    }

    public function visitSelectorCombinatorDescendant() {
      $this->endConditions();
      if ($this->_buffer !== '') {
        $this->add('//');
      } else {
        $this->add('.//');
      }
      $this->status(self::STATUS_COMBINATOR);
    }

    public function visitSelectorCombinatorFollower() {
      $this->endConditions();
      if ($this->_buffer !== '') {
        $this->add('/');
      }
      $this->add('following-sibling::');
      $this->status(self::STATUS_COMBINATOR);
    }

    public function visitSelectorCombinatorNext() {
      $this->endConditions();
      if ($this->_buffer !== '') {
        $this->add('/');
      }
      $this->add('following-sibling::*[1]/self::');
      $this->status(self::STATUS_COMBINATOR);
    }

    public function visitSelectorSimplePseudoClass(Ast\Selector\Simple\PseudoClass $pseudoClass) {
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
      case 'odd' :
        if ($this->status() === self::STATUS_CONDITION) {
          $this->add(']');
          $this->status(self::STATUS_ELEMENT);
        }
        $condition = 'position() mod 2 = 0';
        break;
      case 'even' :
        if ($this->status() === self::STATUS_CONDITION) {
          $this->add(']');
          $this->status(self::STATUS_ELEMENT);
        }
        $condition = 'position() mod 2 = 1';
        break;
      default :
        throw new Exception\NotConvertable('pseudoclass '.$pseudoClass->name, 'Xpath');
      }
      $this->addCondition($condition);
    }

    public function visitEnterSelectorSimplePseudoClass(Ast\Selector\Simple\PseudoClass $pseudoClass) {
      switch ($pseudoClass->name) {
      case 'not' :
        $this->addCondition('not(');
        $this->status(self::STATUS_PSEUDOCLASS);
        return TRUE;
      case 'has' :
        $this->addCondition('(');
        $this->status(self::STATUS_DEFAULT);
        return TRUE;
      case 'contains':
        $this->addCondition('contains(., '.$this->quoteLiteral($pseudoClass->parameter->value));
        $this->status(self::STATUS_PSEUDOCLASS);
        return TRUE;
      case 'gt' :
      case 'lt' :
        if ($this->status() === self::STATUS_CONDITION) {
          $this->add(']');
        }
        $this->status(self::STATUS_ELEMENT);
        $operator = $pseudoClass->name === 'gt' ? '>' : '<';
        $condition = $pseudoClass->parameter->value < 0
          ? 'last() - '.\abs($pseudoClass->parameter->value - 1)
          : $pseudoClass->parameter->value + 1;
        $this->addCondition(
          'position() '.$operator.' '.$condition
        );
        break;
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

    public function visitValuePosition(
      Ast\Value\Position $position
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

    public function visitSelectorSimplePseudoElement(Ast\Selector\Simple\PseudoElement $pseudoElement) {
      throw new Exception\NotConvertable('pseudoelement '.$pseudoElement->name, 'Xpath');
    }

    public function visitValueLanguage(
      Ast\Value\Language $language
    ) {
      $this->addCondition(
        sprintf(
          '(ancestor-or-self::*[@%2$s][1]/@%2$s = %1$s or'.
          ' substring-before(ancestor-or-self::*[@%2$s][1]/@%2$s, "-") = %1$s)',
          $this->quoteLiteral($language->language),
          $this->hasOption(self::OPTION_XML_ATTRIBUTES) ? 'xml:lang' : 'lang'
        )
      );
    }

    /**
     * Use unicode aware strtolower if available
     *
     * @param $string
     * @return string
     */
    private function strtolower($string) {
      if (is_callable('mb_strtolower')) {
        return mb_strtolower($string, 'utf-8');
      } elseif (class_exists('Transliterator', FALSE)) {
        return \Transliterator::create('Any-Lower')->transliterate($string);
      }
      return strtolower($string);
    }
  }
}
