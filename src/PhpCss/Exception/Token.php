<?php
/**
* Exception thrown if a parse error occurs
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2014 PhpCss Team
*/

namespace PhpCss\Exception {

  use PhpCss;

  /**
  * Exception thrown if a parse error occurs
  *
  * A parse error occurs if certain tokens are expected for further parsing, but
  * none of them are found on the token stream
  */
  class Token extends Parser {

    /**
    * The token encountered during the scan.
    *
    * This is the token object which was not expected to be found at the given
    * position.
    *
    * @var PhpCss\Scanner\Token
    */
    protected  $_encounteredToken;

    public function getToken() {
      return $this->_encounteredToken;
    }
  }
}