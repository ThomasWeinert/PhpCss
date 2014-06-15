<?php
/**
* Abstract superclass for all scanner status implementations
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2014 PhpCss Team
*/
namespace PhpCss\Scanner {

  /**
   * Abstract superclass for all scanner status implementations
   *
   * It defines the API and provides basic logic to match patterns.
   */
  abstract class Status {

    /**
    * Try to get token in buffer at offset position.
    *
    * @param string $buffer
    * @param integer $offset
    * @return Token
    */
    abstract public function getToken($buffer, $offset);

    /**
    * Check if token ends status
    *
    * @param Token $token
    * @return boolean
    */
    abstract public function isEndToken(Token $token);

    /**
    * Get new (sub)status if needed.
    *
    * @param Token $token
    * @return Status
    */
    abstract public function getNewStatus(Token $token);

    /**
    * Checks if the given offset position matches the pattern.
    *
    * @param string $buffer
    * @param integer $offset
    * @param string $pattern
    * @return string|NULL
    */
    public function matchPattern($buffer, $offset, $pattern) {
      $found = preg_match(
        $pattern, $buffer, $match, PREG_OFFSET_CAPTURE, $offset
      );
      if ($found &&
          isset($match[0]) &&
          isset($match[0][1]) &&
          $match[0][1] === $offset) {
        return $match[0][0];
      }
      return NULL;
    }
  }
}
