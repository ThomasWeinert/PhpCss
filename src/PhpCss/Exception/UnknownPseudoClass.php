<?php
/**
* Exception thrown if an a pseudo class is found and the name is not known.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2014 PhpCss Team
*/

namespace PhpCss\Exception {

  use PhpCss;

  /**
  * Exception thrown if an a pseudo class is found and the name is not known.
  */
  class UnknownPseudoClass extends Token {

    /**
     * @param PhpCss\Scanner\Token $token
     */
    public function __construct(PhpCss\Scanner\Token $token) {
      parent::__construct(
        $token,
        sprintf(
          'Parse error: Unknown pseudo class "%s" at character "%d".',
          $token->content,
          $token->position
        )
      );
    }
  }
}
