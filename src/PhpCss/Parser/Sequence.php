<?php
/**
* The Sequence parser parses a list of simple selector tokens into the AST.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2012 PhpCss Team
*/
namespace PhpCss\Parser {

  use PhpCss;
  use PhpCss\Ast;
  use PhpCss\Scanner;

  /**
  * The Sequence parser parses a list of simple selector tokens into the AST.
  *
  * It delegates to separate parsers for pseudo classes and attributes.
  *
  * A css combinator delegates to a new instance of this class.
  */
  class Sequence extends PhpCss\Parser {

    /**
    * Parse the token stream for a simple selector sequence,
    * after the first element the typeselector is not allowed any more,
    * but a combinator is possible.
    */
    public function parse() {
      $sequence = new Ast\Selector\Sequence();
      $token = $this->lookahead(
        array(
          Scanner\Token::IDENTIFIER,
          Scanner\Token::ID_SELECTOR,
          Scanner\Token::CLASS_SELECTOR,
          Scanner\Token::PSEUDO_CLASS,
          Scanner\Token::PSEUDO_ELEMENT,
          Scanner\Token::ATTRIBUTE_SELECTOR_START,
          Scanner\Token::COMBINATOR
        )
      );
      while (isset($token)) {
        if ($selector = $this->createSelector($token)) {
          $this->read($token->type);
          $sequence->simples[] = $selector;
        }
        switch ($token->type) {
        case Scanner\Token::SEPARATOR :
          $this->read(Scanner\Token::SEPARATOR);
          return $sequence;
        case Scanner\Token::PSEUDO_CLASS :
          $sequence->simples[] = $this->delegate(PseudoClass::CLASS);
          break;
        case Scanner\Token::PSEUDO_ELEMENT :
          $sequence->simples[] = $this->createPseudoElement($token);
          $this->read($token->type);
          break;
        case Scanner\Token::ATTRIBUTE_SELECTOR_START :
          $this->read($token->type);
          $sequence->simples[] = $this->delegate(Attribute::CLASS);
          break;
        case Scanner\Token::COMBINATOR :
        case Scanner\Token::WHITESPACE :
          $this->read($token->type);
          $subSequence = $this->delegate(get_class($this));
          /**
           * @var Ast\Selector\Sequence $subSequence
           */
          $sequence->combinator = $this->createCombinator(
            $token, $subSequence
          );
          return $sequence;
        }
        if ($this->endOfTokens()) {
          $token = NULL;
          continue;
        }
        $token = $this->lookahead(
          array(
            Scanner\Token::ID_SELECTOR,
            Scanner\Token::CLASS_SELECTOR,
            Scanner\Token::PSEUDO_CLASS,
            Scanner\Token::PSEUDO_ELEMENT,
            Scanner\Token::ATTRIBUTE_SELECTOR_START,
            Scanner\Token::COMBINATOR,
            Scanner\Token::WHITESPACE,
            Scanner\Token::SEPARATOR
          )
        );
      }
      return $sequence;
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

    private function createCombinator(
      Scanner\Token $token,
      Ast\Selector\Sequence $sequence
    ) {
      switch (trim($token->content)) {
      case '>' :
        return new Ast\Selector\Combinator\Child($sequence);
      case '+' :
        return new Ast\Selector\Combinator\Next($sequence);
      case '~' :
        return new Ast\Selector\Combinator\Follower($sequence);
      default :
        return new Ast\Selector\Combinator\Descendant($sequence);
      }
    }

    private function createPseudoElement($token) {
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
  }
}
