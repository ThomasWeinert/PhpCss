<?php
/**
* An ast visitor that compiles a dom document explaining the selector
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2014 PhpCss Team
*/
namespace PhpCss\Ast\Visitor  {

  use PhpCss\Ast;

  /**
  * An ast visitor that compiles a dom document explaining the selector
  */
  class Explain extends Overload {

    private $_xmlns = 'urn:carica-phpcss-explain-2014';

    /**
     * @var \DOMDocument
     */
    private $_dom = NULL;

    /**
     * @var \DOMElement
     */
    private $_current = NULL;

    public function __construct() {
      $this->clear();
    }

    /**
    * Clear the visitor object to visit another selector group
    */
    public function clear() {
      $this->_current = $this->_dom = new \DOMDocument();
    }

    /**
    * Return the collected selector string
    */
    public function __toString() {
      return $this->_dom->saveXml();
    }

    /**
     * @param $name
     * @param string $content
     * @param array $attributes
     * @return \DOMNode
     */
    private function appendElement($name, $content = '', array $attributes = array(), $contentType = 'text') {
      $result = $this->_dom->createElementNs($this->_xmlns, $name);
      if (!empty($content)) {
        $text = $result->appendChild(
          $this->_dom->createElementNs($this->_xmlns, $contentType)
        );
        if (trim($content) !== $content) {
          $text->appendChild(
            $this->_dom->createCDATASection($content)
          );
        } else {
          $text->appendChild(
            $this->_dom->createTextNode($content)
          );
        }
      }
      foreach ($attributes as $attribute => $value) {
        $result->setAttribute($attribute, $value);
      }
      $this->_current->appendChild($result);
      return $result;
    }

    /**
     * @param $content
     * @return \DOMNode
     */
    private function appendText($content) {
      $text = $this->_current->appendChild(
        $this->_dom->createElementNs($this->_xmlns, 'text')
      );
      if (trim($content) !== $content) {
        $text->appendChild(
          $this->_dom->createCDATASection($content)
        );
      } else {
        $text->appendChild(
          $this->_dom->createTextNode($content)
        );
      }
      return $text;
    }

    /**
     * Set the provided node as the current element, start a
     * subgroup.
     *
     * @param $node
     * @return bool
     */
    private function start($node) {
      $this->_current = $node;
      return TRUE;
    }

    /**
     * Move the current element to its parent element
     *
     * @return bool
     */
    private function end() {
      $this->_current = $this->_current->parentNode;
      return TRUE;
    }

    /**
    * Validate the buffer before visiting a Ast\Selector\Group.
    * If the buffer already contains data, throw an exception.
    *
    * @throws \LogicException
    * @return boolean
    */
    public function visitEnterSelectorGroup() {
      $this->start($this->appendElement('selector-group'));
      return TRUE;
    }

    /**
    * If here is already data in the buffer, add a separator before starting the next.
    *
    * @return boolean
    */
    public function visitEnterSelectorSequence() {
      if (
        $this->_current === $this->_dom->documentElement &&
        $this->_current->hasChildNodes()
      ) {
        $this
          ->_current
          ->appendChild(
            $this->_dom->createElementNs($this->_xmlns, 'text')
          )
          ->appendChild(
            $this->_dom->createCDATASection(', ')
          );
      }
      return $this->start($this->appendElement('selector'));
    }

    /**
     * @return bool
     */
    public function visitLeaveSelectorSequence() {
      return $this->end();
    }

    /**
    * @param Ast\Selector\Simple\Universal $universal
    * @return boolean
    */
    public function visitSelectorSimpleUniversal(Ast\Selector\Simple\Universal $universal) {
      if (!empty($universal->namespacePrefix) && $universal->namespacePrefix != '*') {
        $css = $universal->namespacePrefix.'|*';
      } else {
        $css = '*';
      }
      $this->appendElement('universal', $css);
      return TRUE;
    }

    /**
     * @param Ast\Selector\Simple\Type $type
     * @return bool
     */
    public function visitSelectorSimpleType(Ast\Selector\Simple\Type $type) {
      if (!empty($type->namespacePrefix) && $type->namespacePrefix != '*') {
        $css = $type->namespacePrefix.'|'.$type->elementName;
      } else {
        $css = $type->elementName;
      }
      $this->appendElement('type', $css);
      return TRUE;
    }

    /**
    * @param Ast\Selector\Simple\Id $id
    * @return boolean
    */
    public function visitSelectorSimpleId(Ast\Selector\Simple\Id $id) {
      $this->appendElement('id', '#'.$id->id);
      return TRUE;
    }

    /**
    * @param Ast\Selector\Simple\ClassName $class
    * @return boolean
    */
    public function visitSelectorSimpleClassName(Ast\Selector\Simple\ClassName $class) {
      $this->appendElement('class', '.'.$class->className);
      return TRUE;
    }

    /**
     * @return bool
     */
    public function visitEnterSelectorCombinatorDescendant() {
      return $this->start($this->appendElement('descendant', ' '));
    }

    /**
     * @return bool
     */
    public function visitLeaveSelectorCombinatorDescendant() {
      return $this->end();
    }

    /**
     * @return bool
     */
    public function visitEnterSelectorCombinatorChild() {
      return $this->start($this->appendElement('child', ' > '));
    }

    /**
     * @return bool
     */
    public function visitLeaveSelectorCombinatorChild() {
      return $this->end();
    }

    /**
     * @return bool
     */
    public function visitEnterSelectorCombinatorFollower() {
      return $this->start($this->appendElement('follower', ' ~ '));
    }

    /**
     * @return bool
     */
    public function visitLeaveSelectorCombinatorFollower() {
      return $this->end();
    }

