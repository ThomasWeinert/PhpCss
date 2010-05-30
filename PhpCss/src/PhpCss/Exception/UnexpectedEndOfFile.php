<?php
/**
* Exception thrown if an unexpected end of file is detected. 
* 
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010 PhpCss Team
*
* @package PhpCss
* @subpackage Exceptions
*/

/**
* Exception thrown if an unexpected end of file is detected.
* 
* @package PhpCss
* @subpackage Exceptions 
*/ 
class PhpCssExceptionUnexpectedEndOfFile extends PhpCssExceptionParser {
  
  public function __construct($expectedTokens) {
    $this->expectedTokens = $expectedTokens;
    
    $expectedTokenStrings = array();
    foreach($expectedTokens as $expectedToken) {
      $expectedTokenStrings[] = PhpCssScannerToken::typeToString($expectedToken);
    }

    parent::__construct(
      'Parse error: Unexpected end of file was found while one of '.
      implode(", ", $expectedTokenStrings).' was expected.'
    );
  }  
}