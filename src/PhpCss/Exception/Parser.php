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
  abstract class Parser extends \Exception implements PhpCss\Exception {

    /**
    * An array of tokens which would have been expected to be found.
    *
    * @var array(PhpCss\Scanner\Token)
    */
    protected $_expectedTokens = array();

    public function getExpected() {
      return $this->_expectedTokens;
    }
  }
}
