<?php
/**
* The attribute parser parses a simple attribute selector.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2014 PhpCss Team
*/
namespace PhpCss\Parser {

  use PhpCss;
  use PhpCss\Ast;
  use PhpCss\Scanner;
  /**
  * The attribute parser parses a simple attribute selector.
  *
  * The attribute value can be an string if a string start is found it delegates to a string
  * parser.
  */
  class PseudoClassParser extends PhpCss\Parser {

    const PARAMETER_NONE = 1;
    const PARAMETER_IDENTIFIER = 2;
    const PARAMETER_POSITION = 4;
    const PARAMETER_SIMPLE_SELECTOR = 8;


    public function parse() {
      $token = $this->read(Scanner\Token::PSEUDO_CLASS);
      $name = substr($token->content, 1);
      if ($mode = $this->getParameterMode($name)) {
        if ($mode == self::PARAMETER_NONE) {
          return new Ast\Selector\Simple\PseudoClass($name);
        }
        $this->read(Scanner\Token::PARENTHESES_START);
        $this->ignore(Scanner\Token::WHITESPACE);
        switch ($mode) {
        case self::PARAMETER_IDENTIFIER :
          $parameterToken = $this->read(Scanner\Token::IDENTIFIER);
          $class = new Ast\Selector\Simple\PseudoClass\Language($parameterToken->content);
          break;
        case self::PARAMETER_POSITION :
          $parameterToken = $this->read(
            array(
              Scanner\Token::IDENTIFIER,
              Scanner\Token::NUMBER,
              Scanner\Token::PSEUDO_CLASS_POSITION
            )
          );
          $class = new Ast\Selector\Simple\PseudoClass(
            $name, $this->createPseudoClassPosition($parameterToken->content)
          );
          break;
        case self::PARAMETER_SIMPLE_SELECTOR :
          $parameterToken = $this->lookahead(
            array(
              Scanner\Token::IDENTIFIER,
              Scanner\Token::ID_SELECTOR,
              Scanner\Token::CLASS_SELECTOR,
              Scanner\Token::PSEUDO_CLASS,
              Scanner\Token::PSEUDO_ELEMENT,
              Scanner\Token::ATTRIBUTE_SELECTOR_START
            )
          );
          switch ($parameterToken->type) {
          case Scanner\Token::IDENTIFIER :
          case Scanner\Token::ID_SELECTOR :
          case Scanner\Token::CLASS_SELECTOR :
            $this->read($parameterToken->type);
            $parameter = $this->createSelector($parameterToken);
            break;
          case Scanner\Token::PSEUDO_CLASS :
            if ($parameterToken->content == ':not') {
              throw new \LogicException('not not allowed in not - @todo implement exception');
            }
            $parameter = $this->delegate(PseudoClassParser::CLASS);
            break;
          case Scanner\Token::PSEUDO_ELEMENT :
            $this->read($parameterToken->type);
            $parameter = $this->createPseudoElement($parameterToken);
            break;
          case Scanner\Token::ATTRIBUTE_SELECTOR_START :
            $this->read($parameterToken->type);
            $parameter = $this->delegate(AttributeParser::CLASS);
            break;
          default :
            $parameter = NULL;
          }
          $class = new Ast\Selector\Simple\PseudoClass(
            $name, $parameter
          );
          break;
        default :
          $class = NULL;
        }
        $this->ignore(Scanner\Token::WHITESPACE);
        $this->read(Scanner\Token::PARENTHESES_END);
        return $class;
      }
      throw new PhpCss\Exception\UnknownPseudoClass($token);
    }

    private function getParameterMode($name) {
      switch ($name) {
      case 'not' :
        return self::PARAMETER_SIMPLE_SELECTOR;
      case 'lang' :
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

    private function createSelector(Scanner\Token $token) {
      switch ($token->type) {
      case Scanner\Token::IDENTIFIER :
        if (FALSE !== strpos($token->content, '|')) {
          list($prefix, $name) = explode('|', $token->content);
        } else {
          $prefix = '';
          $name = $token->content;
        }
        if ($name == '*') {
          return new Ast\Selector\Simple\Universal($prefix);
        } else {
          return new Ast\Selector\Simple\Type($name, $prefix);
        }
      case Scanner\Token::ID_SELECTOR :
        return new Ast\Selector\Simple\Id(substr($token->content, 1));
      case Scanner\Token::CLASS_SELECTOR :
        return new Ast\Selector\Simple\ClassName(substr($token->content, 1));
      }
      return NULL;
    }

    private function createPseudoElement(Scanner\Token $token) {
      $name = substr($token->content, 2);
      switch ($name) {
      case 'first-line' :
      case 'first-letter' :
      case 'after' :
      case 'before' :
        return new Ast\Selector\Simple\PseudoElement($name);
      }
      throw new PhpCss\Exception\UnknownPseudoElement($token);
    }

    private function createPseudoClassPosition($string) {
      $string = str_replace(' ', '', $string);
      if ($string == 'n') {
        $position = new Ast\Selector\Simple\PseudoClass\Position(1, 0);
      } elseif ($string == 'odd') {
        $position = new Ast\Selector\Simple\PseudoClass\Position(2, 1);
      } elseif ($string == 'even') {
        $position = new Ast\Selector\Simple\PseudoClass\Position(2, 0);
      } elseif (preg_match('(^[+-]?\d+$)D', $string)) {
        $position = new Ast\Selector\Simple\PseudoClass\Position(0, (int)$string);
      } elseif (
          preg_match('(^(?P<repeat>\d+)n$)D', $string, $matches) ||
          preg_match('(^(?P<repeat>[+-]?\d*)n(?P<add>[+-]\d+)$)D', $string, $matches)
        ) {
        $position = new Ast\Selector\Simple\PseudoClass\Position(
          isset($matches['repeat']) && $matches['repeat'] != ''
            ? (int)$matches['repeat'] : 1,
          isset($matches['add']) ? (int)$matches['add'] : 0
        );
      } else {
        throw new \LogicException('Invalid pseudo class position - @todo implement exception');
      }
      return $position;
    }
  }
}
