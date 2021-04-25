<?php /** @noinspection PhpUnused */

/**
 * An visitor that compiles the AST into a xpath expression
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright 2010-2014 PhpCss Team
 */

namespace PhpCss\Ast\Visitor {

  use InvalidArgumentException;
  use LogicException;
  use PhpCss\Ast;
  use PhpCss\Exception;
  use Transliterator;

  /**
   * An visitor that compiles the AST into a xpath expression
   */
  class Xpath extends Overload {

    /**
     * use explicit namespaces only, no defined namespace means no namespaces. This option and
     * OPTION_DEFAULT_NAMESPACE can not be used at the same time.
     */
    public const OPTION_EXPLICIT_NAMESPACES = 1;

    /**
     * use a default namespace, no defined namespace means both no and the default namespace.
     * This option and OPTION_EXPLICIT_NAMESPACES can not be used at the same time.
     *
     * If not changed 'html' is used as the additional prefix for elements.
     *
     * Example: foo -> *[(self::foo or self::html:foo)]
     *
     */
    public const OPTION_DEFAULT_NAMESPACE = 16;

    /**
     * start expressions in document context
     */
    public const OPTION_USE_DOCUMENT_CONTEXT = 2;
    public const OPTION_USE_CONTEXT_DOCUMENT = 2;

    /**
     * start expressions in descendant-or-self context
     */
    public const OPTION_USE_CONTEXT_SELF = 32;
    /**
     * limit expressions to self context
     */
    public const OPTION_USE_CONTEXT_SELF_LIMIT = 64;

    /**
     * lowercase the element names (not the namespace prefixes)
     */
    public const OPTION_LOWERCASE_ELEMENTS = 4;
    /**
     * use xml:id and xml:lang not just id or lang
     */
    public const OPTION_XML_ATTRIBUTES = 8;

    private const STATUS_DEFAULT = 0;
    private const STATUS_ELEMENT = 1;
    private const STATUS_CONDITION = 2;
    private const STATUS_COMBINATOR = 3;
    private const STATUS_PSEUDOCLASS = 4;

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
    private const DEFAULT_NAMESPACE_PREFIX = 'html';

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
     * @throws InvalidArgumentException
     */
    public function setOptions(
      int $options = 0, string $defaultPrefix = self::DEFAULT_NAMESPACE_PREFIX
    ): void {
      if (
        $this->hasOption(self::OPTION_EXPLICIT_NAMESPACES) &&
        $this->hasOption(self::OPTION_DEFAULT_NAMESPACE)
      ) {
        throw new InvalidArgumentException(
          'Options OPTION_EXPLICIT_NAMESPACES and OPTION_DEFAULT_NAMESPACE can not be set at the same time.'
        );
      }
      if (trim($defaultPrefix) === '') {
        throw new InvalidArgumentException(
          'The default namespace prefix "'.$defaultPrefix.'" is not valid.'
        );
      }
      $this->_options = $options;
      $this->_defaultNamespacePrefix = trim($defaultPrefix);
    }

    /**
     * Clear the visitor object to visit another selector group
     */
    public function clear(): void {
      $this->_buffer = '';
      $this->_status = self::STATUS_DEFAULT;
    }

    /**
     * Add a string to the buffer
     *
     * @param string $string
     */
    private function add(string $string): void {
      $this->_buffer .= $string;
    }

    /**
     * Get/Set the current visiting status
     *
     * @param null|int $status
     * @return int
     */
    private function status($status = NULL): int {
      if (isset($status)) {
        $this->_status = $status;
      }
      return $this->_status;
    }

    /**
     * Read the status of an option
     *
     * @param $option
     * @return bool
     */
    public function hasOption($option): bool {
      return ($this->_options & $option) === $option;
    }

    /**
     * Return the collected selector string
     */
    public function __toString() {
      return $this->_buffer;
    }

