<?php
/**
* Exception thrown if an a pseudo element is found and the name is not known.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2012 PhpCss Team
*
* @package PhpCss
* @subpackage Exceptions
*/

/**
* Exception thrown if an a pseudo element is found and the name is not known.
*
* @package PhpCss
* @subpackage Exceptions
*/
class PhpCssExceptionUnknownPseudoElement extends PhpCssExceptionParser {

  public function __construct($token) {
    parent::__construct(
      sprintf(
        'Parse error: Unknown pseudo element "%s" at character "%d".',
        $token->content,
        $token->position
      )
    );
  }
}