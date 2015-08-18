<?php
/**
* Abstract class implementing functionality to ease parsing in extending
* subparsers.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2014 PhpCss Team
*/

namespace PhpCss {
  /**
  * Abstract class implementing functionality to ease parsing in extending
  * subparsers.
  */
  abstract class Parser {

    /**
    * List of tokens from scanner
    *
    * @var array(Scanner\Token)
    */
    protected $_tokens = array();

    /**
     * Construct a parser object taking the token list to operate on as
     * argument
     */
    public function __construct(array &$tokens) {
      $this->_tokens = &$tokens;
    }

    /**
     * Execute the parsing process on the provided token stream
     *
     * This method is supposed to handle all the steps needed to parse the
     * current subsegment of the token stream. It is supposed to return a valid
     * PhpCssAst.
     *
     * If the parsing process can't be completed because of invalid input a
     * PhpCssParserException needs to be thrown.
     *
     * The methods protected methods read and lookahead should be used to
     * operate on the token stream. They will throw PhpCssParserExceptions
     * automatically in case they do not succeed.
     *
     * @return Ast
     */
    abstract public function parse();

    /**
     * Try to read any of the $expectedTokens from the token list and return
     * the matching one.
     *
     * This method tries to match the current token list against all of the
     * provided tokens. If a match is found it is removed from the token list
     * and returned.
     *
     * If no match can be found a PhpCssParserException will thrown indicating what
     * has been expected and what was found.
     *
     * The $expectedTokens parameter may be an array of tokens or a scalar
     * value, which is handled the same way an array with only one entry would
     * be.
     *
     * The special Token Scanner\Token::ANY may be used to indicate
     * everything is valid and may be matched. However if it is used no other
     * token may be specified, which does not make any sense, anyway.
     *
     * @param array|integer|string $expectedTokens
     * @throws Exception
     * @return Scanner\Token
     */
    protected function read($expectedTokens) {
      // Allow scalar token values for better readability
      if (!is_array($expectedTokens)) {
        return $this->read(array($expectedTokens));
      }

      foreach($expectedTokens as $token) {
        if ($this->matchToken(0, $token)) {
          return array_shift($this->_tokens);
        }
      }

      // None of the given tokens matched
      throw $this->handleMismatch($expectedTokens);
    }

    /**
     * Try to match any of the $expectedTokens against the given token stream
     * position and return the matching one.
     *
     * This method tries to match the current token stream at the provided
     * lookahead position against all of the provided tokens. If a match is
     * found it simply returned. The token stream remains unchanged.
     *
     * If no match can be found a PhpCssParserException will thrown indicating what
     * has been expected and what was found.
     *
     * The $expectedTokens parameter may be an array of tokens or a scalar
     * value, which is handled the same way an array with only one entry would
     * be.
     *
     * The special Token Scanner\Token::ANY may be used to indicate
     * everything is valid and may be matched. However if it is used no other
     * token may be specified, which does not make any sense, anyway.
     *
     * The position parameter may be provided to enforce a match on an
     * arbitrary token stream position. Therefore unlimited lookahead is
     * provided.
     *
     * @param array|integer|string $expectedTokens
     * @param int $position
     * @param bool $allowEndOfTokens
     * @throws Exception
     * @return Scanner\Token|NULL
     */
    protected function lookahead($expectedTokens, $position = 0, $allowEndOfTokens = FALSE) {
      // Allow scalar token values for better readability
      if (!is_array($expectedTokens)) {
        return $this->lookahead(array($expectedTokens), $position, $allowEndOfTokens);
      }

      // If the the requested characters is not available on the token stream
      // and this state is allowed return a special ANY token
      if ($allowEndOfTokens === TRUE && (!isset($this->_tokens[$position]))) {
        return new Scanner\Token(Scanner\Token::ANY, '', 0);
      }

      foreach($expectedTokens as $token) {
        if ($this->matchToken($position, $token)) {
          return $this->_tokens[$position];
        }
      }

      // None of the given tokens matched
      throw $this->handleMismatch($expectedTokens, $position);
    }

    /**
     * Validate if the of the token stream is reached. The position parameter
     * may be provided to look forward.
     *
     * @param int $position
     * @return boolean
     */
    protected function endOfTokens($position = 0) {
      return (count($this->_tokens) <= $position);
    }

    /**
     * Try to read any of the $expectedTokens from the token stream and remove them
     * from it.
     *
     * This method tries to match the current token list against all of the
     * provided tokens. Matching tokens are removed from the list until a non
     * matching token is found or the token list ends.
     *
     * The $expectedTokens parameter may be an array of tokens or a scalar
     * value, which is handled the same way an array with only one entry would
     * be.
     *
     * The special Token Scanner\Token::ANY is not valid here.
     *
     * The method return TRUE if tokens were removed, otherwise FALSE.
     *
     * @param array|integer|string $expectedTokens
     * @param boolean
     * @return bool
     */
    protected function ignore($expectedTokens) {
      // Allow scalar token values for better readability
      if (!is_array($expectedTokens)) {
        return $this->ignore(array($expectedTokens));
      }

      // increase position until the end of the token stream is reached or
      // a non matching token is found
      $position = 0;
      $found = FALSE;
      while (count($this->_tokens) > $position) {
        foreach ($expectedTokens as $token) {
          if ($found = $this->matchToken($position, $token)) {
            ++$position;
            continue;
          }
        }
        if ($found) {
          continue;
        } else {
          break;
        }
      }

      // remove the tokens from the stream
      if ($position > 0) {
        array_splice($this->_tokens, 0, $position);
        return TRUE;
      } else {
        return FALSE;
      }
    }

    /**
     * Delegate the parsing process to a subparser
     *
     * The result of the subparser is returned
     *
     * Only the name of the subparser is expected here, the method takes care
     * of providing the current token stream as well as instantiating the
     * subparser.
     *
     * @param string $parserClass
     * @return Ast
     */
    protected function delegate($parserClass) {
      $parser = new $parserClass($this->_tokens);
      /**
       * @var Parser $parser
       */
      return $parser->parse();
    }

    /**
     * Match a token on the token stream against a token type.
     *
     * Returns true if the token at the given position exists and the provided
     * token type matches type of the token at this position, false otherwise.
     *
     * @param int $position
     * @param int $type
     * @internal param Scanner\Token $token
     * @return bool
     */
    protected function matchToken($position, $type) {
      if (!isset($this->_tokens[$position])) {
        return false;
      }

      if ($type === Scanner\Token::ANY) {
        // A token has been found. We do not care which one it was
        return true;
      }

      return ($this->_tokens[$position]->type === $type);
    }

    /**
     * Handle the case if none of the expected tokens could be found.
     *
     * @param array() $expectedTokens
     * @param int $position
     * @return Exception
     */
    private function handleMismatch($expectedTokens, $position = 0) {
      // If the token stream ended unexpectedly throw an appropriate exception
      if (!isset($this->_tokens[$position])) {
        return new Exception\UnexpectedEndOfFile($expectedTokens);
      }

      // We found a token but none of the expected ones.
      return new Exception\TokenMismatch($this->_tokens[$position], $expectedTokens);
    }
  }
}