    /**
     * @return bool
     */
    public function visitEnterSelectorCombinatorNext() {
      return $this->start($this->appendElement('next', ' + '));
    }

    /**
     * @return bool
     */
    public function visitLeaveSelectorCombinatorNext() {
      return $this->end();
    }

    /**
     * @param Ast\Selector\Simple\Attribute $attribute
     * @return bool
     */
    public function visitSelectorSimpleAttribute(
      Ast\Selector\Simple\Attribute $attribute
    ) {
      $operators = array(
        Ast\Selector\Simple\Attribute::MATCH_EXISTS => 'exists',
        Ast\Selector\Simple\Attribute::MATCH_PREFIX => 'prefix',
        Ast\Selector\Simple\Attribute::MATCH_SUFFIX => 'suffix',
        Ast\Selector\Simple\Attribute::MATCH_SUBSTRING => 'substring',
        Ast\Selector\Simple\Attribute::MATCH_EQUALS => 'equals',
        Ast\Selector\Simple\Attribute::MATCH_INCLUDES => 'includes',
        Ast\Selector\Simple\Attribute::MATCH_DASHMATCH => 'dashmatch'
      );
      $this->start(
        $this->appendElement(
          'attribute', '', array('operator' => $operators[$attribute->match])
        )
      );
      $this->appendText('[');
      $this->appendElement('name', $attribute->name);
      if ($attribute->match !== Ast\Selector\Simple\Attribute::MATCH_EXISTS) {
        $operatorStrings = array(
          Ast\Selector\Simple\Attribute::MATCH_PREFIX => '^=',
          Ast\Selector\Simple\Attribute::MATCH_SUFFIX => '$=',
          Ast\Selector\Simple\Attribute::MATCH_SUBSTRING => '*=',
          Ast\Selector\Simple\Attribute::MATCH_EQUALS => '=',
          Ast\Selector\Simple\Attribute::MATCH_INCLUDES => '~=',
          Ast\Selector\Simple\Attribute::MATCH_DASHMATCH => '|='
        );
        $this->appendElement('operator', $operatorStrings[$attribute->match]);
        $this->appendText('"');
        $this->appendElement(
          'value',
          str_replace(array('\\', '"'), array('\\\\', '\\"'), $attribute->literal)
        );
        $this->appendText('"');
      }
      $this->appendText(']');
      $this->end();
      return TRUE;
    }

    /**
     * @param Ast\Selector\Simple\PseudoClass $class
     * @return bool
     */
    public function visitSelectorSimplePseudoClass(
      Ast\Selector\Simple\PseudoClass $class
    ) {
      $this->start($this->appendElement('pseudoclass'));
      $this->appendElement('name', ':'.$class->name);
      return $this->end();
    }

    /**
     * @param Ast\Selector\Simple\PseudoClass $class
     * @return bool
     */
    public function visitEnterSelectorSimplePseudoClass(
      Ast\Selector\Simple\PseudoClass $class
    ) {
      $this->start($this->appendElement('pseudoclass'));
      $this->appendElement('name', ':'.$class->name);
      $this->appendText('(');
      $this->start($this->appendElement('parameter'));
      return TRUE;
    }

    /**
     * @return bool
     */
    public function visitLeaveSelectorSimplePseudoClass() {
      $this->end();
      $this->appendText(')');
      return $this->end();
    }

    /**
     * @param Ast\Value\Literal $literal
     * @return bool
     */
    public function visitValueLiteral(
      Ast\Value\Literal $literal
    ) {
      $this->appendText('"');
      $this->appendElement(
        'value',
        str_replace(array('\\', '"'), array('\\\\', '\\"'), $literal->value)
      );
      $this->appendText('"');
      return TRUE;
    }

    /**
     * @param Ast\Value\Number $number
     * @return bool
     */
    public function visitValueNumber(
      Ast\Value\Number $number
    ) {
      $this->appendElement(
        'value', $number->value, [], 'number'
      );
      return TRUE;
    }

    /**
     * @param Ast\Value\Position $position
     * @return bool
     */
    public function visitValuePosition(
      Ast\Value\Position $position
    ) {
      if ($position->repeat == 2 && $position->add == 1) {
        $css = 'odd';
      } elseif ($position->repeat == 2 && $position->add == 0) {
        $css = 'even';
      } elseif ($position->repeat == 0) {
        $css = $position->add;
      } elseif ($position->repeat == 1) {
        $css = 'n';
        if ($position->add != 0) {
          $css .= $position->add >= 0
            ? '+'.$position->add : $position->add;
        }
      } else {
        $css = $position->repeat.'n';
        if ($position->add != 0) {
          $css .= $position->add >= 0
            ? '+'.$position->add : $position->add;
        }
      }
      $this->appendText($css);
      return TRUE;
    }

    /**
     * @param Ast\Value\Language $language
     * @return bool
     */
    public function visitValueLanguage(
      Ast\Value\Language $language
    ) {
      $this->start($this->appendElement('pseudoclass'));
      $this->appendElement('name', ':lang');
      $this->appendText('(');
      $this->start($this->appendElement('parameter'));
      $this->appendText($language->language);
      $this->end();
      $this->appendText(')');
      return TRUE;
    }

    /**
     * @param Ast\Selector\Simple\PseudoElement $element
     * @return bool
     */
    public function visitSelectorSimplePseudoElement(
      Ast\Selector\Simple\PseudoElement $element
    ) {
      $this->start($this->appendElement('pseudoclass'));
      $this->appendElement('name', '::'.$element->name);
      return $this->end();
    }
  }
}
