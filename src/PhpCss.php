<?php
/**
* PhpCss provides several integrative functions to use this library
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2014 PhpCss Team
*/

use PhpCss\Exception\ParserException;

/**
* PhpCss provides several integrative functions to use this library
*/
abstract class PhpCss {

  /**
  * Parses a css selector and compiles it into an css selector again
  *
  * @param string $cssSelector
  * @return string
  */
  public static function reformat($cssSelector) {
    $ast = self::getAst($cssSelector);
    $visitor = new PhpCss\Ast\Visitor\Css();
    $ast->accept($visitor);
    return (string)$visitor;
  }

  /**
  * Parses a css selector and transforms it into an xpath expression
  *
  * @param string $cssSelector
  * @param int $options
  * @return string
  */
  public static function toXpath($cssSelector, $options = 0) {
    $ast = self::getAst($cssSelector);
    $visitor = new PhpCss\Ast\Visitor\Xpath($options);
    $ast->accept($visitor);
    return (string)$visitor;
  }

  /**
   * Parses a css selector and returns the AST
   *
   * @param string $cssSelector
   * @return PhpCss\Ast\Node
   * @throws ParserException
   */
  public static function getAst(string $cssSelector): PhpCss\Ast\Node {
    $tokens = array();
    $scanner = new PhpCss\Scanner(new PhpCss\Scanner\Status\Selector());
    $scanner->scan($tokens, $cssSelector);
    $parser = new PhpCss\Parser\Standard($tokens);
    return $parser->parse();
  }
}
