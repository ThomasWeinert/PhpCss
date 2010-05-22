<?php
/**
* PhpCssScannerStatus is the abstract subperclass for all scanner status implementations
*
* @version $Id: Status.php 429 2010-03-29 08:05:32Z subjective $
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package PhpCss
* @subpackage Scanner
*/

/**
* PhpCssScannerStatus is the abstract subperclass for all scanner status implementations
*
* @package PhpCss
* @subpackage Scanner
*/
abstract class PhpCssScannerStatus {

  /**
  * Try to get token in buffer at offset position.
  *
  * @param string $buffer
  * @param integer $offset
  * @return PhpCssScannerToken
  */
  abstract public function getToken($buffer, $offset);

  /**
  * Check if token ends status
  *
  * @param PhpCssScannerToken $token
  * @return boolean
  */
  abstract public function isEndToken($token);

  /**
  * Get new (sub)status if needed.
  *
  * @param PhpCssScannerToken $token
  * @return PhpCssScannerStatus
  */
  abstract public function getNewStatus($token);

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