    private function setElement($element): void {
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
    private function addCondition($condition): void {
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
    private function endConditions(): void {
      if ($this->status() === self::STATUS_CONDITION) {
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
    private function quoteLiteral(string $literal): string {
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
        }
        return "'".$literal."'";
      }
      return '"'.$literal.'"';
    }

    /**
     * Validate the buffer before visiting a Ast\Selector\Group.
     * If the buffer already contains data, throw an exception.
     *
     * @param Ast\Selector\Group $group
     * @return boolean
     * @throws LogicException
     */
    public function visitEnterSelectorSequenceGroup(Ast\Selector\Group $group): bool {
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
     * @param Ast\Selector\Sequence $sequence
     * @return boolean
     */
    public function visitEnterSelectorSequence(Ast\Selector\Sequence $sequence): bool {
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
    public function visitLeaveSelectorSequence(): bool {
      $this->endConditions();
      return TRUE;
    }

    /**
     * Output the universal type (* or xmlns|*) selector to the buffer
     *
     * @param Ast\Selector\Simple\Universal $universal
     */
    public function visitSelectorSimpleUniversal(Ast\Selector\Simple\Universal $universal): void {
      if ($universal->namespacePrefix !== '*' && trim($universal->namespacePrefix) !== '') {
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
     */
    public function visitSelectorSimpleType(Ast\Selector\Simple\Type $type): void {
      if ($this->hasOption(self::OPTION_LOWERCASE_ELEMENTS)) {
        $elementName = $this->strToLower($type->elementName);
      } else {
        $elementName = $type->elementName;
      }
      if ('' === $type->namespacePrefix && $this->hasOption(self::OPTION_EXPLICIT_NAMESPACES)) {
        $this->add($elementName);
        $this->setElement($elementName);
        $this->status(self::STATUS_ELEMENT);
      } else {
        $isEmptyPrefix = !isset($type->namespacePrefix) || $type->namespacePrefix === '';
        if (!$isEmptyPrefix && $type->namespacePrefix !== '*') {
          $this->add($type->namespacePrefix.':'.$elementName);
          $this->setElement($type->namespacePrefix.':'.$elementName);
          $this->status(self::STATUS_ELEMENT);
        } elseif ($isEmptyPrefix && $this->hasOption(self::OPTION_DEFAULT_NAMESPACE)) {
          $condition = '(self::'.$elementName.' or self::'.$this->_defaultNamespacePrefix.':'.$elementName.')';
          if ($this->status() !== self::STATUS_PSEUDOCLASS) {
            $this->setElement('*['.$condition.']');
            $this->add('*');
            $this->status(self::STATUS_ELEMENT);
          }
          $this->addCondition($condition);
        } else {
          $condition = 'local-name() = '.$this->quoteLiteral($elementName);
          if ($this->status() !== self::STATUS_PSEUDOCLASS) {
            $this->setElement('*['.$condition.']');
            $this->add('*');
            $this->status(self::STATUS_ELEMENT);
          }
          $this->addCondition($condition);
        }
      }
    }

    /**
     * Output the class selector to the buffer
     *
     * @param Ast\Selector\Simple\Id $id
     */
    public function visitSelectorSimpleId(Ast\Selector\Simple\Id $id): void {
      $this->addCondition(
        sprintf(
          '@%1$s = %2$s',
          $this->hasOption(self::OPTION_XML_ATTRIBUTES) ? 'xml:id' : 'id',
          $this->quoteLiteral($id->id)
        )
      );
    }


    /**
     * Output the class selector to the buffer
     *
     * @param Ast\Selector\Simple\ClassName $class
     */
    public function visitSelectorSimpleClassName(Ast\Selector\Simple\ClassName $class): void {
      $this->addCondition(
        sprintf(
          'contains(concat(" ", normalize-space(@class), " "), " %s ")',
          $class->className
        )
      );
    }

    public function visitSelectorSimpleAttribute(
      Ast\Selector\Simple\Attribute $attribute
    ): void {
      switch ($attribute->match) {
      case Ast\Selector\Simple\Attribute::MATCH_PREFIX :
        $condition = sprintf(
          'starts-with(@%s, %s)',
          $attribute->name,
          $this->quoteLiteral($attribute->literal->value)
        );
        break;
      case Ast\Selector\Simple\Attribute::MATCH_SUFFIX :
        $condition = sprintf(
          'substring(@%1$s, string-length(@%1$s) - %2$s) = %3$s',
          $attribute->name,
          strlen($attribute->literal->value),
          $this->quoteLiteral($attribute->literal->value)
        );
        break;
      case Ast\Selector\Simple\Attribute::MATCH_SUBSTRING :
        $condition = sprintf(
          'contains(@%s, %s)',
          $attribute->name,
          $this->quoteLiteral($attribute->literal->value)
        );
        break;
      case Ast\Selector\Simple\Attribute::MATCH_EQUALS :
        $condition = '@'.$attribute->name.' = '.$this->quoteLiteral($attribute->literal->value);
        break;
      case Ast\Selector\Simple\Attribute::MATCH_INCLUDES :
        $condition = sprintf(
          'contains(concat(" ", normalize-space(@%s), " "), %s)',
          $attribute->name,
          $this->quoteLiteral(' '.trim($attribute->literal->value).' ')
        );
        break;
      case Ast\Selector\Simple\Attribute::MATCH_DASHMATCH :
        $condition = sprintf(
          '(@%1$s = %2$s or substring-before(@%1$s, "-") = %2$s)',
          $attribute->name,
          $this->quoteLiteral($attribute->literal->value)
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
    }

    public function visitSelectorCombinatorChild(): void {
      $this->endConditions();
      if ($this->_buffer !== '') {
        $this->add('/');
      }
      $this->status(self::STATUS_COMBINATOR);
    }

    public function visitSelectorCombinatorDescendant(): void {
      $this->endConditions();
      if ($this->_buffer !== '') {
        $this->add('//');
      } else {
        $this->add('.//');
      }
      $this->status(self::STATUS_COMBINATOR);
    }

    public function visitSelectorCombinatorFollower(): void {
      $this->endConditions();
      if ($this->_buffer !== '') {
        $this->add('/');
      }
      $this->add('following-sibling::');
      $this->status(self::STATUS_COMBINATOR);
    }

    public function visitSelectorCombinatorNext(): void {
      $this->endConditions();
      if ($this->_buffer !== '') {
        $this->add('/');
      }
      $this->add('following-sibling::*[1]/self::');
      $this->status(self::STATUS_COMBINATOR);
    }

    /**
     * @throws Exception\NotConvertibleException
     */
    public function visitSelectorSimplePseudoClass(Ast\Selector\Simple\PseudoClass $pseudoClass): void {
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
        throw new Exception\NotConvertibleException('pseudoclass '.$pseudoClass->name, 'Xpath');
      }
      $this->addCondition($condition);
    }

    public function visitEnterSelectorSimplePseudoClass(Ast\Selector\Simple\PseudoClass $pseudoClass): bool {
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
        if (
          ($parameter = $pseudoClass->parameter) &&
          ($parameter instanceof Ast\Value\Number || $parameter instanceof Ast\Value\Literal)
        ) {
          $this->addCondition('contains(., '.$this->quoteLiteral($parameter->value));
          $this->status(self::STATUS_PSEUDOCLASS);
        }
        return TRUE;
      case 'gt' :
      case 'lt' :
        if (
          ($parameter = $pseudoClass->parameter) &&
          ($parameter instanceof Ast\Value\Number || $parameter instanceof Ast\Value\Literal)
        ) {
          if ($this->status() === self::STATUS_CONDITION) {
            $this->add(']');
          }
          $this->status(self::STATUS_ELEMENT);
          $operator = $pseudoClass->name === 'gt' ? '>' : '<';
          $condition = $parameter->value < 0
            ? 'last() - '.abs($parameter->value - 1)
            : $parameter->value + 1;
          $this->addCondition(
            'position() '.$operator.' '.$condition
          );
        }
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

    public function visitLeaveSelectorSimplePseudoClass(): void {
      $this->endConditions();
      $this->add(')');
      $this->status(self::STATUS_CONDITION);
    }

    public function visitValuePosition(
      Ast\Value\Position $position
    ): void {
      $repeat = $position->repeat;
      $add = $position->add;
      $expressionPosition = empty($this->_expressions['position'])
        ? 'position()' : $this->_expressions['position'];
      $expressionCount = empty($this->_expressions['count'])
        ? 'last()' : $this->_expressions['count'];
      if ($repeat === 0) {
        $condition = $expressionPosition.' = '.$add;
      } else {
        if ($add > $repeat) {
          $balance = $add - (floor($add / $repeat) * $repeat);
          $start = $add;
        } elseif ($add < 0) {
          if (abs($add) > $repeat) {
            $balance = $add - (floor($add / $repeat) * $repeat);
            $start = $add;
          } else {
            $balance = $repeat + $add;
            $start = 1;
          }
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

    /**
     * @throws Exception\NotConvertibleException
     */
    public function visitSelectorSimplePseudoElement(Ast\Selector\Simple\PseudoElement $pseudoElement): void {
      throw new Exception\NotConvertibleException('pseudoelement '.$pseudoElement->name, 'Xpath');
    }

    public function visitValueLanguage(
      Ast\Value\Language $language
    ): void {
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
     * @param string $string
     * @return string
     */
    private function strToLower(string $string): string {
      if (is_callable('mb_strtolower')) {
        return mb_strtolower($string, 'utf-8');
      }
      if (class_exists('Transliterator', FALSE)) {
        $transliterator = Transliterator::create('Any-Lower');
        if ($transliterator) {
          return $transliterator->transliterate($string);
        }
      }
      return strtolower($string);
    }
  }
}
