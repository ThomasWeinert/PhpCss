<?php
/**
* Exception thrown if an a pseudo element is found and the name is not known.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2014 PhpCss Team
*/

namespace PhpCss\Exception {

  use PhpCss;
  /**
  * Exception thrown if an a pseudo element is found and the name is not known.
  */
  class UnknownPseudoElement extends Parser {

    public function __construct(PhpCss\Scanner\Token $token) {
      parent::__construct(
        sprintf(
          'Parse error: Unknown pseudo element "%s" at character "%d".',
          $token->content,
          $token->position
        )
      );
    }
  }
}