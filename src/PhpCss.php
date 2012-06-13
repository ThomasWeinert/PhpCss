<?php
/**
* PhpCss provides several integrative functions to use this library
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2012 PhpCss Team
*
* @package PhpCss
* @subpackage Scanner
*/

require_once(dirname(__FILE__).'/PhpCss/Loader.php');
PhpCssLoader::addAutoloadFile(dirname(__FILE__).'/PhpCss/Loader/All.php');
spl_autoload_register('PhpCssLoader::autoload');

/**
* PhpCss provides several integrative functions to use this library
*
* @package PhpCss
* @subpackage Scanner
*/
class PhpCss {

  /**
  * Parses a css selector and compiles it into an css selector again
  *
  * @param string $cssSelector
  * @return string
  */
  public function reformat($cssSelector) {
    $ast = $this->getAst($cssSelector);
    $visitor = new PhpCssAstVisitorCss();
    $ast->accept($visitor);
    return (string)$visitor;
  }

  /**
  * Parses a css selector and transforms it into an xpath expression
  *
  * @param string $cssSelector
  * @return string
  */
  public function toXpath($cssSelector) {
    $ast = $this->getAst($cssSelector);
    $visitor = new PhpCssAstVisitorXpath();
    $ast->accept($visitor);
    return (string)$visitor;
  }

  /**
  * Parses a css selector and returns the AST
  *
  * @param string $cssSelector
  * @return PhpCssAst
  */
  public function getAst($cssSelector) {
    $tokens = array();
    $scanner = new PhpCssScanner(new PhpCssScannerStatusSelector());
    $scanner->scan($tokens, $cssSelector);
    $parser = new PhpCssParserDefault($tokens);
    return $parser->parse();
  }
}
