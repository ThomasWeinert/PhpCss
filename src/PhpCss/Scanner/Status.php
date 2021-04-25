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
    abstract public function getToken(string $buffer, int $offset): ?Token;

    /**
    * Check if token ends status
    *
    * @param Token $token
    * @return bool
    */
    abstract public function isEndToken(Token $token): bool;

    /**
    * Get new (sub)status if needed.
    *
    * @param Token $token
    * @return Status|NULL
    */
    abstract public function getNewStatus(Token $token): ?Status;

    /**
    * Checks if the given offset position matches the pattern.
    *
    * @param string $buffer
    * @param integer $offset
    * @param string $pattern
    * @return string|NULL
    */
    protected function matchPattern(string $buffer, int $offset, string $pattern): ?string {
      $found = preg_match(
        $pattern, $buffer, $match, PREG_OFFSET_CAPTURE, $offset
      );
      if (
        $found &&
        isset($match[0][1]) &&
        $match[0][1] === $offset
      ) {
        return $match[0][0];
      }
      return NULL;
    }

    protected function matchPatterns(string $buffer, int $offset, array $patterns): ?Token {
      foreach ($patterns as $type => $pattern) {
        $tokenString = $this->matchPattern(
          $buffer, $offset, $pattern
        );
        if (!empty($tokenString)) {
          return new Token(
            $type, $tokenString, $offset
          );
        }
      }
      return NULL;
    }

    protected function matchCharacters(string $buffer, int $offset, array $chars): ?Token {
      if (isset($buffer[$offset])) {
        $char = $buffer[$offset];
        foreach ($chars as $type => $expectedChar) {
          if ($char === $expectedChar) {
            return new Token($type, $char, $offset);
          }
        }
      }
      return NULL;
    }
  }
}
