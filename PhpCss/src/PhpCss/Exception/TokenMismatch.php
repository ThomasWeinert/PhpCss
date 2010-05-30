<?php
/**
* Exception thrown if a token is encountered which wasn't expected.
*  
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010 PhpCss Team
*
* @package PhpCss
* @subpackage Exceptions
*/

/**
* Exception thrown if a token is encountered which wasn't expected.
* 
* @package PhpCss
* @subpackage Exceptions 
*/ 

class PhpCssExceptionTokenMismatch extends PhpCssExceptionParser {
  
  /**
  * The token encountered during the scan.
  *
  * This is the token object which was not expected to be found at the given 
  * position. 
  * 
  * @var PhpCssScannerToken
  */
  public $encounteredToken;
    
  public function __construct($encounteredToken, $expectedTokens) {
    $this->encounteredToken = $encounteredToken;
    $this->expectedTokens = $expectedTokens;
    
    $expectedTokenStrings = array();
    foreach($expectedTokens as $expectedToken) {
      $expectedTokenStrings[] = PhpCssScannerToken::typeToString($expectedToken);
    }

    parent::__construct(
     'Parse error: Found '.(string)$encounteredToken .
     ' while one of '.implode(", ", $expectedTokenStrings).' was expected.'
    );
  }
}