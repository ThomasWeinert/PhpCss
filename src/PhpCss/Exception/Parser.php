<?php
/**
* Exception thrown if a parse error occurs
* 
* @package PhpCss
* @subpackage Exceptions
*/

/**
* Exception thrown if a parse error occurs
*
* A parse error occurs if certain tokens are expected for further parsing, but 
* none of them are found on the token stream 
* 
* @package PhpCss
* @subpackage Exceptions
*/
class PhpCssExceptionParser extends PhpCssException {
  
  /**
  * An array of tokens which would have been expected to be found. 
  * 
  * @var array(PhpCssScannerToken)
  */
  public $expectedTokens = array();   
}