<?php
/**
* The attribute parser parses a simple attribute selector.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2012 PhpCss Team
*
* @package PhpCss
* @subpackage Parser
*/

/**
* The attribute parser parses a simple attribute selector.
*
* The attribute value can be an string if a string start is found it delgates to a string
* parser.
*
* @package PhpCss
* @subpackage Parser
*/
class PhpCssParserPseudoClass extends PhpCssParser {

  const PARAMETER_NONE = 1;
  const PARAMETER_IDENTIFIER = 2;
  const PARAMETER_POSITION = 4;
  const PARAMETER_SIMPLE_SELECTOR = 8;


  public function parse() {
    $token = $this->read(PhpCssScannerToken::PSEUDO_CLASS);
    $name = substr($token->content, 1);
    if ($mode = $this->getParameterMode($name)) {
      if ($mode == self::PARAMETER_NONE) {
        return new PhpCssAstSelectorSimplePseudoClass($name);
      }
      $this->read(PhpCssScannerToken::PARENTHESES_START);
      $this->ignore(PhpCssScannerToken::WHITESPACE);
      switch ($mode) {
      case self::PARAMETER_IDENTIFIER :
        $parameterToken = $this->read(PhpCssScannerToken::IDENTIFIER);
        $class = new PhpCssAstSelectorSimplePseudoClassLanguage($parameterToken->content);
        break;
      case self::PARAMETER_POSITION :
        $parameterToken = $this->read(
          array(
            PhpCssScannerToken::IDENTIFIER,
            PhpCssScannerToken::NUMBER,
            PhpCssScannerToken::PSEUDO_CLASS_POSITION
          )
        );
        $class = new PhpCssAstSelectorSimplePseudoClass(
          $name, $this->createPseudoClassPosition($parameterToken->content)
        );
        break;
      case self::PARAMETER_SIMPLE_SELECTOR :
        $parameterToken = $this->lookahead(
          array(
            PhpCssScannerToken::IDENTIFIER,
            PhpCssScannerToken::ID_SELECTOR,
            PhpCssScannerToken::CLASS_SELECTOR,
            PhpCssScannerToken::PSEUDO_CLASS,
            PhpCssScannerToken::PSEUDO_ELEMENT,
            PhpCssScannerToken::ATTRIBUTE_SELECTOR_START
          )
        );
        switch ($parameterToken->type) {
        case PhpCssScannerToken::IDENTIFIER :
        case PhpCssScannerToken::ID_SELECTOR :
        case PhpCssScannerToken::CLASS_SELECTOR :
          $this->read($parameterToken->type);
          $parameter = $this->createSelector($token);
          break;
        case PhpCssScannerToken::PSEUDO_CLASS :
          if ($token->content == ':not') {
            throw new LogicException('not not allowed in not - @todo implement exception');
          }
          $parameter = $this->delegate(self);
          break;
        case PhpCssScannerToken::PSEUDO_ELEMENT :
          $this->read($parameterToken->type);
          $parameter = $this->createPseudoElement($token);
          break;
        case PhpCssScannerToken::ATTRIBUTE_SELECTOR_START :
          $this->read($parameterToken->type);
          $parameter = $this->delegate('PhpCssParserAttribute');
        }
        $class = new PhpCssAstSelectorSimplePseudoClass(
          $name, $parameter
        );
        break;
      }
      $this->ignore(PhpCssScannerToken::WHITESPACE);
      $this->read(PhpCssScannerToken::PARENTHESES_END);
      return $class;
    }
    throw new PhpCssExceptionUnknownPseudoClass($token);
  }

  private function getParameterMode($name) {
    switch ($name) {
    case 'not' :
      return self::PARAMETER_SIMPLE_SELECTOR;
    case 'not' :
      return self::PARAMETER_IDENTIFIER;
    case 'nth-child' :
    case 'nth-last-child' :
    case 'nth-of-type' :
    case 'nth-last-of-type' :
      return self::PARAMETER_POSITION;
    case 'root' :
    case 'first-child' :
    case 'last-child' :
    case 'first-of-type' :
    case 'last-of-type' :
    case 'only-child' :
    case 'only-of-type' :
    case 'empty' :
    case 'link' :
    case 'visited' :
    case 'active' :
    case 'hover' :
    case 'focus' :
    case 'target' :
    case 'enabled' :
    case 'disabled' :
    case 'checked' :
      return self::PARAMETER_NONE;
    }
    return NULL;
  }

  private function createSelector(PhpCssScannerToken $token) {
    switch ($token->type) {
    case PhpCssScannerToken::IDENTIFIER :
      if (FALSE !== strpos($token->content, '|')) {
        list($prefix, $name) = explode('|', $token->content);
      } else {
        $prefix = '*';
        $name = $token->content;
      }
      if ($name == '*') {
        return new PhpCssAstSelectorSimpleUniversal($prefix);
      } else {
        return new PhpCssAstSelectorSimpleType($name, $prefix);
      }
    case PhpCssScannerToken::ID_SELECTOR :
      return new PhpCssAstSelectorSimpleId(substr($token->content, 1));
    case PhpCssScannerToken::CLASS_SELECTOR :
      return new PhpCssAstSelectorSimpleClass(substr($token->content, 1));
    }
    return NULL;
  }

  private function createPseudoElement(PhpCssScannerToken $token) {
    $name = substr($token->content, 2);
    switch ($name) {
    case 'first-line' :
    case 'first-letter' :
    case 'after' :
    case 'before' :
      return new PhpCssAstSelectorSimplePseudoElement($name);
    }
    throw new PhpCssExceptionUnknownPseudoElement($token);
  }

  private function createPseudoClassPosition($string) {
    var_dump('', $string);
    if ($string == 'n') {
      $position = new PhpCssAstSelectorSimplePseudoClassPosition(1, 0);
    } elseif ($string == 'odd') {
      $position = new PhpCssAstSelectorSimplePseudoClassPosition(2, 1);
    } elseif ($string == 'even') {
      $position = new PhpCssAstSelectorSimplePseudoClassPosition(2, 0);
    } elseif (preg_match('(^[+-]?\d+$)D', $string)) {
      $position = new PhpCssAstSelectorSimplePseudoClassPosition(0, (int)$string);
    } elseif (preg_match('(^(?P<repeat>[+-]?\d*)n\s*(?P<add>[+-]\d+)$)D', $string, $matches)) {
      $position = new PhpCssAstSelectorSimplePseudoClassPosition(
        isset($matches['repeat']) && $matches['repeat'] != ''
          ? (int)$matches['repeat'] : 1,
        (int)$matches['add']
      );
    } else {
      throw new LogicException('Invalid pseudo class position - @todo implement exception');
    }
    return $position;
  }
}
