<?php /** @noinspection PhpUnused */

/**
* An ast visitor that compiles a css selector string
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2014 PhpCss Team
*/
namespace PhpCss\Ast\Visitor  {

  use LogicException;
  use PhpCss\Ast;

  /**
  * An ast visitor that compiles a css selector string
  */
  class Css extends Overload {

    private $_buffer = '';
    private $_inSelectorSequence = FALSE;

    /**
    * Clear the visitor object to visit another selector group
    */
    public function clear(): void {
      $this->_buffer = '';
    }

    /**
    * Return the collected selector string
    */
    public function __toString() {
      return $this->_buffer;
    }

    /**
    * Validate the buffer before visiting a Ast\Selector\Group.
    * If the buffer already contains data, throw an exception.
    *
    * @throws LogicException
    * @param Ast\Selector\Group $group
    * @return boolean
    */
    public function visitEnterSelectorGroup(Ast\Selector\Group $group): bool {
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
    * @return boolean
    */
    public function visitEnterSelectorSequence(): bool {
      if ($this->_inSelectorSequence) {
        $this->_buffer .= ', ';
      }
      $this->_inSelectorSequence = TRUE;
      return TRUE;
    }

    /**
    * Output the universal selector to the buffer
    *
    * @param Ast\Selector\Simple\Universal $universal
    * @return boolean
    */
    public function visitSelectorSimpleUniversal(Ast\Selector\Simple\Universal $universal): bool {
      if (!empty($universal->namespacePrefix) && $universal->namespacePrefix !== '*') {
        $this->_buffer .= $universal->namespacePrefix.'|*';
      } else {
        $this->_buffer .= '*';
      }
      return TRUE;
    }

    /**
    * Output the type selector to the buffer
    *
    * @param Ast\Selector\Simple\Type $type
    * @return boolean
    */
    public function visitSelectorSimpleType(Ast\Selector\Simple\Type $type): bool {
      if (!empty($type->namespacePrefix) && $type->namespacePrefix !== '*') {
        $this->_buffer .= $type->namespacePrefix.'|'.$type->elementName;
      } else {
        $this->_buffer .= $type->elementName;
      }
      return TRUE;
    }

    /**
    * Output the class selector to the buffer
    *
    * @param Ast\Selector\Simple\Id $id
    * @return boolean
    */
    public function visitSelectorSimpleId(Ast\Selector\Simple\Id $id): bool {
      $this->_buffer .= '#'.$id->id;
      return TRUE;
    }

    /**
    * Output the class selector to the buffer
    *
    * @param Ast\Selector\Simple\ClassName $class
    * @return boolean
    */
    public function visitSelectorSimpleClassName(Ast\Selector\Simple\ClassName $class): bool {
      $this->_buffer .= '.'.$class->className;
      return TRUE;
    }

    public function visitEnterSelectorCombinatorDescendant(): bool {
      if ($this->_buffer !== '') {
        $this->_buffer .= ' ';
      }
      $this->_inSelectorSequence = FALSE;
      return TRUE;
    }

    public function visitEnterSelectorCombinatorChild(): bool {
      $this->_buffer .= ($this->_buffer !== '') ? ' > ' : '> ';
      $this->_inSelectorSequence = FALSE;
      return TRUE;
    }

    public function visitEnterSelectorCombinatorFollower(): bool {
      $this->_buffer .= ($this->_buffer !== '') ? ' ~ ' : '~ ';
      $this->_inSelectorSequence = FALSE;
      return TRUE;
    }

    public function visitEnterSelectorCombinatorNext(): bool {
      $this->_buffer .= ($this->_buffer !== '') ? ' + ' : '+ ';
      $this->_inSelectorSequence = FALSE;
      return TRUE;
    }

    public function visitSelectorSimpleAttribute(
      Ast\Selector\Simple\Attribute $attribute
    ): bool {
      $this->_buffer .= '[';
      $this->_buffer .= $attribute->name;
      if ($attribute->match !== Ast\Selector\Simple\Attribute::MATCH_EXISTS) {
        $operatorStrings = [
          Ast\Selector\Simple\Attribute::MATCH_PREFIX => '^=',
          Ast\Selector\Simple\Attribute::MATCH_SUFFIX => '$=',
          Ast\Selector\Simple\Attribute::MATCH_SUBSTRING => '*=',
          Ast\Selector\Simple\Attribute::MATCH_EQUALS => '=',
          Ast\Selector\Simple\Attribute::MATCH_INCLUDES => '~=',
          Ast\Selector\Simple\Attribute::MATCH_DASHMATCH => '|='
        ];
        $this->_buffer .= $operatorStrings[$attribute->match];
        $this->_buffer .= $this->quoteString($attribute->literal->value);
      }
      $this->_buffer .= ']';
      return TRUE;
    }

    public function visitSelectorSimplePseudoClass(
      Ast\Selector\Simple\PseudoClass $class
    ): void {
      $this->_buffer .= ':'.$class->name;
      if ($class->parameter) {
        $this->_buffer .= '(';
        $this->visit($class->parameter);
        $this->_buffer .= ')';
      }
    }

    public function visitValueLiteral(
      Ast\Value\Literal $literal
    ): void {
      $this->_buffer .= $this->quoteString($literal->value);
    }

    public function visitValueNumber(
      Ast\Value\Number $literal
    ): void {
      $this->_buffer .= $literal->value;
    }

    public function visitEnterSelectorSimplePseudoClass(
      Ast\Selector\Simple\PseudoClass $class
    ): bool {
      $this->_buffer .= ':'.$class->name.'(';
      return TRUE;
    }

    public function visitLeaveSelectorSimplePseudoClass(): void {
      $this->_buffer .= ')';
    }

    public function visitValuePosition(
      Ast\Value\Position $position
    ): void {
      $repeatsOddEven = $position->repeat === 2;
      if ($repeatsOddEven && $position->add === 1) {
        $this->_buffer .= 'odd';
      } elseif ($repeatsOddEven && $position->add === 0) {
        $this->_buffer .= 'even';
      } elseif ($position->repeat === 0) {
        $this->_buffer .= $position->add;
      } elseif ($position->repeat === 1) {
        $this->_buffer .= 'n';
        if ($position->add !== 0) {
          $this->_buffer .= $position->add >= 0
            ? '+'.$position->add : $position->add;
        }
      } else {
        $this->_buffer .= $position->repeat.'n';
        if ($position->add !== 0) {
          $this->_buffer .= $position->add >= 0
            ? '+'.$position->add : $position->add;
        }
      }
    }

    public function visitValueLanguage(
      Ast\Value\Language $language
    ): void {
      $this->_buffer .= ':lang('.$language->language.')';
    }

    public function visitSelectorSimplePseudoElement(
      Ast\Selector\Simple\PseudoElement $element
    ): void {
      $this->_buffer .= '::'.$element->name;
    }

    private function quoteString(string $string): string {
      return '"'.str_replace(array('\\', '"'), array('\\\\', '\\"'), $string).'"';
    }
  }
}
