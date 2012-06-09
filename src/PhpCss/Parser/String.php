<?php
/**
* The string parser collects all string character tokens until a string end token is found.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2012 PhpCss Team
*
* @package PhpCss
* @subpackage Parser
*/

/**
* The string parser collects all string character tokens until a string end token is found.
*
* @package PhpCss
* @subpackage Parser
*/
class PhpCssParserString extends PhpCssParser {

  public function parse() {
    $string = '';
    while (TRUE) {
      $token = $this->read(
        array(
          PhpCssScannerToken::STRING_CHARACTERS,
          PhpCssScannerToken::STRING_ESCAPED_CHARACTER,
          PhpCssScannerToken::SINGLEQUOTE_STRING_END,
          PhpCssScannerToken::DOUBLEQUOTE_STRING_END
        )
      );
      switch ($token->type) {
      case PhpCssScannerToken::STRING_CHARACTERS :
        $string .= $token->content;
        break;
      case PhpCssScannerToken::STRING_ESCAPED_CHARACTER :
        $string .= substr($token->content, 1);
        break;
      case PhpCssScannerToken::SINGLEQUOTE_STRING_END :
      case PhpCssScannerToken::DOUBLEQUOTE_STRING_END :
        return $string;
      }
    }
  }
}