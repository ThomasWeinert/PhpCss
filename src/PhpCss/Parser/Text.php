<?php
/**
* The string parser collects all string character tokens until a string end token is found.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2014 PhpCss Team
*/

namespace PhpCss\Parser {

  use PhpCss;
  use PhpCss\Scanner;

  /**
  * The string parser collects all string character tokens until a string end token is found.
  */
  class Text extends PhpCss\Parser {

    public function parse() {
      $string = '';
      while (TRUE) {
        $token = $this->read(
          array(
            Scanner\Token::STRING_CHARACTERS,
            Scanner\Token::STRING_ESCAPED_CHARACTER,
            Scanner\Token::SINGLEQUOTE_STRING_END,
            Scanner\Token::DOUBLEQUOTE_STRING_END
          )
        );
        switch ($token->type) {
        case Scanner\Token::STRING_CHARACTERS :
          $string .= $token->content;
          break;
        case Scanner\Token::STRING_ESCAPED_CHARACTER :
          $string .= substr($token->content, 1);
          break;
        case Scanner\Token::SINGLEQUOTE_STRING_END :
        case Scanner\Token::DOUBLEQUOTE_STRING_END :
          return $string;
        }
      }
      return $string;
    }
  }
}
